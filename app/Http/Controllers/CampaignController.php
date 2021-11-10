<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\CampaignCategory;
use App\Models\CampaignTree;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CustomField;
use App\Models\MessageRuleCategory;
use App\Models\Rule;

class CampaignController extends Controller
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
    public function index($campaign_tree_id = "")
    {
        if (empty($campaign_tree_id)) {
            return false;
        } else {
            $campaigns = Campaign::where('campaign_tree_id', $campaign_tree_id)->get();

            return view('admin.campaign.index', compact('campaigns', 'campaign_tree_id'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($campaign_tree_id)
    {
        $campaign_tree = CampaignTree::where('id', $campaign_tree_id)->first();
        $campaign_category = CampaignCategory::where('id', $campaign_tree->campaign_category_id)->first();
        $system_placeholders = CustomField::where('type', 'system')->get();
        $category_placeholders = CustomField::where('type', 'category')->where('campaign_category_id', $campaign_tree->campaign_category_id)->get();
        $campaign_tree_placeholders = CustomField::where('type', 'campaign_tree')->where('campaign_tree_id', $campaign_tree_id)->get();
        $rule_campaigns = Campaign::where('campaign_tree_id', $campaign_tree->id)->get();
        $message_rule_categories = MessageRuleCategory::get();

        return view('admin.campaign.create', compact('campaign_category', 'campaign_tree', 'system_placeholders', 'category_placeholders', 'campaign_tree_placeholders', 'rule_campaigns', 'message_rule_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $campaign_tree_id)
    {
        $check_name = Campaign::where('name', $request['campaign_name'])->where('campaign_tree_id', $campaign_tree_id)->first();

        if (!empty($check_name)) {
            Session::flash('error_message','Name already exist!');
            return redirect('/admin/campaign/new/'.$campaign_tree_id);
        }

        $campaign_data = array(
                            'campaign_tree_id' => $campaign_tree_id,
                            'name' => $request['campaign_name'],
                            'description' => $request['campaign_description'],
                            'is_reminder' => isset($request['is_reminder']) ? 1 : 0,
                            'before_hours' => $request['before_hours']
                        );
        $add_campaign = Campaign::create($campaign_data);
        $campaign_id = $add_campaign->id;
        $x = 0;
        if (isset($request['wait'])) {
            foreach ($request['wait'] as $row) {
                $message_data = array(
                                    'campaign_id' => $campaign_id,
                                    'wait' => $request['wait'][$x],
                                    'days' => empty($request['days'][$x]) ? 0 : $request['days'][$x],
                                    'delivery_time' => empty($request['delivery_time'][$x]) ? '00:00:00' : $request['delivery_time'][$x],
                                    'time' => empty($request['time'][$x]) ? 0 : $request['time'][$x],
                                    'name' => $request['name'][$x],
                                    'body' => $request['body'][$x],
                                    'media_url' => $request['media_url'][$x],
                                    'ordering' => $x+1
                                );
                $add_message = CampaignMessage::create($message_data);
                $i = 0;
                $rule_order = 1;
                if (isset($request['execute_when'][$request['msg_index'][$x]])) {
                    foreach ($request['execute_when'][$request['msg_index'][$x]] as $value) {
                        $execute_when = $request['execute_when'][$request['msg_index'][$x]][$i];
                        $rule_data = array(
                                        'message_id' => $add_message->id,
                                        'execute_when' => $execute_when,
                                        'removed' => $request['removed'][$request['msg_index'][$x]][$i],
                                        'add_to_campaign' => $request['add_to_campaign'][$request['msg_index'][$x]][$i],
                                        'expression_value' => ($execute_when == 'reply_with_expression') ? $request['expression_value'][$request['msg_index'][$x]][$i] : NULL,
                                        'category' => ($execute_when == 'reply_with_category') ? $request['category'][$request['msg_index'][$x]][$i] : NULL,
                                        'instant_reply' => $request['instant_reply'][$request['msg_index'][$x]][$i],
                                        'ordering' => $rule_order
                                        
                                    );
                        $add_rule = Rule::create($rule_data);
                        $i++;
                        $rule_order++;
                    }
                }
                $x++;
            }
        }
        Session::flash('success_message','Successfully saved');

        return redirect('/admin/campaign/edit/'.$add_campaign->id);
        // return redirect('/admin/campaign/'.$campaign_tree_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $campaign = Campaign::where('id', $id)->first();
        $messages = CampaignMessage::with('rules')->where('campaign_id', $id)->orderBy('ordering')->get();
        $campaign_tree = CampaignTree::where('id', $campaign->campaign_tree_id)->first();
        $campaign_category = CampaignCategory::where('id', $campaign_tree->campaign_category_id)->first();
        $rule_campaigns = Campaign::where('campaign_tree_id', $campaign_tree->id)->get();
        $message_rule_categories = MessageRuleCategory::get();
        $system_placeholders = CustomField::where('type', 'system')->get();
        $category_placeholders = CustomField::where('type', 'category')->where('campaign_category_id', $campaign_tree->campaign_category_id)->get();
        $campaign_tree_placeholders = CustomField::where('type', 'campaign_tree')->where('campaign_tree_id', $campaign->campaign_tree_id)->get();

        return view('admin.campaign.edit', compact('campaign_category', 'campaign_tree', 'campaign', 'messages', 'system_placeholders', 'category_placeholders', 'campaign_tree_placeholders', 'rule_campaigns', 'message_rule_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $campaign_tree_id)
    {
        $check_name = Campaign::where('name', $request['campaign_name'])->where('id', '!=', $id)->where('campaign_tree_id', $campaign_tree_id)->first();

        if (!empty($check_name)) {
            Session::flash('error_message','Name already exist!');
            return redirect('/admin/campaign/edit/'.$id);
        }

        $campaign_data = array(
                            'campaign_tree_id' => $campaign_tree_id,
                            'name' => $request['campaign_name'],
                            'description' => $request['campaign_description'],
                            'is_reminder' => isset($request['is_reminder']) ? 1 : 0,
                            'before_hours' => isset($request['is_reminder']) ? $request['before_hours'] : NULL
                        );
        $add_campaign = Campaign::where('id', $id)->update($campaign_data);
        $x = 0;
        $order = 1;
        if (isset($request['wait'])) {
            foreach ($request['wait'] as $row) {
                $message_data = array(
                                    'campaign_id' => $id,
                                    'wait' => $request['wait'][$x],
                                    'days' => empty($request['days'][$x]) ? 0 : $request['days'][$x],
                                    'delivery_time' => empty($request['delivery_time'][$x]) ? '00:00:00' : $request['delivery_time'][$x],
                                    'time' => empty($request['time'][$x]) ? 0 : $request['time'][$x],
                                    'name' => $request['name'][$x],
                                    'body' => $request['body'][$x],
                                    'media_url' => $request['media_url'][$x],
                                    'ordering' => $order
                                );
                if ($request['is_old'][$x] == 0) {
                    $add_message = CampaignMessage::create($message_data);
                    $i = 0;
                    $rule_order = 1;
                    if (isset($request['execute_when'][$request['msg_index'][$x]])) {
                        foreach ($request['execute_when'][$request['msg_index'][$x]] as $value) {
                            $execute_when = $request['execute_when'][$request['msg_index'][$x]][$i];
                            $rule_data = array(
                                            'message_id' => $add_message->id,
                                            'execute_when' => $execute_when,
                                            'removed' => $request['removed'][$request['msg_index'][$x]][$i],
                                            'add_to_campaign' => $request['add_to_campaign'][$request['msg_index'][$x]][$i],
                                            'expression_value' => ($execute_when == 'reply_with_expression') ? $request['expression_value'][$request['msg_index'][$x]][$i] : NULL,
                                            'category' => ($execute_when == 'reply_with_category') ? $request['category'][$request['msg_index'][$x]][$i] : NULL,
                                            'instant_reply' => $request['instant_reply'][$request['msg_index'][$x]][$i],
                                            'ordering' => $rule_order
                                            
                                        );
                            $add_rule = Rule::create($rule_data);
                            $i++;
                            $rule_order++;
                        }
                    }
                } else {
                    $update_message = CampaignMessage::where('id', $request['is_old'][$x])->update($message_data);
                    $i = 0;
                    $rule_order = 1;
                    if (isset($request['execute_when'][$request['msg_index'][$x]])) {
                        foreach ($request['execute_when'][$request['msg_index'][$x]] as $value) {
                            $execute_when = $request['execute_when'][$request['msg_index'][$x]][$i];
                            $rule_data = array(
                                            'message_id' => $request['is_old'][$x],
                                            'execute_when' => $execute_when,
                                            'removed' => $request['removed'][$request['msg_index'][$x]][$i],
                                            'add_to_campaign' => $request['add_to_campaign'][$request['msg_index'][$x]][$i],
                                            'expression_value' => ($execute_when == 'reply_with_expression') ? $request['expression_value'][$request['msg_index'][$x]][$i] : NULL,
                                            'category' => ($execute_when == 'reply_with_category') ? $request['category'][$request['msg_index'][$x]][$i] : NULL,
                                            'instant_reply' => $request['instant_reply'][$request['msg_index'][$x]][$i],
                                            'ordering' => $rule_order
                                            
                                        );
                            if ($request['is_old_rule'][$request['msg_index'][$x]][$i] == 0) {
                                $add_rule = Rule::create($rule_data);
                            } else {
                                $update_rule = Rule::where('id', $request['is_old_rule'][$request['msg_index'][$x]][$i])->update($rule_data);
                            }
                            $i++;
                            $rule_order++;
                        }
                    }
                }
                $x++;
                $order++;
            }
        }
        Session::flash('success_message','Successfully updated');

        return redirect('/admin/campaign/edit/'.$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = Campaign::where('id', $id)->delete();
        // delete messages & rules
        $messages = CampaignMessage::where('campaign_id', $id)->get();
        foreach ($messages as $message) {
            $delete_rule = Rule::where('message_id', $message->id)->delete();
        }
        $delete_messages = CampaignMessage::where('campaign_id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }

    public function messageDelete($id)
    {
        $delete = CampaignMessage::where('id', $id)->delete();
        $delete_rule = Rule::where('message_id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }

    public function ruleDelete($id)
    {
        $delete = Rule::where('id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }
}
