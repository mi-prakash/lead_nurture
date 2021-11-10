<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\CampaignMessage;
use App\Models\CampaignTree;
use App\Models\Rule;

class CampaignCategoryController extends Controller
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
    public function index()
    {
        $campaign_categories = CampaignCategory::with('campaign_trees')->get();
        return view('admin.campaign_categories.index', compact('campaign_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.campaign_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
                            'name' => 'required|unique:campaign_categories'
                        ]);

        $data = array(
                    'name' => $request['name'],
                    'status' => 'active'
                );
        $create = CampaignCategory::create($data);
        if ($create) {
            Session::flash('success_message','Successfully saved');
        }

        return redirect('/admin/campaign/categorytree');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CampaignCategory  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $campaign_category = CampaignCategory::where('id', $id)->first();
        return view('admin.campaign_categories.edit', compact('campaign_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CampaignCategory  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
                            'name' => 'required|unique:campaign_categories,name,'.$id
                        ]);

        $data = array(
                    'name' => $request['name'],
                    'status' => $request['status']
                );
        $update = CampaignCategory::where('id', $id)->update($data);
        if ($update) {
            Session::flash('success_message','Successfully updated');
        }

        return redirect('/admin/campaign/categorytree');
    }

    public function copy($campaign_tree_id)
    {
        $categories = CampaignCategory::where('status', 'active')->get();
        $campaign_tree = CampaignTree::where('id', $campaign_tree_id)->first();
        return view('admin.campaign_categories.copy', compact('categories', 'campaign_tree_id', 'campaign_tree'));
    }

    public function save_copy(Request $request, $campaign_tree_id)
    {
        // print_r($request->all());
        $to_category_id = $request['to_category_id'];

        if (isset($request['to_new_tree']) && !empty($request['to_new_tree'])) {
            $data = array(
                        'campaign_category_id' => $request['to_category_id'],
                        'name' => $request['to_new_tree'],
                        'status' => 'active'
                    );
            $create_campaign_tree = CampaignTree::create($data);
            $to_tree_id = $create_campaign_tree->id;
        } else {
            $to_tree_id = $request['to_tree_id'];
        }

        $copy_campaigns = Campaign::where('campaign_tree_id', $campaign_tree_id)->get();

        // remove prev campaign
        $remove_prev_campaigns = Campaign::where('campaign_tree_id', $to_tree_id)->delete();

        // copy from tree -> campaigns, messages, rules
        foreach ($copy_campaigns as $campaign) {
            $campaign_data = array(
                                'campaign_tree_id' => $to_tree_id,
                                'name' => $campaign->name,
                                'description' => $campaign->description,
                                'is_reminder' => $campaign->is_reminder,
                                'before_hours' => $campaign->before_hours
                            );
            $add_campaign = Campaign::create($campaign_data);
        }
        foreach ($copy_campaigns as $campaign) {
            // check messages
            $campaign_messages = CampaignMessage::where('campaign_id', $campaign->id)->get();
            foreach ($campaign_messages as $campaign_message) {
                $get_cpy_campaign = Campaign::where('id', $campaign->id)->first();
                $get_new_campaign = Campaign::where('campaign_tree_id', $to_tree_id)->where('name', $get_cpy_campaign->name)->first();
                $message_data = array(
                                    'campaign_id' => $get_new_campaign->id,
                                    'wait' => $campaign_message->wait,
                                    'days' => $campaign_message->days,
                                    'delivery_time' => $campaign_message->delivery_time,
                                    'time' => $campaign_message->time,
                                    'name' => $campaign_message->name,
                                    'body' => $campaign_message->body,
                                    'media_url' => $campaign_message->media_url,
                                    'ordering' => $campaign_message->ordering
                                );
                $add_message = CampaignMessage::create($message_data);

                // check rules
                $rules = Rule::where('message_id', $campaign_message->id)->get();
                foreach ($rules as $rule) {
                    $add_to_campaign = NULL;
                    if (!empty($rule->add_to_campaign)) {
                        $get_campaign = Campaign::where('id', $rule->add_to_campaign)->first();
                        $new_campaign = Campaign::where('campaign_tree_id', $to_tree_id)->where('name', $get_campaign->name)->first();
                        if (!empty($new_campaign)) {
                            $add_to_campaign = $new_campaign->id;
                        }
                    }
                    $rule_data = array(
                                    'message_id' => $add_message->id,
                                    'execute_when' => $rule->execute_when,
                                    'removed' => $rule->removed,
                                    'add_to_campaign' => $add_to_campaign,
                                    'expression_value' => $rule->expression_value,
                                    'category' => $rule->category,
                                    'instant_reply' => $rule->instant_reply,
                                    'ordering' => $rule->ordering
                                    
                                );
                    $add_rule = Rule::create($rule_data);
                }
            }
        }

        if ($add_campaign) {
            Session::flash('success_message','Successfully copied');
        }

        return redirect('/admin/campaign/categorytree');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CampaignCategory  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = CampaignCategory::where('id', $id)->delete();
        $delete_tree = CampaignTree::where('campaign_category_id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }
}
