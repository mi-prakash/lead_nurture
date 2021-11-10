<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\CampaignTrigger;
use App\Models\CampaignMessage;
use App\Models\CustomField;
use App\Models\Lead;
use App\Models\LeadCampaign;
use App\Models\Message;
use App\Models\MessageQueue;
use App\Models\Setting;
use App\Models\TimezoneSetting;
use App\Models\User;
use App\Models\UserCampaign;
use App\Models\UserTimeSetting;
use Twilio\Rest\Client;
use AcuityScheduling;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['webhook_new_appointment', 'webhook_rescheduled', 'webhook_canceled', 'webhook_changed', 'webhook_complete', 'clickfunnel_webhook', 'acuity_fetch_calendar', 'clickfunnel_fetch_funnel', 'artisan_add_appointments', 'artisan_send_queue_messages', 'testTime']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_id = Auth::id();
        $leads = Lead::where('user_id', $user_id)->get();

        return view('home', compact('leads'));
    }

    public function getNoSchedule()
    {
        $campaign_type = "schedule";
        $lead_id = 6;
        $lead_name = "Felicia";
        $appointment_date = "2021-09-14 06:00";
        $appointment_date = date('Y-m-d', strtotime($appointment_date));
        $appointment_datetime = "2021-09-14 09:00";
        $appointment_time = date('H:i', strtotime("2021-09-14 09:00"));
        $user_id = 1;
        $last_msg_time = "";

        $user_campaigns_with_campaign_type = UserCampaign::select('*')
                                            ->join('campaign_triggers', 'user_campaigns.campaign_tree_id', '=', 'campaign_triggers.campaign_tree_id')
                                            ->join('campaigns', 'campaign_triggers.campaign_id', '=', 'campaigns.id')
                                            ->where('campaign_triggers.type', $campaign_type)
                                            ->where('user_campaigns.user_id', $user_id)
                                            ->where('user_campaigns.status', 'active')
                                            ->get();

        echo "user_campaigns_with_campaign_type<pre>";
        print_r($user_campaigns_with_campaign_type->toArray());
        echo "</pre>";
        // exit;
        foreach ($user_campaigns_with_campaign_type as $user_campaing) {
            echo "campaign_id ".$user_campaing->campaign_id."<br>";
            echo "is_reminder ".$user_campaing->is_reminder."<br><br>";
            // insert lead campaign id here
            $add_data = array(
                            'lead_id' => $lead_id,
                            'campaign_id' => $user_campaing->campaign_id,
                            'status' => 'active'
                        );
            // $add_lead_campaign = LeadCampaign::create($add_data);
            // get messages
            $get_campaign_messages = CampaignMessage::with('rules')->where('campaign_id', $user_campaing->campaign_id)->get();

            echo "get_campaign_messages<pre>";
            print_r($get_campaign_messages->toArray());
            echo "</pre>";

            // get custom fields
            $custom_fields_with_user = DB::select("SELECT custom_fields.*,
                                            user_custom_fields.value AS user_value,
                                            user_custom_fields.data_type AS user_data_type
                                        FROM custom_fields
                                        LEFT JOIN user_custom_fields ON user_custom_fields.custom_field_id = custom_fields.id
                                        WHERE (user_custom_fields.user_id = $user_id OR 
                                            user_custom_fields.user_id IS NULL) 
                                        ORDER BY custom_fields.id");
            // echo "<br>custom_fields<pre>";
            // print_r($custom_fields_with_user);
            // echo "</pre>";

            foreach ($get_campaign_messages as $campaign_message) {
                // add message into queue according to lead_id and user_id
                // echo $campaign_message->body."<br>";
                $msg_body = $campaign_message->body;
                preg_match_all("/\\[(.*?)\\]/", $msg_body, $matches); 
                
                // print_r($matches[1]);
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
                            // echo $custom_field_data."<br><br>";
                            $msg_body = str_replace("[".$value."]", $custom_field_data, $msg_body);
                        }
                    }
                }
                // echo $msg_body."<br>";
                $send_timer = "";
                if ($user_campaing->is_reminder == 0) {
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
                            $send_timer = $dateTime->modify('-'.$campaign_message->days.' days');
                            $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                            $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                        } else {
                            $dateTime = new \DateTime($appointment_datetime);
                            $send_timer = $dateTime->modify('-'.$campaign_message->days.' days');
                            $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                            $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                        }  
                    } else {
                        if ($last_msg_time != "") {
                            $dateTime = new \DateTime($last_msg_time);
                            $send_timer = $dateTime->modify('-'.$campaign_message->time.' minutes');
                            $send_timer = $dateTime->format('Y-m-d H:i');
                        } else {
                            $dateTime = new \DateTime($appointment_datetime);
                            $send_timer = $dateTime->modify('-'.$campaign_message->time.' minutes');
                            $send_timer = $dateTime->format('Y-m-d H:i');
                        }
                    }
                }
                $last_msg_time = $send_timer;

                $message_queue_data = array(
                                        'lead_id' => $lead_id,
                                        'user_id' => $user_id,
                                        'campaign_message_id' => $campaign_message->id,
                                        'message' => $msg_body,
                                        'send_timer' => $send_timer,
                                        'is_reminder' => $user_campaing->is_reminder,
                                        'wait' => $campaign_message->wait,
                                        'status' => 'pending'
                                    );
                    
                echo "<pre>";
                print_r($message_queue_data);
                echo "</pre>";

                foreach ($campaign_message->rules as $rule) {
                    if ($rule->execute_when == 'is_sent') {
                        $rule_msg = $rule->instant_reply;
                        preg_match_all("/\\[(.*?)\\]/", $rule_msg, $matches);
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
                                    $rule_msg = str_replace("[".$value."]", $custom_field_data, $rule_msg);
                                }
                            }
                        }
                        if ($rule_msg != "") {
                            $message_queue_data = array(
                                                    'lead_id' => $lead_id,
                                                    'user_id' => $user_id,
                                                    'campaign_message_id' => $rule->message_id,
                                                    'message' => $rule_msg,
                                                    'send_timer' => $send_timer,
                                                    'is_reminder' => 0,
                                                    'wait' => NULL,
                                                    'status' => 'pending'
                                                );
                            // $add_message_queue = MessageQueue::create($message_queue_data);
                            
                            $this->add_new_campaign($rule->removed, $lead_id, $rule->add_to_campaign, $campaign_message->campaign_id);
                        }
                    }
                }
                // $add_message_queue = MessageQueue::create($message_queue_data);
            }
        }
    }
    
    public function webhook_new_appointment(Request $request, $identifier)
    {
        $params = $request->all();

        // storing log text
        $myfile = fopen(public_path("acuity.txt"), "a") or die("Unable to open file!");
        $txt = json_encode($params);
        fwrite($myfile, $txt."\n");
        fclose($myfile);
        
        $user = User::where('identifier', $identifier)->first();
        if (!$user) {
            return;
        }
        $user_settings = Setting::where('user_id', $user->id)->first();
        if (!$user_settings) {
            return;
        }

        $userId = $user_settings->acuity_user_id;
        $apiKey = $user_settings->acuity_api_key;

        if ($params) {
            // Handle webhook after verifying signature:
            try {
                /*AcuityScheduling::verifyMessageSignature($apiKey);
                error_log("The message is authentic:\n".json_encode($params, JSON_PRETTY_PRINT));*/

                $appointment_id = $params['id'];

                /*$acuity = new AcuityScheduling(array(
                  'userId' => $userId,
                  'apiKey' => $apiKey
                ));

                $appointment_by_id = $acuity->request('/appointments/'.$appointment_id);
                $appointment_json = json_encode($appointment_by_id);*/

                // dummy data
                /*$appointment_json = '{"id":655491364,"firstName":"Henry","lastName":"Cook","phone":"+8801671340328","email":"mytew@mailinator.com","date":"October 13, 2021","time":"09:00","endTime":"09:30","dateCreated":"October 11, 2021","datetimeCreated":"2021-10-11T23:52:54-0500","datetime":"2021-10-13T09:00:00-0400","price":"0.00","priceSold":"0.00","paid":"no","amountPaid":"0.00","type":"ALAN","appointmentTypeID":12976640,"classID":null,"addonIDs":[],"category":"","duration":"30","calendar":"TBD SAYEED 01","calendarID":4887542,"certificate":null,"confirmationPage":"https://app.acuityscheduling.com/schedule.php?owner=18141694&action=appt&id%5B%5D=e3e610f25ffeaa3e3ae6ceec6d812dc1","location":"","notes":"","timezone":"Asia/Dhaka","calendarTimezone":"America/New_York","canceled":false,"canClientCancel":true,"canClientReschedule":true,"labels":null,"forms":[],"formsText":"Name: Henry Cook\nPhone: +8801671340328\nEmail: mytew@mailinator.com\n","isVerified":false,"scheduledBy":null}';*/


                // Convert time
                $date = date('F d, Y', strtotime($params['appointment_datetime']));
                $time = date('H:i', strtotime($params['appointment_datetime']));
                $end_time = date('H:i', strtotime($params['appointment_datetime']) + 1800);
                $date_created = date('F d, Y', strtotime(now()));
                $date_time = date('Y-m-d H:i:s', strtotime($params['appointment_datetime']));
                $date_time_created = date('Y-m-d H:i:s', strtotime(now()));
                $timezone = $params['timezone'];


                $appointment_json = '{
                    "id": '.$params['id'].',
                    "firstName": "'.$params['first_name'].'",
                    "lastName": "'.$params['last_name'].'",
                    "phone": "'.$params['phone'].'",
                    "email": "mytew@mailinator.com",
                    "date": "'.$date.'",
                    "time": "'.$time.'",
                    "endTime": "'.$end_time.'",
                    "dateCreated": "'.$date_created.'",
                    "datetimeCreated": "'.$date_time_created.'",
                    "datetime": "'.$date_time.'",
                    "price": "0.00",
                    "priceSold": "0.00",
                    "paid": "no",
                    "amountPaid": "0.00",
                    "type": "ALAN",
                    "appointmentTypeID": 12976640,
                    "classID": null,
                    "addonIDs": [],
                    "category": "",
                    "duration": "30",
                    "calendar": "TBD SAYEED 01",
                    "calendarID": 4887542,
                    "certificate": null,
                    "confirmationPage": "https://app.acuityscheduling.com/schedule.php?owner=18141694&action=appt&id%5B%5D=e3e610f25ffeaa3e3ae6ceec6d812dc1",
                    "location": "",
                    "notes": "",
                    "timezone": "Asia/Dhaka",
                    "calendarTimezone": "'.$timezone.'",
                    "canceled": false,
                    "canClientCancel": true,
                    "canClientReschedule": true,
                    "labels": null,
                    "forms": [],
                    "formsText": "Name: Henry Cook\nPhone: +8801671340328\nEmail: mytew@mailinator.com\n",
                    "isVerified": false,
                    "scheduledBy": null
                }';

                $appointment_by_id = json_decode($appointment_json, true);

                // print_r($appointment_by_id);exit();

                if (!isset($appointment_by_id['phone'])) {
                    echo "error, phone is empty";
                    die();
                }
                $lead = lead::where('phone', $appointment_by_id['phone'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

                $lead_insert_id = NULL;
                if (!$lead) {
                    $lead_data = array(
                                    'user_id' => $user->id,
                                    'first_name' => $appointment_by_id['firstName'],
                                    'last_name' => $appointment_by_id['lastName'],
                                    'phone' => $appointment_by_id['phone'],
                                    'email' => $appointment_by_id['email'],
                                    'is_appointment' => !empty($appointment_by_id['date']) ? 1 : 0,
                                    'created_by' => 'acuity',
                                    'json_data' => $appointment_json
                                );

                    $lead_insert = Lead::create($lead_data);

                    if ($lead_insert) {
                        $lead_insert_id = $lead_insert->id;
                    }

                    if (!empty($appointment_by_id['date'])) {
                        // Timezone time difference calculate
                        $time_diff = $this->time_difference($appointment_by_id['calendarTimezone'], $appointment_by_id['timezone']);
                        $converted_time = date('H:i', strtotime($appointment_by_id['time']) + $time_diff);
                        $converted_end_time = date('H:i', strtotime($appointment_by_id['endTime']) + $time_diff);
                        $date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetime']));
                        $date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetimeCreated']));
                        $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetime']) + $time_diff);
                        $converted_date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetimeCreated']) + $time_diff);

                        $appointment_data = array(
                                                'appointment_id' => $appointment_by_id['id'],
                                                'user_id' => $user->id,
                                                'lead_id' => $lead_insert_id,
                                                'date' => $appointment_by_id['date'],
                                                'date_time' => $date_time,
                                                'time' => $appointment_by_id['time'],
                                                'end_time' => $appointment_by_id['endTime'],
                                                'converted_time' => $converted_time,
                                                'converted_end_time' => $converted_end_time,
                                                'date_created' => $appointment_by_id['dateCreated'],
                                                'date_time_created' => $date_time_created,
                                                'price' => $appointment_by_id['price'],
                                                'price_sold' => $appointment_by_id['priceSold'],
                                                'paid' => $appointment_by_id['paid'],
                                                'amount_paid' => $appointment_by_id['amountPaid'],
                                                'type' => $appointment_by_id['type'],
                                                'appointment_type_id' => $appointment_by_id['appointmentTypeID'],
                                                'calendar_id' => $appointment_by_id['calendarID'],
                                                'timezone' => $appointment_by_id['timezone'],
                                                'calendar_timezone' => $appointment_by_id['calendarTimezone'],
                                                'canceled' => (empty($appointment_by_id['canceled'])) ? 0 : $appointment_by_id['canceled'],
                                                'can_client_cancel' => $appointment_by_id['canClientCancel'],
                                                'can_client_reschedule' => $appointment_by_id['canClientReschedule'],
                                                'status' => 'scheduled',
                                                'json_data' => $appointment_json
                                            );
                        // Add to log
                        $log_data = array(
                                        'appointment_id' => $appointment_by_id['id'],
                                        'json_data' => json_encode($appointment_data),
                                        'status' => 'scheduled'
                                    );
                        $add_log = AppointmentLog::create($log_data);
                    }
                } else {
                    // If no appointment data is found, it'll update the status of the lead
                    if ($lead->is_appointment == 0) {
                        $lead_data = array(
                                        'first_name' => $appointment_by_id['firstName'],
                                        'last_name' => $appointment_by_id['lastName'],
                                        'email' => $appointment_by_id['email'],
                                        'is_appointment' => 1,
                                        'json_data' => $appointment_json
                                    );
                        $update_lead = Lead::where('id', $lead->id)->update($lead_data);
                    } else {
                        $lead_data = array(
                                        'first_name' => $appointment_by_id['firstName'],
                                        'last_name' => $appointment_by_id['lastName'],
                                        'email' => $appointment_by_id['email'],
                                        'json_data' => $appointment_json
                                    );
                        $update_lead = Lead::where('id', $lead->id)->update($lead_data);
                    }
                    // Check if other appointments present with Scheduled, rescheduled and changed
                    $open_appointments = Appointment::with('lead')
                                                    ->where('lead_id', $lead->id)
                                                    ->where(function($q) {
                                                        $q->where('status', 'scheduled')
                                                        ->orWhere('status', 'rescheduled')
                                                        ->orWhere('status', 'changed')
                                                        ->orWhere('status', '');
                                                    })
                                                    ->whereNotIn('appointment_id', [$appointment_by_id['id']])
                                                    ->get();
                    // update the open appointments status to canceled
                    foreach ($open_appointments as $open_appointment) {
                        // Update appointment log 
                        $log_data = array(
                                        'appointment_id' => $open_appointment->appointment_id,
                                        'json_data' => json_encode($open_appointment->toArray()),
                                        'status' => 'canceled',
                                        'is_adjusted' => 1
                                    );
                        $add_log = AppointmentLog::create($log_data);

                        // Update appointment status
                        $update_appointment = Appointment::where('appointment_id', $open_appointment->appointment_id)->update(['status' => 'canceled']);
                    }

                    // Timezone time difference calculate
                    $time_diff = $this->time_difference($appointment_by_id['calendarTimezone'], $appointment_by_id['timezone']);
                    $converted_time = date('H:i', strtotime($appointment_by_id['time']) + $time_diff);
                    $converted_end_time = date('H:i', strtotime($appointment_by_id['endTime']) + $time_diff);
                    $date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetime']));
                    $date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetimeCreated']));
                    $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetime']) + $time_diff);
                    $converted_date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetimeCreated']) + $time_diff);

                    $appointment_data = array(
                                            'appointment_id' => $appointment_by_id['id'],
                                            'user_id' => $user->id,
                                            'lead_id' => $lead->id,
                                            'date' => $appointment_by_id['date'],
                                            'date_time' => $date_time,
                                            'time' => $appointment_by_id['time'],
                                            'end_time' => $appointment_by_id['endTime'],
                                            'converted_time' => $converted_time,
                                            'converted_end_time' => $converted_end_time,
                                            'date_created' => $appointment_by_id['dateCreated'],
                                            'date_time_created' => $date_time_created,
                                            'price' => $appointment_by_id['price'],
                                            'price_sold' => $appointment_by_id['priceSold'],
                                            'paid' => $appointment_by_id['paid'],
                                            'amount_paid' => $appointment_by_id['amountPaid'],
                                            'type' => $appointment_by_id['type'],
                                            'appointment_type_id' => $appointment_by_id['appointmentTypeID'],
                                            'calendar_id' => $appointment_by_id['calendarID'],
                                            'timezone' => $appointment_by_id['timezone'],
                                            'calendar_timezone' => $appointment_by_id['calendarTimezone'],
                                            'canceled' => (empty($appointment_by_id['canceled'])) ? 0 : $appointment_by_id['canceled'],
                                            'can_client_cancel' => $appointment_by_id['canClientCancel'],
                                            'can_client_reschedule' => $appointment_by_id['canClientReschedule'],
                                            'status' => 'scheduled',
                                            'json_data' => $appointment_json
                                        );
                    // Add to log
                    $log_data = array(
                                    'appointment_id' => $appointment_by_id['id'],
                                    'json_data' => json_encode($appointment_data),
                                    'status' => 'scheduled'
                                );
                    $add_log = AppointmentLog::create($log_data);
                }

                $campaign_type = "schedule";
                if (!$lead) {
                    $lead_id = $lead_insert->id;
                    $lead_name = $lead_insert->first_name;
                    $appointment_date = date('Y-m-d', strtotime($converted_date_time));
                    $appointment_datetime = $converted_date_time;
                    $appointment_time = date('H:i', strtotime($appointment_by_id['time']));
                    $user_id = $user->id;
                } else {
                    $lead_id = $lead->id;
                    $lead_name = $lead->first_name;
                    $appointment_date = date('Y-m-d', strtotime($converted_date_time));
                    $appointment_datetime = $converted_date_time;
                    $appointment_time = date('H:i', strtotime($appointment_by_id['time']));
                    $user_id = $user->id;

                    // set previous lead campaigns status
                    $update_lead_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);

                    // set previous lead message queues
                    $update_message_queue = MessageQueue::where('lead_id', $lead_id)->update(['status' => 'canceled']);
                }

                $msg_date_time_created = $converted_date_time_created;

                $this->campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $appointment_date, $appointment_datetime, $appointment_time, $msg_date_time_created, $converted_date_time_created, $user_id);


                /*$user_campaigns_with_campaign_type = UserCampaign::select('*')
                                                    ->join('campaign_triggers', 'user_campaigns.campaign_tree_id', '=', 'campaign_triggers.campaign_tree_id')
                                                    ->join('campaigns', 'campaign_triggers.campaign_id', '=', 'campaigns.id')
                                                    ->where('campaign_triggers.type', $campaign_type)
                                                    ->where('user_campaigns.user_id', $user_id)
                                                    ->where('user_campaigns.status', 'active')
                                                    ->get();

                $user_time_setting = UserTimeSetting::where('user_id', $user_id)->first();

                foreach ($user_campaigns_with_campaign_type as $user_campaing) {
                    // insert lead campaign id here
                    $add_data = array(
                                    'lead_id' => $lead_id,
                                    'campaign_id' => $user_campaing->campaign_id,
                                    'status' => 'active'
                                );
                    $add_lead_campaign = LeadCampaign::create($add_data);
                    // get messages
                    $get_campaign_messages = CampaignMessage::with('rules')->where('campaign_id', $user_campaing->campaign_id)->get();

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
                        if ($user_campaing->is_reminder == 0) {
                            if ($campaign_message->wait == 'day') {
                                if ($last_msg_time != "") {
                                    $dateTime = new \DateTime($last_msg_time);
                                    $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                                    $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                                    $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                                } else {
                                    $dateTime = new \DateTime($converted_date_time_created);
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
                                    $dateTime = new \DateTime($converted_date_time_created);
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
                                    $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                                    $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                                                'is_reminder' => $user_campaing->is_reminder,
                                                'wait' => $campaign_message->wait,
                                                'status' => 'pending'
                                            );
                        $add_message_queue = MessageQueue::create($message_queue_data);

                        foreach ($campaign_message->rules as $rule) {
                            if ($rule->execute_when == 'is_sent') {
                                $rule_msg = $rule->instant_reply;
                                preg_match_all("/\\[(.*?)\\]/", $rule_msg, $matches);
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
                                            $rule_msg = str_replace("[".$value."]", $custom_field_data, $rule_msg);
                                        }
                                    }
                                }
                                if ($rule_msg != "") {
                                    $message_queue_data = array(
                                                            'lead_id' => $lead_id,
                                                            'user_id' => $user_id,
                                                            'campaign_message_id' => $rule->message_id,
                                                            'message' => $rule_msg,
                                                            'send_timer' => $last_msg_time,
                                                            'is_reminder' => 0,
                                                            'wait' => NULL,
                                                            'status' => 'pending'
                                                        );
                                    $add_message_queue = MessageQueue::create($message_queue_data);

                                    $this->add_new_campaign($rule->removed, $lead_id, $rule->add_to_campaign, $campaign_message->campaign_id);
                                }
                            }
                        }
                    }
                    $last_msg_time = "";
                }*/
            
            echo "success";

            } catch (Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }
    }

    public function webhook_rescheduled(Request $request)
    {
        $params = $request->all();

        if ($params) {
            // Handle webhook after verifying signature:
            try {
                /*AcuityScheduling::verifyMessageSignature($apiKey);
                error_log("The message is authentic:\n".json_encode($params, JSON_PRETTY_PRINT));*/

                $appointment_id = $params['id'];

                $db_appointment = Appointment::where('appointment_id', $appointment_id)->first();
                $user = User::where('id', $db_appointment->user_id)->first();
                $lead = Lead::where('lead_id', $db_appointment->lead_id)->first();

                if (!$user) {
                    return;
                }
                $user_settings = Setting::where('user_id', $user->id)->first();
                if (!$user_settings) {
                    return;
                }

                $userId = $user_settings->acuity_user_id;
                $apiKey = $user_settings->acuity_api_key;

                $acuity = new AcuityScheduling(array(
                  'userId' => $userId,
                  'apiKey' => $apiKey
                ));

                $appointment_by_id = $acuity->request('/appointments/'.$appointment_id);

                // Timezone time difference calculate
                $time_diff = $this->time_difference($appointment_by_id['calendarTimezone'], $appointment_by_id['timezone']);
                $converted_time = date('H:i', strtotime($appointment_by_id['time']) + $time_diff);
                $converted_end_time = date('H:i', strtotime($appointment_by_id['endTime']) + $time_diff);
                $date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetime']));
                $date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetimeCreated']));
                $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetime']) + $time_diff);
                $converted_date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['datetimeCreated']) + $time_diff);

                $appointment_data = array(
                                        'date' => $appointment_by_id['date'],
                                        'date_time' => $date_time,
                                        'time' => $appointment_by_id['time'],
                                        'end_time' => $appointment_by_id['endTime'],
                                        'converted_time' => $converted_time,
                                        'converted_end_time' => $converted_end_time,
                                        'date_created' => $appointment_by_id['dateCreated'],
                                        'date_time_created' => $date_time_created,
                                        'price' => $appointment_by_id['price'],
                                        'price_sold' => $appointment_by_id['priceSold'],
                                        'paid' => $appointment_by_id['paid'],
                                        'amount_paid' => $appointment_by_id['amountPaid'],
                                        'type' => $appointment_by_id['type'],
                                        'appointment_type_id' => $appointment_by_id['appointmentTypeID'],
                                        'calendar_id' => $appointment_by_id['calendarID'],
                                        'timezone' => $appointment_by_id['timezone'],
                                        'calendar_timezone' => $appointment_by_id['calendarTimezone'],
                                        'can_client_cancel' => $appointment_by_id['canClientCancel'],
                                        'can_client_reschedule' => $appointment_by_id['canClientReschedule'],
                                        'status' => 'rescheduled'
                                    );
                // Add to log
                $log_data = array(
                                'appointment_id' => $appointment_by_id['id'],
                                'json_data' => json_encode($appointment_data),
                                'status' => 'rescheduled',
                                'is_adjusted' => 1
                            );
                $add_log = AppointmentLog::create($log_data);

                $campaign_type = "re_schedule";
                $lead_id = $lead->id;
                $lead_name = $lead->first_name;
                $appointment_date = date('Y-m-d', strtotime($converted_date_time));
                $appointment_datetime = $converted_date_time;
                $appointment_time = date('H:i', strtotime($appointment_by_id['time']));
                $user_id = $user->id;
                $msg_date_time_created = $converted_date_time_created;

                // set previous lead campaigns status
                $update_lead_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);

                // set previous lead message queues
                $update_message_queue = MessageQueue::where('lead_id', $lead_id)->update(['status' => 'canceled']);

                $this->campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $appointment_date, $appointment_datetime, $appointment_time, $msg_date_time_created, $converted_date_time_created, $user_id);

                // Reschedule appointment
                $update_appointment = Appointment::where('appointment_id', $appointment_by_id['id'])->update($appointment_data);
                if ($update_appointment) {
                    echo "success";
                }

            } catch (Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }
    }

    public function webhook_canceled(Request $request)
    {
        $params = $request->all();

        if ($params) {
            // Handle webhook after verifying signature:
            try {
                /*AcuityScheduling::verifyMessageSignature($apiKey);
                error_log("The message is authentic:\n".json_encode($params, JSON_PRETTY_PRINT));*/

                $appointment_id = $params['id'];

                $appointment_by_id = Appointment::where('appointment_id', $appointment_id)->first()->toArray();

                $user = User::where('id', $appointment_by_id->user_id)->first();
                $lead = Lead::where('lead_id', $appointment_by_id->lead_id)->first();

                // Timezone time difference calculate
                $time_diff = $this->time_difference($appointment_by_id['calendar_timezone'], $appointment_by_id['timezone']);
                $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['date_time']) + $time_diff);
                $converted_date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['date_time_created']) + $time_diff);

                $appointment_data = array(
                                        'date' => $appointment_by_id['date'],
                                        'date_time' => $appointment_by_id['date_time'],
                                        'time' => $appointment_by_id['time'],
                                        'end_time' => $appointment_by_id['end_time'],
                                        'converted_time' => $appointment_by_id['converted_time'],
                                        'converted_end_time' => $appointment_by_id['converted_end_time'],
                                        'date_created' => $appointment_by_id['date_created'],
                                        'date_time_created' => $appointment_by_id['date_time_created'],
                                        'price' => $appointment_by_id['price'],
                                        'price_sold' => $appointment_by_id['price_sold'],
                                        'paid' => $appointment_by_id['paid'],
                                        'amount_paid' => $appointment_by_id['amount_paid'],
                                        'type' => $appointment_by_id['type'],
                                        'appointment_type_id' => $appointment_by_id['appointment_type_id'],
                                        'calendar_id' => $appointment_by_id['calendar_id'],
                                        'timezone' => $appointment_by_id['timezone'],
                                        'calendar_timezone' => $appointment_by_id['calendar_timezone'],
                                        'canceled' => 1,
                                        'can_client_cancel' => $appointment_by_id['can_client_cancel'],
                                        'can_client_reschedule' => $appointment_by_id['can_client_reschedule'],
                                        'status' => 'canceled'
                                    );
                // Add to log
                $log_data = array(
                                'appointment_id' => $appointment_by_id['appointment_id'],
                                'json_data' => json_encode($appointment_data),
                                'status' => 'canceled',
                                'is_adjusted' => 1
                            );
                $add_log = AppointmentLog::create($log_data);

                
                $campaign_type = "cancel";
                $lead_id = $lead->id;
                $lead_name = $lead->first_name;
                $appointment_date = date('Y-m-d', strtotime($converted_date_time));
                $appointment_datetime = $converted_date_time;
                $appointment_time = date('H:i', strtotime($appointment_by_id['time']));
                $user_id = $user->id;
                $msg_date_time_created = $converted_date_time_created;

                // set previous lead campaigns status
                $update_lead_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);

                // set previous lead message queues
                $update_message_queue = MessageQueue::where('lead_id', $lead_id)->update(['status' => 'canceled']);
                
                $this->campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $appointment_date, $appointment_datetime, $appointment_time, $msg_date_time_created, $converted_date_time_created, $user_id);

                /*$user_campaigns_with_campaign_type = UserCampaign::select('*')
                                                    ->join('campaign_triggers', 'user_campaigns.campaign_tree_id', '=', 'campaign_triggers.campaign_tree_id')
                                                    ->join('campaigns', 'campaign_triggers.campaign_id', '=', 'campaigns.id')
                                                    ->where('campaign_triggers.type', $campaign_type)
                                                    ->where('user_campaigns.user_id', $user_id)
                                                    ->where('user_campaigns.status', 'active')
                                                    ->get();

                $user_time_setting = UserTimeSetting::where('user_id', $user_id)->first();

                foreach ($user_campaigns_with_campaign_type as $user_campaing) {
                    // insert lead campaign id here
                    $add_data = array(
                                    'lead_id' => $lead_id,
                                    'campaign_id' => $user_campaing->campaign_id,
                                    'status' => 'active'
                                );
                    $add_lead_campaign = LeadCampaign::create($add_data);
                    // get messages
                    $get_campaign_messages = CampaignMessage::with('rules')->where('campaign_id', $user_campaing->campaign_id)->get();

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
                        if ($user_campaing->is_reminder == 0) {
                            if ($campaign_message->wait == 'day') {
                                if ($last_msg_time != "") {
                                    $dateTime = new \DateTime($last_msg_time);
                                    $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                                    $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                                    $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                                } else {
                                    $dateTime = new \DateTime($converted_date_time_created);
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
                                    $dateTime = new \DateTime($converted_date_time_created);
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
                                    $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                                    $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                                                'is_reminder' => $user_campaing->is_reminder,
                                                'wait' => $campaign_message->wait,
                                                'status' => 'pending'
                                            );
                        $add_message_queue = MessageQueue::create($message_queue_data);

                        foreach ($campaign_message->rules as $rule) {
                            if ($rule->execute_when == 'is_sent') {
                                $rule_msg = $rule->instant_reply;
                                preg_match_all("/\\[(.*?)\\]/", $rule_msg, $matches);
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
                                            $rule_msg = str_replace("[".$value."]", $custom_field_data, $rule_msg);
                                        }
                                    }
                                }
                                if ($rule_msg != "") {
                                    $message_queue_data = array(
                                                            'lead_id' => $lead_id,
                                                            'user_id' => $user_id,
                                                            'campaign_message_id' => $rule->message_id,
                                                            'message' => $rule_msg,
                                                            'send_timer' => $last_msg_time,
                                                            'is_reminder' => 0,
                                                            'wait' => NULL,
                                                            'status' => 'pending'
                                                        );
                                    $add_message_queue = MessageQueue::create($message_queue_data);
                                    
                                    $this->add_new_campaign($rule->removed, $lead_id, $rule->add_to_campaign, $campaign_message->campaign_id);
                                }
                            }
                        }
                    }
                    $last_msg_time = "";
                }*/

                // Cancel appointment
                $update_appointment = Appointment::where('appointment_id', $appointment_by_id['id'])->update(['status' => 'canceled']);
                if ($update_appointment) {
                    echo "success";
                }

            } catch (Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }
    }

    public function webhook_changed(Request $request)
    {
        $params = $request->all();

        if ($params) {
            // Handle webhook after verifying signature:
            try {
                /*AcuityScheduling::verifyMessageSignature($apiKey);
                error_log("The message is authentic:\n".json_encode($params, JSON_PRETTY_PRINT));*/

                $appointment_id = $params['id'];

                $db_appointment = Appointment::where('appointment_id', $appointment_id)->first();
                $user = User::where('id', $db_appointment->user_id)->first();

                if (!$user) {
                    return;
                }
                $user_settings = Setting::where('user_id', $user->id)->first();
                if (!$user_settings) {
                    return;
                }

                $userId = $user_settings->acuity_user_id;
                $apiKey = $user_settings->acuity_api_key;

                $acuity = new AcuityScheduling(array(
                  'userId' => $userId,
                  'apiKey' => $apiKey
                ));

                $appointment_by_id = $acuity->request('/appointments/'.$appointment_id);

                $lead = lead::where('phone', $appointment_by_id['phone'])->orderBy('created_at', 'desc')->first();

                if ($lead) {
                    $lead_data = array(
                                    'first_name' => $appointment_by_id['firstName'],
                                    'last_name' => $appointment_by_id['lastName'],
                                    'email' => $appointment_by_id['email'],
                                );

                }

                $update_lead = Lead::where('id', $lead->id)->where('created_by', 'acuity')->update($lead_data);
                if ($update_lead) {
                    echo "success";
                }

            } catch (Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }
    }

    public function webhook_complete(Request $request)
    {
        $params = $request->all();

        if ($params) {
            // Handle webhook after verifying signature:
            try {
                /*AcuityScheduling::verifyMessageSignature($apiKey);
                error_log("The message is authentic:\n".json_encode($params, JSON_PRETTY_PRINT));*/

                $appointment_id = $params['id'];

                $appointment_by_id = Appointment::where('appointment_id', $appointment_id)->first()->toArray();

                $appointment_data = array(
                                        'date' => $appointment_by_id['date'],
                                        'date_time' => $appointment_by_id['date_time'],
                                        'time' => $appointment_by_id['time'],
                                        'end_time' => $appointment_by_id['end_time'],
                                        'converted_time' => $appointment_by_id['converted_time'],
                                        'converted_end_time' => $appointment_by_id['converted_end_time'],
                                        'date_created' => $appointment_by_id['date_created'],
                                        'date_time_created' => $appointment_by_id['date_time_created'],
                                        'price' => $appointment_by_id['price'],
                                        'price_sold' => $appointment_by_id['price_sold'],
                                        'paid' => $appointment_by_id['paid'],
                                        'amount_paid' => $appointment_by_id['amount_paid'],
                                        'type' => $appointment_by_id['type'],
                                        'appointment_type_id' => $appointment_by_id['appointment_type_id'],
                                        'calendar_id' => $appointment_by_id['calendar_id'],
                                        'timezone' => $appointment_by_id['timezone'],
                                        'calendar_timezone' => $appointment_by_id['calendar_timezone'],
                                        'can_client_cancel' => $appointment_by_id['can_client_cancel'],
                                        'can_client_reschedule' => $appointment_by_id['can_client_reschedule'],
                                        'status' => 'completed'
                                    );
                // Add to log
                $log_data = array(
                                'appointment_id' => $appointment_by_id['appointment_id'],
                                'json_data' => json_encode($appointment_data),
                                'status' => 'completed',
                                'is_adjusted' => 1
                            );
                $add_log = AppointmentLog::create($log_data);

                // Complete appointment
                $update_appointment = Appointment::where('appointment_id', $appointment_by_id['id'])->update(['status' => 'completed']);
                if ($update_appointment) {
                    echo "success";
                }
                
            } catch (Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }
    }

    public function acuity_fetch_calendar(Request $request)
    {
        $params = $request->all();

        $userId = $params['user_id'];
        $apiKey = $params['api_key'];

        $acuity = new AcuityScheduling(array(
          'userId' => $userId,
          'apiKey' => $apiKey
        ));

        $calendar_list = $acuity->request('/calendars');

        foreach ($calendar_list as $calendar) {
            echo "<option value='".$calendar['id']."'>".$calendar['name']."</option>\n";
        }
    }

    public function clickfunnel_webhook($identifier)
    {
        $user = User::where('identifier', $identifier)->first();

        $user_settings = Setting::where('user_id', $user->id)->first();
        if (!$user_settings) {
            echo "error";
            return;
        }
        $user_funnel_id = $user_settings->click_funnel_id;

        if (!empty($user_funnel_id)) {
            $json_data = file_get_contents('php://input');

            $data = json_decode($json_data, true);

            $first_name = $data['data']['attributes']['first-name'];
            $last_name = $data['data']['attributes']['last-name'];
            $phone = $data['data']['attributes']['phone'];
            $email = $data['data']['attributes']['email'];

            $funnel_id = $data['data']['attributes']['funnel-id'];
            // Logic with funnel id
            if ($funnel_id == $user_funnel_id) {
                $lead = lead::where('phone', $phone)->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
                if (!$lead) {
                    $lead_data = array(
                                    'user_id' => $user->id,
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'phone' => $phone,
                                    'email' => $email,
                                    'is_appointment' => 0,
                                    'created_by' => 'clickfunnel',
                                    'json_data' => $json_data
                                );

                    $lead_insert = Lead::create($lead_data);
                } else {
                    $lead_data = array(
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'email' => $email,
                                );
                    $update_lead = Lead::where('id', $lead->id)->update($lead_data);
                }
                // insert campaign relation here
                $campaign_type = "no_schedule";
                if (!$lead) {
                    $lead_id = $lead_insert->id;
                    $lead_name = $lead_insert->first_name;
                    $lead_create_date = date('Y-m-d', strtotime($lead_insert->created_at));
                    $lead_create_datetime = $lead_insert->created_at;
                    $lead_create_time = date('H:i', strtotime($lead_insert->created_at));
                    $user_id = $user->id;
                } else {
                    $lead_id = $lead->id;
                    $lead_name = $lead->first_name;
                    $lead_create_date = date('Y-m-d', strtotime($lead->created_at));
                    $lead_create_datetime = $lead->created_at;
                    $lead_create_time = date('H:i', strtotime($lead->created_at));
                    $user_id = $user->id;

                    // set previous lead campaigns status
                    $update_lead_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);

                    // set previous lead message queues
                    $update_message_queue = MessageQueue::where('lead_id', $lead_id)->update(['status' => 'canceled']);
                }
                $msg_date_time_created = $lead_create_datetime;

                $this->campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $lead_create_date, $lead_create_datetime, $lead_create_time, $msg_date_time_created, $lead_create_datetime, $user_id);

                /*$user_campaigns_with_campaign_type = UserCampaign::select('*')
                                                    ->join('campaign_triggers', 'user_campaigns.campaign_tree_id', '=', 'campaign_triggers.campaign_tree_id')
                                                    ->join('campaigns', 'campaign_triggers.campaign_id', '=', 'campaigns.id')
                                                    ->where('campaign_triggers.type', $campaign_type)
                                                    ->where('user_campaigns.user_id', $user_id)
                                                    ->where('user_campaigns.status', 'active')
                                                    ->get();

                $user_time_setting = UserTimeSetting::where('user_id', $user_id)->first();

                foreach ($user_campaigns_with_campaign_type as $user_campaing) {
                    // insert lead campaign id here
                    $add_data = array(
                                    'lead_id' => $lead_id,
                                    'campaign_id' => $user_campaing->campaign_id,
                                    'status' => 'active'
                                );
                    $add_lead_campaign = LeadCampaign::create($add_data);
                    // get messages
                    $get_campaign_messages = CampaignMessage::with('rules')->where('campaign_id', $user_campaing->campaign_id)->get();

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
                                        $custom_field_data = $lead_create_date;
                                    } else if ($custom_field->name == 'appointment_time') {
                                        $custom_field_data = $lead_create_time;
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
                        if ($user_campaing->is_reminder == 0) {
                            if ($campaign_message->wait == 'day') {
                                if ($last_msg_time != "") {
                                    $dateTime = new \DateTime($last_msg_time);
                                    $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                                    $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                                    $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                                } else {
                                    $dateTime = new \DateTime($lead_create_date);
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
                                    $dateTime = new \DateTime($lead_create_datetime);
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
                                    $dateTime = new \DateTime($lead_create_date);
                                    $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                                    $dateTime = new \DateTime($lead_create_datetime);
                                    $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                                                'is_reminder' => $user_campaing->is_reminder,
                                                'wait' => $campaign_message->wait,
                                                'status' => 'pending'
                                            );
                        $add_message_queue = MessageQueue::create($message_queue_data);

                        foreach ($campaign_message->rules as $rule) {
                            if ($rule->execute_when == 'is_sent') {
                                $rule_msg = $rule->instant_reply;
                                preg_match_all("/\\[(.*?)\\]/", $rule_msg, $matches);
                                foreach ($matches[1] as $value) {
                                    foreach ($custom_fields_with_user as $custom_field) {
                                        if ($custom_field->name == $value) {
                                            if ($custom_field->name == 'customer_first_name') {
                                                $custom_field_data = $lead_name;
                                            } else if ($custom_field->name == 'appointment_date') {
                                                $custom_field_data = $lead_create_date;
                                            } else if ($custom_field->name == 'appointment_time') {
                                                $custom_field_data = $lead_create_time;
                                            } else {
                                                if (!empty($custom_field->user_value)) {
                                                    $custom_field_data = $custom_field->user_value;
                                                } else {
                                                    $custom_field_data = $custom_field->value;
                                                }
                                            }
                                            $rule_msg = str_replace("[".$value."]", $custom_field_data, $rule_msg);
                                        }
                                    }
                                }
                                if ($rule_msg != "") {
                                    $message_queue_data = array(
                                                            'lead_id' => $lead_id,
                                                            'user_id' => $user_id,
                                                            'campaign_message_id' => $rule->message_id,
                                                            'message' => $rule_msg,
                                                            'send_timer' => $last_msg_time,
                                                            'is_reminder' => 0,
                                                            'wait' => NULL,
                                                            'status' => 'pending'
                                                        );
                                    $add_message_queue = MessageQueue::create($message_queue_data);
                                    
                                    $this->add_new_campaign($rule->removed, $lead_id, $rule->add_to_campaign, $campaign_message->campaign_id);
                                }
                            }
                        }
                    }
                    $last_msg_time = "";
                }*/

                echo "success";   
            }
        } else {
            echo "error";
        }
    }

    public function testTime()
    {
        echo time()."<br>";
        echo date(DATE_ATOM, mktime(0, 0, 0, 10, 12, 2021))."<br>";
        echo date(DATE_RFC2822)."<br>";
        echo "2021-10-11T23:00:00-0500 <br>";
        echo date("Y-m-d h:i:s A", strtotime("2021-10-11T23:00:00-0500"))."<br>";
        $date_time = date("Y-F-d H:i:s");

        echo $date_time."<br>";

        $timezones = config('view.timezones');

        echo "<br><br>";
        foreach ($timezones as $key => $value) {
            $converted_datetime = date('Y-m-d h:i:s A', strtotime($date_time) + ($value * (60 * 60)));
            echo $key.": (".$value." hrs) ".$converted_datetime."<br>";
        }

        echo "<br><br><br><br>";


        // $send_timers = ["2021-09-29 22:35:00", "2021-09-29 22:45:00"];
        $register_time = "2021-09-29 21:50:00";
        $send_timers = array(
                            array(
                                'date' => '2021-09-29 22:00:00',
                                'minutes' => '5'
                            ),
                            array(
                                'date' => '2021-09-29 22:10:00',
                                'minutes' => '1445'
                            ),
                        );
        $last_msg_time = "";

        $user_id = Auth::id();
        $user_campaing = UserTimeSetting::where('user_id', $user_id)->first();

        foreach ($send_timers as $send_timer) {
            if ($last_msg_time == "") {
                $dateTime = new \DateTime($register_time);
                $added_datetime = $dateTime->modify('+'.$send_timer['minutes'].' minutes');
                $added_datetime = $dateTime->format('Y-m-d H:i:s');
            } else {
                $dateTime = new \DateTime($last_msg_time);
                $added_datetime = $dateTime->modify('+'.$send_timer['minutes'].' minutes');
                $added_datetime = $dateTime->format('Y-m-d H:i:s');
            }
            $added_time = date('H:i:s', strtotime($added_datetime));

            echo "last date time - ".$last_msg_time."<br>";
            echo "minutes - ".$send_timer['minutes']."<br>";
            echo "date time - ".$register_time."<br>";
            echo "added date time - ".$added_datetime."<br>";
            echo "time - ".$added_time."<br>";
            echo "from time - ".$user_campaing->from_time."<br>";
            echo "to time - ".$user_campaing->to_time."<br>";
            echo "<pre>";
            // print_r($user_campaing->toArray());

            if (strtotime($added_time) >= strtotime($user_campaing->from_time) || strtotime($added_time) <= strtotime($user_campaing->to_time)) {
                echo "if<br>";
                // new time changed and last timer save
                if (strtotime($added_time) > strtotime($user_campaing->to_time)) {
                    echo "add 1 day<br>";
                    $dateTime = new \DateTime($added_datetime);
                    $new_datetime = $dateTime->modify('+1 days');
                    $new_datetime = $dateTime->format('Y-m-d '.$user_campaing->to_time);
                    echo "final date time - ".$new_datetime."<br>";
                    $last_msg_time = $new_datetime;
                } else {
                    echo "dont add day<br>";
                    $dateTime = new \DateTime($added_datetime);
                    $new_datetime = $dateTime->format('Y-m-d '.$user_campaing->to_time);
                    echo "final date time - ".$new_datetime."<br>";
                    $last_msg_time = $new_datetime;
                }
            } else {
                echo "else<br>";
                echo "final date time - ".$added_datetime."<br>";
                $last_msg_time = $added_datetime;
            }
            echo "=============================================";
            echo "<br><br><br>";
        }
    }

    public function clickfunnel_fetch_funnel(Request $request)
    {
        $params = $request->all();

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.clickfunnels.com/funnels.json?email='.$params['email'].'&auth_token='.$params['api_key'],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datas = json_decode($response, true);
        if (!empty($datas)) {
            if (array_key_exists("cf_affiliate_id_from_sticky_cookie",$datas) || array_key_exists("email",$datas) || array_key_exists("password",$datas))
            {
                return;
            }
            else
            {
                foreach ($datas as $data) {
                    echo "<option value='".$data['id']."'>".$data['name']."</option>\n";
                }
            }
        } else {
            return;
        }
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
            $add_campaign = array(
                            'lead_id' => $lead_id,
                            'campaign_id' => $new_campaign_id,
                            'status' => 'active',
                        );
            $add_new_campaign = LeadCampaign::create($add_campaign);
        }
    }

    public function time_difference($calendar_timezone, $user_timezone)
    {
        $application_timezone = TimezoneSetting::where('type', 'application')->first();

        if (!empty($application_timezone)) {
            $user_timezone = $application_timezone->timezone;
        }

        $calendar_tz = new \DateTimeZone($calendar_timezone);
        $calendar = new \DateTime('now', $calendar_tz);

        $user_tz = new \DateTimeZone($user_timezone);
        $user = new \DateTime('now', $user_tz);

        $calendar_offset = $calendar->getOffset();
        $user_offset = $user->getOffset();

        $time_diff = $user_offset - $calendar_offset;

        return $time_diff; // Returns time difference in seconds
    }

    public function artisan_add_appointments()
    {
        $add_appointments = Artisan::call('log:add_appointments');
    }

    public function artisan_send_queue_messages()
    {
        $add_appointments = Artisan::call('send:queue_messages');
    }

    public function calendar()
    {
        $user_id = Auth::id();
        // echo "User ID ".$user_id."<br>";
        $appointments = Appointment::with('lead')->where('user_id', $user_id)
                                    ->where(function($q) {
                                        $q->where('status', 'scheduled')
                                        ->orWhere('status', 'rescheduled')
                                        ->orWhere('status', 'changed');
                                    })
                                    ->get();
        return view('calendar.index', compact('appointments'));
    }

    public function calendarAddSchedule()
    {   
        $user_id = Auth::id();
        $leads = Lead::where('user_id', $user_id)->get();
        return view('calendar.add', compact('leads'));
    }

    public function calendarShowSchedule($appointment_id)
    {
        $user_id = Auth::id();
        $appointment = Appointment::with('lead')->where('appointment_id', $appointment_id)->first();
        
        return view('calendar.show', compact('appointment'));
    }

    public function calendarStoreSchedule(Request $request)
    {
        $user_id = Auth::id();

        $user = User::where('id', $user_id)->first();
        
        $lead = Lead::where('id', $request['lead_id'])->first();

        $user_settings = Setting::where('user_id', $user_id)->first();

        $application_timezone = TimezoneSetting::where('type', 'application')->first();

        if (!empty($application_timezone)) {
            $timezone = $application_timezone->timezone;
        } else {
            $timezone = "UTC";
        }

        $time = date("H:i", strtotime($request['time']));
        $end_time = date("H:i", strtotime($request['time']) + 1800); // manually set 30 mins for calendar dummy data
        $date = date("F d, Y", strtotime($request['date']));
        $date_created = date('F d, Y', strtotime(now()));
        $datetime = $request['date']."T".$time;
        $first_name = $lead->first_name;
        $last_name = $lead->last_name;
        $phone = $lead->phone;
        $email = $lead->email;

        if (!$user_settings || empty($user_settings->acuity_calendar_id)) {
            return;
        }

        /*$userId = $user_settings->acuity_user_id;
        $apiKey = $user_settings->acuity_api_key;

        $acuity = new AcuityScheduling(array(
          'userId' => $userId,
          'apiKey' => $apiKey
        ));
        // Make the create-appointment request:
        $appointment_json = $acuity->request('/appointments', array(
          'method' => 'POST',
          'data' => array(
            'appointmentTypeID' => time(),
            'calendarID'        => $user_settings->acuity_calendar_id,
            'datetime'          => $datetime,
            'firstName'         => $first_name,
            'lastName'          => $last_name,
            'phone'             => $phone,
            'email'             => $email,
            'timezone'          => $timezone
          )
        ));*/

        /*echo "<pre>";
        print_r($appointment_json);*/

        // manual data
        $appointment_json = '{
                                "id": '.time().',
                                "firstName": "'.$first_name.'",
                                "lastName": "'.$last_name.'",
                                "phone": "'.$phone.'",
                                "email": "'.$email.'",
                                "date": "'.$date.'",
                                "time": "'.$time.'",
                                "endTime": "'.$end_time.'",
                                "dateCreated": "'.$date_created.'",
                                "datetime": "'.$datetime.'",
                                "price": "0.00",
                                "paid": "no",
                                "amountPaid": "0.00",
                                "type": "ALAN",
                                "appointmentTypeID": 1,
                                "classID": null,
                                "category": "",
                                "duration": "30",
                                "calendar": "TBD SAYEED 01",
                                "calendarID": 4887542,
                                "location": "",
                                "certificate": null,
                                "confirmationPage": "https://www.acuityscheduling.com/schedule.php?action=appt&owner=11145481&id[]=1220aa9f41091c50c0cc659385cfa1d0",
                                "formsText": "...",
                                "forms": [],
                                "notes": "",
                                "timezone": "Asia/Dhaka",
                                "calendarTimezone": "America/New_York",
                                "labels": [{
                                    "id": 1,
                                    "name": "Completed",
                                    "color": "pink"
                                }]
                            }';

        $appointment = json_decode($appointment_json, true);

        $date_time = date('Y-m-d H:i:s', strtotime($appointment['datetime']));

        $time_mins = date('i', strtotime($time));

        if ($time_mins < 30) {
            $db_start_time = date('H:00', strtotime($time));
        } elseif ($time_mins > 30 && $time_mins != 0) {
            $db_start_time = date('H:30', strtotime($time));
        } else {
            $db_start_time = $time;
        }

        $db_date_time = date('Y-m-d', strtotime($date_time));

        $check_appointment_time = DB::select("
                                            SELECT *
                                            FROM appointments
                                            WHERE
                                                user_id = $user->id AND DATE(date_time) = '$db_date_time' 
                                                AND ((TIME(converted_time) <= '$time' AND TIME(converted_end_time) > '$time') OR (TIME(converted_time) < '$end_time' AND TIME(converted_end_time) >= '$end_time'))
                                                AND status IN('scheduled','rescheduled','changed')");

        if (!empty($check_appointment_time)) {
            Session::flash('error_message','Appointment Time is already booked by other client');
            return redirect('home/calendar');
            die();
        }

        if (!isset($appointment['phone'])) {
            echo "error";
            die();
        }
        $lead = lead::where('phone', $appointment['phone'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

       // If no appointment data is found, it'll update the status of the lead
        $lead_data = array(
                        'first_name' => $appointment['firstName'],
                        'last_name' => $appointment['lastName'],
                        'email' => $appointment['email'],
                        'json_data' => $appointment_json
                    );

       if ($lead->is_appointment == 0) {
           $lead_data['is_appointment'] = 1;
       } 
       $update_lead = Lead::where('id', $lead->id)->update($lead_data);

       // Check if other appointments present with Scheduled, rescheduled and changed
       $open_appointments = Appointment::where('lead_id', $lead->id)
                                       ->where(function($q) {
                                           $q->where('status', 'scheduled')
                                           ->orWhere('status', 'rescheduled')
                                           ->orWhere('status', 'changed')
                                           ->orWhere('status', '');
                                       })
                                       ->whereNotIn('appointment_id', [$appointment['id']])
                                       ->get();
       // update the open appointments status to canceled
       foreach ($open_appointments as $open_appointment) {
           // Update appointment log 
           $log_data = array(
                           'appointment_id' => $open_appointment->appointment_id,
                           'json_data' => json_encode($open_appointment->toArray()),
                           'status' => 'canceled',
                           'is_adjusted' => 1
                       );
           $add_log = AppointmentLog::create($log_data);

           // Update appointment status
           $update_appointment = Appointment::where('appointment_id', $open_appointment->appointment_id)->update(['status' => 'canceled']);
       }

       // Timezone time difference calculate
       // $time_diff = $this->time_difference($appointment['calendarTimezone'], $appointment['timezone']);
       $time = date('H:i', strtotime($appointment['time']));
       $end_time = date('H:i', strtotime($appointment['endTime']));
       $converted_time = date('H:i', strtotime($appointment['time']));
       $converted_end_time = date('H:i', strtotime($appointment['endTime']));
       $date_time = date('Y-m-d H:i:s', strtotime($appointment['datetime']));
       $date_time_created = date('Y-m-d H:i:s', strtotime(now()));
       $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment['datetime']));
       $converted_date_time_created = date('Y-m-d H:i:s', strtotime(now()));

       $appointment_data = array(
                               'appointment_id' => $appointment['id'],
                               'user_id' => $user->id,
                               'lead_id' => $lead->id,
                               'date' => $appointment['date'],
                               'date_time' => $date_time,
                               'time' => $time,
                               'end_time' => $end_time,
                               'converted_time' => $converted_time,
                               'converted_end_time' => $converted_end_time,
                               'date_created' => $appointment['dateCreated'],
                               'date_time_created' => $date_time_created,
                               'price' => $appointment['price'],
                               'price_sold' => 0,
                               'paid' => $appointment['paid'],
                               'amount_paid' => $appointment['amountPaid'],
                               'type' => $appointment['type'],
                               'appointment_type_id' => $appointment['appointmentTypeID'],
                               'calendar_id' => $appointment['calendarID'],
                               'timezone' => $appointment['timezone'],
                               'calendar_timezone' => $appointment['calendarTimezone'],
                               'canceled' => 0,
                               'can_client_cancel' => 1,
                               'can_client_reschedule' => 1,
                               'status' => 'scheduled',
                               'json_data' => $appointment_json
                           );

       // Add to log
       $log_data = array(
                       'appointment_id' => $appointment['id'],
                       'json_data' => json_encode($appointment_data),
                       'status' => 'scheduled',
                       'is_adjusted' => 1
                   );
       $add_log = AppointmentLog::create($log_data);

       $campaign_type = "schedule";
       $lead_id = $lead->id;
       $lead_name = $lead->first_name;
       $appointment_date = date('Y-m-d', strtotime($converted_date_time));
       $appointment_datetime = $converted_date_time;
       $appointment_time = date('H:i', strtotime($appointment['time']));
       $user_id = $user->id;
       $msg_date_time_created = $converted_date_time_created;

       // set previous lead campaigns status
       $update_lead_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);

       // set previous lead message queues
       $update_message_queue = MessageQueue::where('lead_id', $lead_id)->update(['status' => 'canceled']);

       $this->campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $appointment_date, $appointment_datetime, $appointment_time, $msg_date_time_created, $converted_date_time_created, $user_id);

       $create = Appointment::create($appointment_data);

       if ($create) {
            Session::flash('success_message','Successfully saved');
       }

       return redirect('home/calendar');
    }

    public function calendarShowReschedule($appointment_id)
    {
        $user_id = Auth::id();
        $appointment = Appointment::with('lead')->where('appointment_id', $appointment_id)->first();
        
        return view('calendar.show_reschedule', compact('appointment'));
    }

    public function calendarUpdateReschedule(Request $request, $appointment_id)
    {
        $user_id = Auth::id();

        $user = User::where('id', $user_id)->first();

        $user_settings = Setting::where('user_id', $user_id)->first();

        $application_timezone = TimezoneSetting::where('type', 'application')->first();

        $db_appointment = Appointment::where('appointment_id', $appointment_id)->first();

        $lead = Lead::where('id', $db_appointment->lead_id)->first();

        if (!empty($application_timezone)) {
            $timezone = $application_timezone->timezone;
        } else {
            $timezone = "UTC";
        }

        if (isset($request['from_calendar']) && $request['from_calendar'] == 1) {
            $time = date("H:i", strtotime($db_appointment->converted_time));
            $end_time = date("H:i", strtotime($db_appointment->converted_end_time));
            $request['date'] = date("Y-m-d", strtotime($request['date']));
        } else {
            $time = date("H:i", strtotime($request['time']));
            $end_time = date("H:i", strtotime($request['time']) + 1800); // manually set 30 mins for calendar dummy data
        }
        
        $date = date("F d, Y", strtotime($request['date']));
        $date_created = date('F d, Y', strtotime(now()));
        $datetime = $request['date']."T".$time;
        $first_name = $lead->first_name;
        $last_name = $lead->last_name;
        $phone = $lead->phone;
        $email = $lead->email;

        if (!$user_settings || empty($user_settings->acuity_calendar_id)) {
            return;
        }

        /*$userId = $user_settings->acuity_user_id;
        $apiKey = $user_settings->acuity_api_key;

        $acuity = new AcuityScheduling(array(
          'userId' => $userId,
          'apiKey' => $apiKey
        ));

        // Make the reschedule-appointment request:
        $appointmentID = $appointment_id;
        $appointment_json = $acuity->request('/appointments/'.$appointmentID.'/reschedule', array(
          'method' => 'PUT',
          'data' => array(
            'datetime' => $datetime,
            'timezone' => $timezone
          )
        ));*/

        // manual data
        $appointment_json = '{
                                "id": '.time().',
                                "firstName": "'.$first_name.'",
                                "lastName": "'.$last_name.'",
                                "phone": "'.$phone.'",
                                "email": "'.$email.'",
                                "date": "'.$date.'",
                                "time": "'.$time.'",
                                "endTime": "'.$end_time.'",
                                "dateCreated": "'.$date_created.'",
                                "datetime": "'.$datetime.'",
                                "price": "0.00",
                                "paid": "no",
                                "amountPaid": "0.00",
                                "type": "ALAN",
                                "appointmentTypeID": 1,
                                "classID": null,
                                "duration": "30",
                                "calendar": "TBD SAYEED 01",
                                "calendarID": 4887542,
                                "location": "",
                                "certificate": null,
                                "confirmationPage": "https://acuityscheduling.com/schedule.php?owner=11145481&id[]=1220aa9f41091c50c0cc659385cfa1d0&action=appt",
                                "formsText": "...",
                                "notes": "Notes",
                                "timezone": "Asia/Dhaka",
                                "calendarTimezone": "America/New_York",
                                "forms": [{
                                    "id": 1,
                                    "name": "Example Intake Form",
                                    "values": [{
                                        "value": "yes",
                                        "name": "Is this your first visit?",
                                        "fieldID": 1,
                                        "id": 21502993
                                    }, {
                                        "value": "Ninja",
                                        "name": "What is your goal for this appointment?",
                                        "fieldID": 2,
                                        "id": 21502994
                                    }]
                                }],
                                "noShow": false
                            }';

        $appointment = json_decode($appointment_json, true);

        // Timezone time difference calculate
        $time = date('H:i', strtotime($appointment['time']));
        $end_time = date('H:i', strtotime($appointment['endTime']));
        $converted_time = date('H:i', strtotime($appointment['time']));
        $converted_end_time = date('H:i', strtotime($appointment['endTime']));
        $date_time = date('Y-m-d H:i:s', strtotime($appointment['datetime']));
        $date_time_created = date('Y-m-d H:i:s', strtotime(now()));
        $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment['datetime']));
        $converted_date_time_created = date('Y-m-d H:i:s', strtotime(now()));

        $appointment_data = array(
                                'date' => $appointment['date'],
                                'date_time' => $date_time,
                                'time' => $time,
                                'end_time' => $end_time,
                                'converted_time' => $converted_time,
                                'converted_end_time' => $converted_end_time,
                                'date_created' => $appointment['dateCreated'],
                                'date_time_created' => $date_time_created,
                                'price' => $appointment['price'],
                                'price_sold' => 0,
                                'paid' => $appointment['paid'],
                                'amount_paid' => $appointment['amountPaid'],
                                'type' => $appointment['type'],
                                'appointment_type_id' => $appointment['appointmentTypeID'],
                                'calendar_id' => $appointment['calendarID'],
                                'timezone' => $appointment['timezone'],
                                'calendar_timezone' => $appointment['calendarTimezone'],
                                'can_client_cancel' => 1,
                                'can_client_reschedule' => 1,
                                'status' => 'rescheduled',
                                'json_data' => $appointment_json
                            );

        $db_date_time = date('Y-m-d', strtotime($date_time));

        $check_appointment_time = DB::select("
                                            SELECT *
                                            FROM appointments
                                            WHERE
                                                user_id = $user->id AND DATE(date_time) = '$db_date_time' 
                                                AND ((TIME(converted_time) <= '$time' AND TIME(converted_end_time) > '$time') OR (TIME(converted_time) < '$end_time' AND TIME(converted_end_time) >= '$end_time'))
                                                AND status IN('scheduled','rescheduled','changed')
                                                AND appointment_id NOT IN ($appointment_id)
                                            ");

        if (!empty($check_appointment_time)) {
            Session::flash('error_message','Appointment Time is already booked by other client');
            if (isset($request['from_calendar']) && $request['from_calendar'] == 1) {
                echo "error_appointment_time";
            } else {
                return redirect('home/calendar');
            }
            die();
        }

        // Add to log
        $log_data = array(
                        'appointment_id' => $appointment['id'],
                        'json_data' => json_encode($appointment_data),
                        'status' => 'rescheduled',
                        'is_adjusted' => 1
                    );
        $add_log = AppointmentLog::create($log_data);

        $campaign_type = "re_schedule";
        $lead_id = $lead->id;
        $lead_name = $lead->first_name;
        $appointment_date = date('Y-m-d', strtotime($converted_date_time));
        $appointment_datetime = $converted_date_time;
        $appointment_time = date('H:i', strtotime($appointment['time']));
        $user_id = $user->id;
        $msg_date_time_created = $converted_date_time_created;

        // set previous lead campaigns status
        $update_lead_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);

        // set previous lead message queues
        $update_message_queue = MessageQueue::where('lead_id', $lead_id)->update(['status' => 'canceled']);

        $this->campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $appointment_date, $appointment_datetime, $appointment_time, $msg_date_time_created, $converted_date_time_created, $user_id);

        // Reschedule appointment
        $update_appointment = Appointment::where('appointment_id', $appointment_id)->update($appointment_data);
        if ($update_appointment) {
            Session::flash('success_message','Successfully updated');
        }

        if (isset($request['from_calendar']) && $request['from_calendar'] == 1) {
            echo "success";
        } else {
            return redirect('home/calendar');
        }
    }

    public function calendarCancelSchedule($appointment_id)
    {
        $user_id = Auth::id();

        $user = User::where('id', $user_id)->first();

        $user_settings = Setting::where('user_id', $user_id)->first();

        $appointment_by_id = Appointment::where('appointment_id', $appointment_id)->first()->toArray();

        $lead = Lead::where('id', $appointment_by_id['lead_id'])->first();

        if (!$user_settings || empty($user_settings->acuity_calendar_id)) {
            return;
        }

        /*$userId = $user_settings->acuity_user_id;
        $apiKey = $user_settings->acuity_api_key;

        $acuity = new AcuityScheduling(array(
          'userId' => $userId,
          'apiKey' => $apiKey
        ));

        // Make the cancel-appointment request:
        $appointmentID = $appointment_by_id['appointment_id'];
        $appointment_json = $acuity->request('/appointments/'.$appointmentID.'/cancel', array(
          'method' => 'PUT',
          'data' => array(
            'cancelNote' => 'Appointment Canceled'
          )
        ));

        $appointment_by_id = json_decode($appointment_json, true);*/

        // inserting manually
        
        if (!empty($appointment_by_id['calendar_timezone'])) {
            $time_diff = $this->time_difference($appointment_by_id['calendar_timezone'], $appointment_by_id['timezone']);

        } else {
            $time_diff = 0;
        }
        $converted_date_time = date('Y-m-d H:i:s', strtotime($appointment_by_id['date_time']) + $time_diff);
        $converted_date_time_created = date('Y-m-d H:i:s', strtotime($appointment_by_id['date_time_created']) + $time_diff);
        $current_time = date('Y-m-d H:i:00', strtotime(now()));

        $appointment_data = array(
                                'date' => $appointment_by_id['date'],
                                'date_time' => $appointment_by_id['date_time'],
                                'time' => $appointment_by_id['time'],
                                'end_time' => $appointment_by_id['end_time'],
                                'converted_time' => $appointment_by_id['converted_time'],
                                'converted_end_time' => $appointment_by_id['converted_end_time'],
                                'date_created' => $appointment_by_id['date_created'],
                                'date_time_created' => $appointment_by_id['date_time_created'],
                                'price' => $appointment_by_id['price'],
                                'price_sold' => $appointment_by_id['price_sold'],
                                'paid' => $appointment_by_id['paid'],
                                'amount_paid' => $appointment_by_id['amount_paid'],
                                'type' => $appointment_by_id['type'],
                                'appointment_type_id' => $appointment_by_id['appointment_type_id'],
                                'calendar_id' => $appointment_by_id['calendar_id'],
                                'timezone' => $appointment_by_id['timezone'],
                                'calendar_timezone' => $appointment_by_id['calendar_timezone'],
                                'canceled' => 1,
                                'can_client_cancel' => $appointment_by_id['can_client_cancel'],
                                'can_client_reschedule' => $appointment_by_id['can_client_reschedule'],
                                'status' => 'canceled'
                            );
        // Add to log
        $log_data = array(
                        'appointment_id' => $appointment_by_id['appointment_id'],
                        'json_data' => json_encode($appointment_data),
                        'status' => 'canceled',
                        'is_adjusted' => 1
                    );
        $add_log = AppointmentLog::create($log_data);

        $campaign_type = "cancel";
        $last_msg_time = "";

        $lead_id = $lead->id;
        $lead_name = $lead->first_name;
        $appointment_date = date('Y-m-d', strtotime($converted_date_time));
        $appointment_datetime = $converted_date_time;
        $appointment_time = date('H:i', strtotime($appointment_by_id['time']));
        $user_id = $user->id;
        $msg_date_time_created = $current_time;

        // set previous lead campaigns status
        $update_lead_campaign = LeadCampaign::where('lead_id', $lead_id)->update(['status' => 'inactive']);

        // set previous lead message queues
        $update_message_queue = MessageQueue::where('lead_id', $lead_id)->update(['status' => 'canceled']);
        
        $this->campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $appointment_date, $appointment_datetime, $appointment_time, $msg_date_time_created, $converted_date_time_created, $user_id);

        // Cancel appointment
        $update_appointment = Appointment::where('appointment_id', $appointment_by_id['appointment_id'])->update(['status' => 'canceled']);

        if ($update_appointment) {
            echo "success";
        }
    }

    public function campaign_messages_rules_process($campaign_type, $lead_id, $lead_name, $appointment_date, $appointment_datetime, $appointment_time, $msg_date_time_created, $converted_date_time_created, $user_id)
    {
        $last_msg_time = "";

        $user_campaigns_with_campaign_type = UserCampaign::select('*')
                                            ->join('campaign_triggers', 'user_campaigns.campaign_tree_id', '=', 'campaign_triggers.campaign_tree_id')
                                            ->join('campaigns', 'campaign_triggers.campaign_id', '=', 'campaigns.id')
                                            ->where('campaign_triggers.type', $campaign_type)
                                            ->where('user_campaigns.user_id', $user_id)
                                            ->where('user_campaigns.status', 'active')
                                            ->get();

        $user_time_setting = UserTimeSetting::where('user_id', $user_id)->first();

        // print_r($user_campaigns_with_campaign_type->toArray());
        // exit();

        foreach ($user_campaigns_with_campaign_type as $user_campaing) {
            // insert lead campaign id here
            $add_data = array(
                            'lead_id' => $lead_id,
                            'campaign_id' => $user_campaing->campaign_id,
                            'status' => 'active'
                        );
            $add_lead_campaign = LeadCampaign::create($add_data);
            // get messages
            $get_campaign_messages = CampaignMessage::with('rules')->where('campaign_id', $user_campaing->campaign_id)->get();

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
                if($campaign_type == 'cancel') {
                    $real_appointment_datetime = $appointment_datetime;
                    $appointment_datetime = $msg_date_time_created;
                }
                if ($user_campaing->is_reminder == 0) {
                    if ($campaign_message->wait == 'day') {
                        if ($last_msg_time != "") {
                            $dateTime = new \DateTime($last_msg_time);
                            $send_timer = $dateTime->modify('+'.$campaign_message->days.' days');
                            $send_timer = $dateTime->format('Y-m-d '.$campaign_message->delivery_time);
                            $send_timer = date('Y-m-d H:i', strtotime($send_timer));
                        } else {
                            $dateTime = new \DateTime($msg_date_time_created);
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
                            $dateTime = new \DateTime($msg_date_time_created);
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
                            $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                            $send_timer = $dateTime->modify('-'.$user_campaing->before_hours.' hours');
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
                                        'is_reminder' => $user_campaing->is_reminder,
                                        'wait' => $campaign_message->wait,
                                        'status' => 'pending'
                                    );

                if ($campaign_type == 'no_schedule' || $campaign_type == 'cancel') {
                    $add_message_queue = MessageQueue::create($message_queue_data);
                } else {
                    if (strtotime($last_msg_time) >= strtotime($converted_date_time_created) && strtotime($last_msg_time) <= strtotime($appointment_datetime)) {
                        $add_message_queue = MessageQueue::create($message_queue_data);
                    }
                }

                foreach ($campaign_message->rules as $rule) {
                    if ($rule->execute_when == 'is_sent') {
                        $rule_msg = $rule->instant_reply;
                        preg_match_all("/\\[(.*?)\\]/", $rule_msg, $matches);
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
                                    $rule_msg = str_replace("[".$value."]", $custom_field_data, $rule_msg);
                                }
                            }
                        }
                        if ($rule_msg != "") {
                            $message_queue_data = array(
                                                    'lead_id' => $lead_id,
                                                    'user_id' => $user_id,
                                                    'campaign_message_id' => $rule->message_id,
                                                    'message' => $rule_msg,
                                                    'send_timer' => $last_msg_time,
                                                    'is_reminder' => 0,
                                                    'wait' => NULL,
                                                    'status' => 'pending'
                                                );

                            if ($campaign_type == 'no_schedule') {
                                $add_message_queue = MessageQueue::create($message_queue_data);
                            } else {
                                if (strtotime($last_msg_time) >= strtotime($converted_date_time_created) && strtotime($last_msg_time) <= strtotime($appointment_datetime)) {
                                    $add_message_queue = MessageQueue::create($message_queue_data);
                                }
                            }

                            $this->add_new_campaign($rule->removed, $lead_id, $rule->add_to_campaign, $campaign_message->campaign_id);
                        }
                    }
                }
                $last_msg_time = "";
            }
        }
    }
}
