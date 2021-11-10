<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Lead;
use App\Models\Message;
use App\Models\MessageQueue;
use App\Models\Setting;
use App\Models\User;
use Twilio\Rest\Client;

class SendQueueMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:queue_messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send queue messages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = date('Y-m-d H:i:00', time());
        echo "current time ".$now."\n\n";
        
        $message_queues = MessageQueue::where('send_timer', '<=', $now)->where('status', 'pending')->orderBy('send_timer', 'asc')->get();

        foreach ($message_queues as $message) {
            $lead = Lead::where('id', $message->lead_id)->first();
            if (empty($lead)) {
                $change_status_id_no_lead = MessageQueue::where('id', $message->id)->update(['status' => 'failed']);
                continue;
            }
            if (isset($message->campaign_message_id)) {
                $campaign_message_id = $message->campaign_message_id;
            } else {
                $campaign_message_id = NULL;
            }
            $send_sms = $this->send_sms($message->lead_id, $message->user_id, $message->message, $campaign_message_id);
            $status = 'sent';
            if ($send_sms == 'invalid phone number' || $send_sms == 'failed') {
                $status = 'failed';
                echo "sms send function status - ".$send_sms."\n";
                echo "message_queues id ".$message->id." - failed to send sms\n\n";
            } elseif ($send_sms == 'success'){
                echo "sms send function status - ".$send_sms."\n";
                echo "message_queues id ".$message->id." - successfully sent sms\n\n";
            }
            $update_status = MessageQueue::where('id', $message->id)->update(['status' => $status]);
        }

        return 0;
    }

    public function send_sms($lead_id, $user_id, $txt_msg, $campaign_message_id)
    {
        $lead = Lead::where('id', $lead_id)->first();
        $user = User::where('id', $user_id)->first();
        $lead_phone = $lead->phone;
        echo "\nphone ".$lead_phone."\n";
        if (!$user) {
            return;
        }
        $user_settings = Setting::where('user_id', $user_id)->first();
        if (!$user_settings) {
            return;
        }

        // Your Account SID and Auth Token from twilio.com/console
        $account_sid = $user_settings->twilio_account_sid;
        $auth_token = $user_settings->twilio_auth_token;

        // A Twilio number you own with SMS capabilities
        $twilio_number = $user_settings->twilio_number;

        // validate phone number
        $url = 'https://lookups.twilio.com/v1/PhoneNumbers/'.urlencode($lead_phone);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_USERPWD, $account_sid.":".$auth_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        
        if ($info['http_code'] == 200) {
            /*$client = new Client($account_sid, $auth_token);

            $message = $client->messages->create(
                // Where to send a text message (your cell phone?)
                $lead_phone,
                array(
                    'from' => $twilio_number,
                    'body' => $txt_msg
                )
            );
            if ($message->sid) {*/
                $message = "";
                $add_data = array(
                                'lead_id' => $lead_id,
                                'user_id' => $user_id,
                                'campaign_message_id' => $campaign_message_id,
                                'message' => $txt_msg,
                                'is_incoming' => 0,
                                'status' => 1,
                                'json_data' => json_encode($message)
                            );
                $add = Message::create($add_data);

                return 'success';
            /*} else {
                return 'failed';
            }*/
        } else {
            return "invalid phone number";
        }
    }
}
