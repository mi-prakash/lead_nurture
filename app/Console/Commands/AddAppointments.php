<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use Twilio\Rest\Client;

class AddAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:add_appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add appointments from the log table to appointments table';

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
        $appointment_logs = AppointmentLog::where('is_adjusted', 0)->get();
        foreach ($appointment_logs as $appointment_log) {
            $check_appointment = Appointment::where('appointment_id', $appointment_log->appointment_id)->first();
            if (empty($check_appointment)) {
                $appointment = json_decode($appointment_log->json_data, true);
                $lead = Lead::where('id', $appointment['lead_id'])->first();
                $appointment_data = array(
                                        'appointment_id' => $appointment['appointment_id'],
                                        'user_id' => $appointment['user_id'],
                                        'lead_id' => $appointment['lead_id'],
                                        'date' => $appointment['date'],
                                        'date_time' => $appointment['date_time'],
                                        'time' => $appointment['time'],
                                        'end_time' => $appointment['end_time'],
                                        'converted_time' => $appointment['converted_time'],
                                        'converted_end_time' => $appointment['converted_end_time'],
                                        'date_created' => $appointment['date_created'],
                                        'date_time_created' => $appointment['date_time_created'],
                                        'price' => $appointment['price'],
                                        'price_sold' => $appointment['price_sold'],
                                        'paid' => $appointment['paid'],
                                        'amount_paid' => $appointment['amount_paid'],
                                        'type' => $appointment['type'],
                                        'appointment_type_id' => $appointment['appointment_type_id'],
                                        'calendar_id' => $appointment['calendar_id'],
                                        'timezone' => $appointment['timezone'],
                                        'calendar_timezone' => $appointment['calendar_timezone'],
                                        'canceled' => $appointment['canceled'],
                                        'can_client_cancel' => $appointment['can_client_cancel'],
                                        'can_client_reschedule' => $appointment['can_client_reschedule'],
                                        'status' => $appointment['status'],
                                        'json_data' => $appointment['json_data']
                                    );

                $appointment_insert = Appointment::create($appointment_data);
                if ($appointment_insert = true) {
                    $update_appointment_log = AppointmentLog::where('id', $appointment_log->id)->update(['is_adjusted' => 1]);

                    echo "appointment_id ".$appointment['appointment_id']." - success\n";
                }
            }
            echo "\n\n";
        }
        return 0;
    }
}
