<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;
use App\Models\Appointment;
use App\Models\AppointmentLog;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($lead_id)
    {
        $user_id = Auth::id();
        $lead = Lead::where('id', $lead_id)->where('user_id', $user_id)->first();
        if (!$lead) {
            abort(404);
        }
        $name = $lead->first_name." ".$lead->last_name;
        $phone = $lead->phone;
        $email = $lead->email;
        $appointment_by_lead_id = Appointment::with('lead')->where('lead_id', $lead_id)->get();

        return view('appointments/show', compact('name', 'phone', 'email', 'appointment_by_lead_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $lead_id
     * @return \Illuminate\Http\Response
     */
    public function show($appointment_id)
    {
        $user_id = Auth::id();
        $appointment = Appointment::with('lead')->where('appointment_id', $appointment_id)->where('user_id', $user_id)->first();
        $appointment_logs = AppointmentLog::select('appointment_logs.*', 'appointments.user_id')->join('appointments', 'appointments.appointment_id', '=', 'appointment_logs.appointment_id')->where('appointment_logs.appointment_id', $appointment_id)->where('appointments.user_id', $user_id)->get();
        if (empty($appointment_logs->toArray())) {
            abort(404);
        }
        return view('appointments/detail', compact('appointment', 'appointment_logs'));
    }

    public function log($appointment_id, $id)
    {
        // $appointment_log = AppointmentLog::where('id', $id)->first();
        $user_id = Auth::id();
        $appointment = Appointment::where('appointment_id', $appointment_id)->first();
        if ($user_id != $appointment->user_id) {
            abort(404);
        }
        if(!$appointment){
            abort(404);
        }
        $appointment_log = AppointmentLog::select('appointment_logs.*', 'appointments.user_id')->join('appointments', 'appointments.appointment_id', '=', 'appointment_logs.appointment_id')->where('appointment_logs.id', $id)->where('appointments.user_id', $appointment->user_id)->first();
        if (!$appointment_log) {
            abort(404);
        }
        return view('appointments/log', compact('appointment_log'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
