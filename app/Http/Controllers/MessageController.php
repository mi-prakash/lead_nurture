<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;
use App\Models\LeadCampaign;
use App\Models\Campaign;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use App\Models\Rule;
use App\Models\CustomField;
use App\Models\MessageQueue;
use App\Models\MessageRuleCategory;
use App\Models\RuleExpression;
use App\Models\TimezoneSetting;
use App\Models\CampaignMessage;
use App\Models\CampaignTrigger;
use App\Models\UserTimeSetting;
use Twilio\Rest\Client;


class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['pre_event', 'get_sms']]);
    }

    public function index($lead_id)
    {
        if (!$lead_id) {
            return false;
        }
        $user_id = Auth::id();
        $latest_leads = DB::select("SELECT lead_id, MAX(created_at) AS max_created_at FROM messages WHERE user_id = ".$user_id." GROUP BY lead_id ORDER BY max_created_at DESC");
        $latest_leads_id = array();
        $latest_leads_data = array();
        $x = 0;
        foreach ($latest_leads as $latest_lead) {
            array_push($latest_leads_id, $latest_lead->lead_id);
            $latest_leads_data[$x]['lead_id'] = $latest_lead->lead_id;
            $details = Message::with('lead')->where('lead_id', $latest_lead->lead_id)->where('user_id', $user_id)->orderBy('created_at', 'desc')->first()->toArray();
            $latest_leads_data[$x]['details'] = $details;
            $x++;
        }

        $other_leads = Lead::whereNotIn('id', $latest_leads_id)->where('user_id', $user_id)->get();

        $last_lead_messages = Message::with('lead')->where('lead_id', $lead_id)->where('user_id', $user_id)->orderBy('created_at', 'asc')->get();

        $lead = Lead::where('id', $lead_id)->where('user_id', $user_id)->first();
        $name = $lead->first_name." ".$lead->last_name;
        $phone = $lead->phone;

        return view('messages/index', compact('lead_id', 'name', 'phone', 'latest_leads_data', 'other_leads', 'last_lead_messages'));
    }

    public function pre_event(Request $request)
    {
        $params = $request->all();
        $json_data = json_encode($params);
        $myfile = fopen(public_path("twilio.txt"), "a") or die("Unable to open file!");
        $txt = $json_data."\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    public function get_sms(Request $request, $identifier)
    {
        $params = $request->all();

        $user = User::where('identifier', $identifier)->first();
        if (!$user) {
            return;
        }

        $phone = $params['From'];
        $message = $params['Body'];
        $json_data = json_encode($params);

        $lead = Lead::where('phone', $phone)->first();

        $appointment = Appointment::where('lead_id', $lead->id)
                                    ->where(function($q) {
                                        $q->where('status', 'scheduled')
                                        ->orWhere('status', 'rescheduled')
                                        ->orWhere('status', 'changed')
                                        ->orWhere('status', '');
                                    })
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        if (!empty($appointment)) {
            // Timezone time difference calculate
            $appointment = $appointment->toArray();
            $time_diff = app('App\Http\Controllers\HomeController')->time_difference($appointment['calendar_timezone'], $appointment['timezone']);
            $converted_date_time_created = date('Y-m-d H:i:s', strtotime($appointment['date_time_created']) + $time_diff);
            $appointment_date = date('Y-m-d', strtotime($appointment['date_time']));
            $appointment_time = date('H:i', strtotime($appointment['time']));
        } else {
            $appointment_date = "'not set'";
            $appointment_time = "'not set'";
        }

        if ($lead) {
            $add_data = array(
                            'lead_id' => $lead->id,
                            'user_id' => $user->id,
                            'message' => $message,
                            'is_incoming' => 1,
                            'status' => 1,
                            'json_data' => $json_data
                        );
            $add = Message::create($add_data);
        }
        // print_r($add_data);
        $last_msg = Message::where('lead_id', $lead->id)->where('user_id', $user->id)->where('campaign_message_id', '!=', NULL)->orderBy('created_at', 'desc')->first();
        // print_r($last_msg->toArray());

        $rules = Rule::where('message_id', $last_msg->campaign_message_id)->get();
        // print_r($rules->toArray());

        $campaign_message = CampaignMessage::where('id', $last_msg->campaign_message_id)->first();
        if (!empty($campaign_message)) {
            $last_msg_campaign_id = $campaign_message->campaign_id;
            // print_r($campaign_message->toArray());
            $check_campaign_id_active = LeadCampaign::where('lead_id', $lead->id)->where('campaign_id', $last_msg_campaign_id)->orderBy('created_at', 'desc')->first();
            if (!empty($check_campaign_id_active)) {
                if ($check_campaign_id_active->status == 'inactive') {
                    echo "failed! campaign is inactive";
                    die();
                }
            } else {
                echo "failed! no campaign found";
                die();
            }
        } else {
            echo "failed! no campaign message found";
            die();
        }

        // get custom fields
        $custom_fields_with_user = DB::select("SELECT custom_fields.*,
                                        user_custom_fields.value AS user_value,
                                        user_custom_fields.data_type AS user_data_type
                                    FROM custom_fields
                                    LEFT JOIN user_custom_fields ON user_custom_fields.custom_field_id = custom_fields.id
                                    WHERE (user_custom_fields.user_id = $user->id OR 
                                        user_custom_fields.user_id IS NULL) 
                                    ORDER BY custom_fields.id");

        foreach ($rules as $rule) {
            $msg_body = $rule->instant_reply;
            preg_match_all("/\\[(.*?)\\]/", $msg_body, $matches);
            foreach ($matches[1] as $value) {
                foreach ($custom_fields_with_user as $custom_field) {
                    if ($custom_field->name == $value) {
                        if ($custom_field->name == 'customer_first_name') {
                            $custom_field_data = $lead->first_name;
                        } else if ($custom_field->name == 'appointment_date') {
                            $custom_field_data = $appointment_date;
                        } else if ($custom_field->name == 'appointment_time') {
                            $custom_field_data = $appointment_time;
                        } else {
                            if (!empty($custom_field->user_value)) {
                                $custom_field_data = $custom_field->user_value;
                            } else {
                                $custom_field_data = $custom_field->value;
                            }
                        }
                        $msg_body = str_replace("[".$value."]", $custom_field_data, $msg_body);
                    }
                }
            }
            // echo $msg_body."\n";
            // continue;
            $send_timer = date('Y-m-d H:i');
            $add_message_queue = false;
            $message_queue_data = array(
                                    'lead_id' => $lead->id,
                                    'user_id' => $user->id,
                                    'campaign_message_id' => $last_msg->campaign_message_id,
                                    'message' => $msg_body,
                                    'send_timer' => $send_timer,
                                    'is_reminder' => 0,
                                    'wait' => NULL,
                                    'status' => 'pending'
                                );

            if ($rule->execute_when == 'reply_any_response') {
                if ($msg_body != "") {
                    $add_message_queue = MessageQueue::create($message_queue_data);
                }
                $campaign_message = CampaignMessage::where('id', $rule->message_id)->first();
                $this->add_new_campaign($rule->removed, $lead->id, $rule->add_to_campaign, $campaign_message->campaign_id);
            } elseif ($rule->execute_when == 'reply_with_expression') {
                $expression = $rule->expression_value;
                if(preg_match("~\b".$expression."\b~i",$message)){
                    if ($msg_body != "") {
                        $add_message_queue = MessageQueue::create($message_queue_data);
                    }
                    $campaign_message = CampaignMessage::where('id', $rule->message_id)->first();
                    $this->add_new_campaign($rule->removed, $lead->id, $rule->add_to_campaign, $campaign_message->campaign_id);
                } else {
                    continue;
                }
            } elseif ($rule->execute_when == 'reply_with_category') {
                $rule_category = $rule->category;
                $rule_expressions = RuleExpression::where('message_rule_category_id', $rule_category)->get();
                foreach ($rule_expressions as $rule_expression) {
                    if(preg_match("~\b".$rule_expression->name."\b~i",$message)) {
                        if ($msg_body != "") {
                            $add_message_queue = MessageQueue::create($message_queue_data);
                        }
                        $campaign_message = CampaignMessage::where('id', $rule->message_id)->first();
                        $this->add_new_campaign($rule->removed, $lead->id, $rule->add_to_campaign, $campaign_message->campaign_id);
                        break;
                    } else {
                        continue;
                    }
                }
            }
            if ($add_message_queue) {
                break;
            }
        }
        echo "success";
    }

    public function add_new_campaign($removed, $lead_id, $new_campaign_id, $old_campaign_id)
    {
        if ($removed != 0) { // remove
            $remove_campaign = LeadCampaign::where('lead_id', $lead_id)->where('campaign_id', $old_campaign_id)->update(['status' => 'inactive']);
            $message_queues = DB::select("SELECT 
                                            message_queues.*,
                                            campaign_messages.campaign_id
                                        FROM message_queues
                                        JOIN campaign_messages ON message_queues.campaign_message_id = campaign_messages.id
                                        WHERE message_queues.lead_id = $lead_id
                                        AND message_queues.wait IS NOT NULL
                                        AND message_queues.status != 'sent'
                                        AND message_queues.status != 'failed'
                                        AND campaign_messages.campaign_id = $old_campaign_id");
            foreach ($message_queues as $message_queue) {
                $change_msg_status = MessageQueue::where('id', $message_queue->id)->update(['status' => 'canceled']);
            }
        }
        if (!empty($new_campaign_id)) { // if set add_to_campaign_id
            if ($removed != 0) {
                $campaign_trigger = CampaignTrigger::where('campaign_id', $new_campaign_id)->first();
                if (!empty($campaign_trigger)) {
                    $type = $campaign_trigger->type;
                    if ($type == 'cancel') {
                        $remove_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);       
                    }
                }
            }
            
            $inactive_if_prev_active = LeadCampaign::where('lead_id', $lead_id)
                                                ->where('campaign_id', $new_campaign_id)
                                                ->where('status', 'active')
                                                ->update(['status' => 'inactive']);

            $add_campaign = array(
                            'lead_id' => $lead_id,
                            'campaign_id' => $new_campaign_id,
                            'status' => 'active',
                        );
            $add_lead_campaign = LeadCampaign::create($add_campaign);

            $new_campaign = Campaign::where('id', $new_campaign_id)->first();

            $lead = Lead::where('id', $lead_id)->first();

            $appointment = Appointment::where('lead_id', $lead->id)
                                        ->where(function($q) {
                                            $q->where('status', 'scheduled')
                                            ->orWhere('status', 'rescheduled')
                                            ->orWhere('status', 'changed')
                                            ->orWhere('status', '');
                                        })
                                        ->first();
            if (!empty($appointment)) {
                // Timezone time difference calculate
                $appointment = $appointment->toArray();
                $time_diff = app('App\Http\Controllers\HomeController')->time_difference($appointment['calendar_timezone'], $appointment['timezone']);
                $converted_date_time_created = date('Y-m-d H:i:s', strtotime($appointment['date_time_created']) + $time_diff);
                $appointment_date = date('Y-m-d', strtotime($appointment['date_time']));
                $appointment_time = date('H:i', strtotime($appointment['time']));
                $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment['date_time']) + $time_diff);
            } else {
                $appointment_date = "'not set'";
                $appointment_time = "'not set'";
            }

            $lead_id = $lead->id;
            $lead_name = $lead->first_name;
            $appointment_datetime = date('Y-m-d H:i:s', strtotime(now()));
            $user_id = $lead->user_id;
            $last_msg_time = "";

            $user_time_setting = UserTimeSetting::where('user_id', $user_id)->first();

            // get messages
            $get_campaign_messages = CampaignMessage::with('rules')->where('campaign_id', $new_campaign_id)->get();

            // get custom fields
            $custom_fields_with_user = DB::select("SELECT custom_fields.*,
                                            user_custom_fields.value AS user_value,
                                            user_custom_fields.data_type AS user_data_type
                                        FROM custom_fields
                                        LEFT JOIN user_custom_fields ON user_custom_fields.custom_field_id = custom_fields.id
                                        WHERE (user_custom_fields.user_id = $user_id OR 
                                            user_custom_fields.user_id IS NULL) 
                                        ORDER BY custom_fields.id");

            foreach ($get_campaign_messages as $campaign_message) {
                // add message into queue according to lead_id and user_id
                $msg_body = $campaign_message->body;
                preg_match_all("/\\[(.*?)\\]/", $msg_body, $matches);
                
                foreach ($matches[1] as $value) {
                    foreach ($custom_fields_with_user as $custom_field) {
                        if ($custom_field->name == $value) {
                            if ($custom_field->name == 'customer_first_name') {
                                $custom_field_data = $lead_name;
                            } else if ($custom_field->name == 'appointment_date') {
                                $custom_field_data = $appointment_date;
                            } else if ($custom_field->name == 'appointment_time') {
                                $custom_field_data = $appointment_time;
                            } else {
                                if (!empty($custom_field->user_value)) {
                                    $custom_field_data = $custom_field->user_value;
                                } else {
                                    $custom_field_data = $custom_field->value;
                                }
                            }
                            $msg_body = str_replace("[".$value."]", $custom_field_data, $msg_body);
                        }
                    }
                }
                $send_timer = "";
                if ($new_campaign->is_reminder == 0) {
                    if ($campaign_message->wait == 'day') {
                        if ($last_msg_time != "") {
                            $dateTime = new \DateTime($last_msg_time);
                            $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                            $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                            $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                        } else {
                            $dateTime = new \DateTime($appointment_datetime);
                            $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                            $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                            $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                        }
                    } else {
                        if ($last_msg_time != "") {
                            $dateTime = new \DateTime($last_msg_time);
                            $send_timer = $dateTime->modify('+'.$campaign_message->time.' minutes');
                            $send_timer = $dateTime->format('Y-m-d H:i');
                        } else {
                            $dateTime = new \DateTime($appointment_datetime);
                            $send_timer = $dateTime->modify('+'.$campaign_message->time.' minutes');
                            $send_timer = $dateTime->format('Y-m-d H:i');
                        }
                    }
                } else {
                    if ($campaign_message->wait == 'day') {
                        if ($last_msg_time != "") {
                            $dateTime = new \DateTime($last_msg_time);
                            $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                            $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                            $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                        } else {
                            $dateTime = new \DateTime($appointment_datetime);
                            $send_timer = $dateTime->modify('-'.$new_campaign->before_hours.' hours');
                            $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                            $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                            $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                        }
                    } else {
                        if ($last_msg_time != "") {
                            $dateTime = new \DateTime($last_msg_time);
                            $send_timer = $dateTime->modify('+'.$campaign_message->time.' minutes');
                            $send_timer = $dateTime->format('Y-m-d H:i');
                        } else {
                            $dateTime = new \DateTime($appointment_datetime);
                            $send_timer = $dateTime->modify('-'.$new_campaign->before_hours.' hours');
                            $send_timer = $dateTime->modify('+'.$campaign_message->time.' minutes');
                            $send_timer = $dateTime->format('Y-m-d H:i');
                        }
                    }
                }

                $send_time = date('H:i:00', strtotime($send_timer));

                if (strtotime($send_time) >= strtotime($user_time_setting->from_time) || strtotime($send_time) <= strtotime($user_time_setting->to_time)) {
                    // new time changed and last timer save
                    if (strtotime($send_time) > strtotime($user_time_setting->to_time)) {
                        $dateTime = new \DateTime($send_timer);
                        $new_datetime = $dateTime->modify('+1 days');
                        $new_datetime = $dateTime->format('Y-m-d '.$user_time_setting->to_time);
                        $last_msg_time = $new_datetime;
                    } else {
                        $dateTime = new \DateTime($send_timer);
                        $new_datetime = $dateTime->format('Y-m-d '.$user_time_setting->to_time);
                        $last_msg_time = $new_datetime;
                    }
                } else {
                    $last_msg_time = $send_timer;
                }

                $message_queue_data = array(
                                        'lead_id' => $lead_id,
                                        'user_id' => $user_id,
                                        'campaign_message_id' => $campaign_message->id,
                                        'message' => $msg_body,
                                        'send_timer' => $last_msg_time,
                                        'is_reminder' => $new_campaign->is_reminder,
                                        'wait' => $campaign_message->wait,
                                        'status' => 'pending'
                                    );

                if (empty($appointment)) {
                    $add_message_queue = MessageQueue::create($message_queue_data);
                } else {
                    if (strtotime($last_msg_time) >= strtotime($converted_date_time_created) && strtotime($last_msg_time) <= strtotime($converted_date_time)) {
                        $add_message_queue = MessageQueue::create($message_queue_data);
                    }
                }
            }
        }
    }

    public function send_message(Request $request)
    {
        $params = $request->all();

        $user_id = Auth::id();
        $user = User::where('id', $user_id)->first();
        $user_settings = Setting::where('user_id', $user_id)->first();
        if (!$user_settings) {
            abort(404);
        }

        $lead = Lead::where('id', $params['lead_id'])->first();
        $user_phone = $lead->phone;
        $text = $params['message'];

        // Your Account SID and Auth Token from twilio.com/console
        $account_sid = $user_settings->twilio_account_sid;
        $auth_token = $user_settings->twilio_auth_token;

        // A Twilio number you own with SMS capabilities
        $twilio_number = $user_settings->twilio_number;

        $client = new Client($account_sid, $auth_token);
        $message = $client->messages->create(
            // Where to send a text message (your cell phone?)
            $user_phone,
            array(
                'from' => $twilio_number,
                'body' => $text
            )
        );
        if ($message->sid) {
            $add_data = array(
                            'lead_id' => $params['lead_id'],
                            'user_id' => $user_id,
                            'message' => $text,
                            'is_incoming' => 0,
                            'status' => 1,
                            'json_data' => json_encode($message)
                        );
            $add = Message::create($add_data);
        }
        return redirect('/home/messages/'.$params['lead_id']);
    }
}
