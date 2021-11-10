<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\CampaignCategory;
use App\Models\CampaignTree;
use App\Models\CustomField;
use App\Models\Campaign;
use App\Models\CampaignTrigger;

class CampaignTriggerController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $campaign_category_id = $request['campaign_category_id'];
        $campaign_tree_id = $request['campaign_tree_id'];
        $categories = CampaignCategory::where('status', 'active')->get();
        $campaign_trees = CampaignTree::where('status', 'active')->where('campaign_category_id', $campaign_category_id)->get();
        $campaign_category = CampaignCategory::where('id', $campaign_category_id)->first();
        $campaign_tree = CampaignTree::where('id', $campaign_tree_id)->first();
        $campaigns = Campaign::where('campaign_tree_id', $campaign_tree_id)->get();
        $no_schedules = CampaignTrigger::where('type', 'no_schedule')->where('campaign_category_id', $campaign_category_id)->where('campaign_tree_id', $campaign_tree_id)->get();
        $schedules = CampaignTrigger::where('type', 'schedule')->where('campaign_category_id', $campaign_category_id)->where('campaign_tree_id', $campaign_tree_id)->get();
        $re_schedules = CampaignTrigger::where('type', 're_schedule')->where('campaign_category_id', $campaign_category_id)->where('campaign_tree_id', $campaign_tree_id)->get();
        $cancels = CampaignTrigger::where('type', 'cancel')->where('campaign_category_id', $campaign_category_id)->where('campaign_tree_id', $campaign_tree_id)->get();
        $no_shows = CampaignTrigger::where('type', 'no_show')->where('campaign_category_id', $campaign_category_id)->where('campaign_tree_id', $campaign_tree_id)->get();

        return View('admin.campaign_triggers.index', compact('categories', 'campaign_trees', 'campaign_category', 'campaign_tree', 'campaign_category_id', 'campaign_tree_id', 'campaigns', 'no_schedules', 'schedules', 're_schedules', 'cancels', 'no_shows'));
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
        // no_schedule, schedule, re_schedule, cancel, no_show
        $campaign_category_id = $request['campaign_category_id'];
        $campaign_tree_id = $request['campaign_tree_id'];

        // delete previous settings
        $delete = CampaignTrigger::where('campaign_category_id', $campaign_category_id)->where('campaign_tree_id', $campaign_tree_id)->delete();
        if (isset($request['no_schedule_campaign'])) {
            foreach ($request['no_schedule_campaign'] as $campaign_id) {
                if (!empty($campaign_id)) {
                    $no_schedule_data = array(
                                            'campaign_category_id' => $campaign_category_id,
                                            'campaign_tree_id' => $campaign_tree_id,
                                            'type' => 'no_schedule',
                                            'campaign_id' => $campaign_id
                                        );
                    $no_schedule_create = CampaignTrigger::create($no_schedule_data);
                }
            }
        }
        if (isset($request['schedule_campaign'])) {
            foreach ($request['schedule_campaign'] as $campaign_id) {
                if (!empty($campaign_id)) {
                    $schedule_data = array(
                                            'campaign_category_id' => $campaign_category_id,
                                            'campaign_tree_id' => $campaign_tree_id,
                                            'type' => 'schedule',
                                            'campaign_id' => $campaign_id
                                        );
                    $schedule_create = CampaignTrigger::create($schedule_data);
                }
            }
        }
        if (isset($request['re_schedule_campaign'])) {
            foreach ($request['re_schedule_campaign'] as $campaign_id) {
                if (!empty($campaign_id)) {
                    $re_schedule_data = array(
                                            'campaign_category_id' => $campaign_category_id,
                                            'campaign_tree_id' => $campaign_tree_id,
                                            'type' => 're_schedule',
                                            'campaign_id' => $campaign_id
                                        );
                    $re_schedule_create = CampaignTrigger::create($re_schedule_data);
                }
            }
        }
        if (isset($request['cancel_campaign'])) {
            foreach ($request['cancel_campaign'] as $campaign_id) {
                if (!empty($campaign_id)) {
                    $cancel_data = array(
                                            'campaign_category_id' => $campaign_category_id,
                                            'campaign_tree_id' => $campaign_tree_id,
                                            'type' => 'cancel',
                                            'campaign_id' => $campaign_id
                                        );
                    $cancel_create = CampaignTrigger::create($cancel_data);
                }
            }
        }
        if (isset($request['no_show_campaign'])) {
            foreach ($request['no_show_campaign'] as $campaign_id) {
                if (!empty($campaign_id)) {
                    $no_show_data = array(
                                            'campaign_category_id' => $campaign_category_id,
                                            'campaign_tree_id' => $campaign_tree_id,
                                            'type' => 'no_show',
                                            'campaign_id' => $campaign_id
                                        );
                    $no_show_create = CampaignTrigger::create($no_show_data);
                }
            }
        }
        Session::flash('success_message','Successfully updated');

        return redirect('/admin/campaign_trigger?campaign_category_id='.$campaign_category_id.'&campaign_tree_id='.$campaign_tree_id);
    }
}
