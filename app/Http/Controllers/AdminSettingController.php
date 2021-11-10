<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Setting;
use App\Models\TimezoneSetting;
use App\Models\User;
use App\Models\UserCampaign;
use App\Models\CampaignCategory;

class AdminSettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => []]);
    }

    /**
     * Show the index
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $admin_id = Auth::id();
        $timezone_settings = TimezoneSetting::where('user_id', $admin_id)->where('type', 'application')->first();
        if (empty($timezone_settings)) {
            $timezone_settings = NULL;
        }
        $timezones = config('view.timezones');
        return view('admin.settings.index', compact('timezone_settings', 'timezones'));
    }

    public function store(Request $request)
    {
        $redirect_url = url('/')."/admin/settings";
        $admin_id = Auth::id();
        $data = array(
                    'type' => 'application',
                    'user_id' => $admin_id,
                    'timezone' => $request['timezone']
                );

        $timezone_settings = TimezoneSetting::where('user_id', $admin_id)->where('type', 'application')->first();

        if (!empty($timezone_settings)) {
            $save_data = TimezoneSetting::where('user_id', $admin_id)->where('type', 'application')->update($data);
        } else {
            $save_data = TimezoneSetting::create($data);
        }

        if ($save_data) {
            $cache_clear = Artisan::call('cache:clear');
            $view_clear = Artisan::call('view:clear');
            $config_cache = Artisan::call('config:cache');
            Session::flash('success_message','Successfully saved');
        }

        return redirect($redirect_url);
    }
}
