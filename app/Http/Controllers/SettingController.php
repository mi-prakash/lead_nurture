<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use App\Models\User;


class SettingController extends Controller
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

    public function index()
    {
        $user_id = Auth::id();
        $user = User::where('id', $user_id)->first();
        $user_settings = Setting::where('user_id', $user_id)->first();
        return view('settings.index', compact('user', 'user_settings'));
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
        $params = $request->all();
        $user_id = Auth::id();
        $user = User::where('id', $user_id)->first();
        $base_url = url('/');
        $click_funnel_webhook_url = $base_url.'/funnel_webhooks/test/get_lead/'.$user->identifier;
        $acuity_webhook_url = $base_url.'/home/webhook_new_appointment/'.$user->identifier;
        $twilio_webhook_url = $base_url.'/home/messages/get_sms/'.$user->identifier;
        
        $setting_data = array(
                            'user_id' => $user_id,
                            'click_funnel_email' => $params['click_funnel_email'],
                            'click_funnel_api_key' => $params['click_funnel_api_key'],
                            'click_funnel_webhook_url' => $click_funnel_webhook_url,
                            'click_funnel_name' => $params['click_funnel_name'],
                            'click_funnel_id' => $params['click_funnel_id'],
                            'acuity_user_id' => $params['acuity_user_id'],
                            'acuity_api_key' => $params['acuity_api_key'],
                            'acuity_webhook_url' => $acuity_webhook_url,
                            'acuity_calendar_name' => $params['acuity_calendar_name'],
                            'acuity_calendar_id' => $params['acuity_calendar_id'],
                            'twilio_account_sid' => $params['twilio_account_sid'],
                            'twilio_auth_token' => $params['twilio_auth_token'],
                            'twilio_number' => $params['twilio_number'],
                            'twilio_webhook_url' => $twilio_webhook_url
                        );
        $update_or_create_settings = Setting::updateOrCreate(['user_id' => $user_id], $setting_data);

        Session::flash('success_message','Successfully saved');

        return redirect('/home/integrations');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
