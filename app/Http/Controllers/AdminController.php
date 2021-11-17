<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['get_timer']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('is_admin', '=', '')->orWhereNull('is_admin')->get();
        return view('admin.index', compact('users'));
    }

    public function createUser(Request $request)
    {
        return view('admin.users.create');
    }

    public function saveUser(Request $request)
    {
        // dd($request->all());
        $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => ['required', 'string', 'min:8'],
                    'confirm_password' => ['same:password'],
                ]);

        $identifier = Str::random(9);
        $user_data = array(
                        'identifier' => $identifier,
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->new_password)
                    );

        User::create($user_data);

        Session::flash('success_message','Successfully saved');

        return redirect('admin');
    }

    public function editUser($identifier)
    {
        $user = User::where('identifier', $identifier)->first();
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $identifier)
    {
        $this_user = User::where('identifier', $identifier)->first();
        $user_email = $this_user->email;
        $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => 'required|email|unique:users,email,'.$this_user->id,
                ]);
        $user_data = array(
                        'name' => $request->name,
                        'email' => $request->email,
                    );
        if (isset($request->new_password) && isset($request->confirm_new_password)) {
            $request->validate([
                        'new_password' => ['required', 'string', 'min:8'],
                        'confirm_new_password' => ['same:new_password'],
                    ]);
            $user_data['password'] = Hash::make($request->new_password);
        }

        User::where('identifier', $identifier)->update($user_data);

        Session::flash('success_message','Successfully updated');

        return redirect('admin/user/edit/'.$identifier);
    }

    public function impersonate($id)
    {
        $user = User::find($id);

        // Guard against administrator impersonate
        if(!$user->is_admin == 1)
        {
            Auth::user()->setImpersonating($user->id);
            return redirect('/');
        }
        else
        {
            echo 'Impersonate disabled for this user.';
        }

        return redirect()->back();
    }

    public function stopImpersonate()
    {
        Auth::user()->stopImpersonating();
    }

    public function userCampaignTreeAssign($user_id)
    {
        $user_campaings = UserCampaign::with('campaign_category')->with('campaign_tree')->where('user_id', $user_id)->orderBy('campaign_category_id', 'asc')->orderBy('campaign_tree_id', 'asc')->get();
        $user = User::where('id', $user_id)->first();
        return view('admin.campaign_tree_assign.index', compact('user_campaings', 'user_id', 'user'));
    }

    public function userNewCampaignTreeAssign($user_id)
    {
        $categories = CampaignCategory::where('status', 'active')->get();
        $user = User::where('id', $user_id)->first();
        return view('admin.campaign_tree_assign.create', compact('categories', 'user_id'));
    }

    public function userSaveCampaignTree(Request $request)
    {
        $check = UserCampaign::where('user_id', $request['user_id'])->where('campaign_category_id', $request['campaign_category_id'])->where('campaign_tree_id', $request['campaign_tree_id'])->first();
        if (!$check) {
            $inactive_prev_records = UserCampaign::where('user_id', $request['user_id'])->update(['status' => 'inactive']);

            $data = array(
                        'user_id' => $request['user_id'],
                        'campaign_category_id' => $request['campaign_category_id'],
                        'campaign_tree_id' => $request['campaign_tree_id'],
                        'status' => 'active',
                    );
            $add_user_campaign = UserCampaign::create($data);
        }
        return redirect('/admin/user/campaign_tree_assign/'.$request['user_id']);
    }

    public function userEditCampaignTree($id)
    {
        $user_campaing = UserCampaign::with('campaign_category')->with('campaign_tree')->where('id', $id)->first();
        return view('admin.campaign_tree_assign.edit', compact('user_campaing'));
    }

    public function userUpdateCampaignTree(Request $request, $id)
    {
        $user_campaing = UserCampaign::where('id', $id)->first();
        if ($request['status'] == 'active') {
            $inactive_prev_records = UserCampaign::where('user_id', $user_campaing->user_id)->whereNotIn('id', [$id])->update(['status' => 'inactive']);
        }
        $change_status = UserCampaign::where('id', $id)->update(['status' => $request['status']]);
        return redirect('/admin/user/campaign_tree_assign/'.$user_campaing->user_id);
    }

    public function get_timer()
    {
        echo $timestamp = date('h:i A');
    }
}
