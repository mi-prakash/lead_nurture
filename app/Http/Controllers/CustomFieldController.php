<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\CampaignCategory;
use App\Models\CampaignTree;
use App\Models\CustomField;
use App\Models\UserCampaign;
use App\Models\UserCustomField;
use App\Models\UserTimeSetting;

class CustomFieldController extends Controller
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
        $system_fields = CustomField::where('type', 'system')->get();
        return View('admin.custom_fields.index', compact('system_fields'));
    }

    public function categoryIndex(Request $request)
    {
        $campaign_category_id = $request['campaign_category_id'];
        $categories = CampaignCategory::where('status', 'active')->get();
        $campaign_category = CampaignCategory::where('id', $campaign_category_id)->first();
        $data_fields = CustomField::where('type', 'category')->where('campaign_category_id', $campaign_category_id)->get();

        return View('admin.custom_fields.category', compact('categories', 'campaign_category', 'data_fields', 'campaign_category_id'));
    }

    public function categoryCreate($campaign_category_id)
    {
        return View('admin.custom_fields.add_category', compact('campaign_category_id'));
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
                            'name' => 'required'
                        ]);

        $check_name = CustomField::where('name', $request['name'])->where('campaign_category_id', $request['campaign_category_id'])->where('campaign_tree_id', NULL)->first();

        if (!empty($check_name)) {
            Session::flash('error_message','The name has already been taken.');
        } else {
            $data = array(
                        'type' => 'category',
                        'campaign_category_id' => $request['campaign_category_id'],
                        'name' => $request['name'],
                        'value' => $request['value']
                    );
            $create = CustomField::create($data);
            if ($create) {
                Session::flash('success_message','Successfully saved');
            }
        }

        return redirect('/admin/custom_fields/category?campaign_category_id='.$request['campaign_category_id']);
    }

    public function categoryEdit($id)
    {
        $custom_field = CustomField::where('id', $id)->first();
        return View('admin.custom_fields.edit_category', compact('custom_field'));

    }

    public function categoryUpdate(Request $request, $id)
    {
        $request->validate([
                            'name' => 'required'
                        ]);

        $check_name = CustomField::where('name', $request['name'])->where('campaign_category_id', $request['campaign_category_id'])->where('campaign_tree_id', NULL)->where('id', '!=', $id)->first();

        if (!empty($check_name)) {
            Session::flash('error_message','The name has already been taken.');
        } else {
            $data = array(
                        'type' => 'category',
                        'campaign_category_id' => $request['campaign_category_id'],
                        'name' => $request['name'],
                        'value' => $request['value']
                    );
            $update = CustomField::where('id', $id)->update($data);
            if ($update) {
                Session::flash('success_message','Successfully updated');
            }
        }

        return redirect('/admin/custom_fields/category?campaign_category_id='.$request['campaign_category_id']);
    }

    public function categoryDelete($id)
    {
        $delete = CustomField::where('id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }

    public function campaignTreeIndex(Request $request)
    {
        $campaign_category_id = $request['campaign_category_id'];
        $campaign_tree_id = $request['campaign_tree_id'];
        $categories = CampaignCategory::where('status', 'active')->get();
        $campaign_trees = CampaignTree::where('status', 'active')->where('campaign_category_id', $campaign_category_id)->get();
        $campaign_category = CampaignCategory::where('id', $campaign_category_id)->first();
        $campaign_tree = CampaignTree::where('id', $campaign_tree_id)->first();
        $data_fields = CustomField::where('type', 'campaign_tree')->where('campaign_category_id', $campaign_category_id)->where('campaign_tree_id', $campaign_tree_id)->get();
        return View('admin.custom_fields.campaign_tree', compact('categories', 'campaign_trees', 'campaign_category', 'campaign_tree', 'data_fields', 'campaign_category_id', 'campaign_tree_id'));
    }

    public function getCampaignTree($campaign_category_id)
    {
        $campaign_trees = CampaignTree::where('campaign_category_id', $campaign_category_id)->get();
        echo "<option value=''>Select Campaign Tree</option>";
        foreach ($campaign_trees as $campaign_tree) {
            echo "<option value='".$campaign_tree->id."'>".$campaign_tree->name."</option>";
        }
    }

    public function campaignTreeCreate($campaign_category_id, $campaign_tree_id)
    {
        return View('admin.custom_fields.add_campaign_tree', compact('campaign_category_id', 'campaign_tree_id'));
    }

    public function campaignTreeStore(Request $request)
    {
        $request->validate([
                            'name' => 'required'
                        ]);

        $check_name = CustomField::where('name', $request['name'])->where('campaign_category_id', $request['campaign_category_id'])->where('campaign_tree_id', $request['campaign_tree_id'])->first();

        if (!empty($check_name)) {
            Session::flash('error_message','The name has already been taken.');
        } else {
            $data = array(
                        'type' => 'campaign_tree',
                        'campaign_category_id' => $request['campaign_category_id'],
                        'campaign_tree_id' => $request['campaign_tree_id'],
                        'name' => $request['name'],
                        'value' => $request['value']
                    );
            $create = CustomField::create($data);
            if ($create) {
                Session::flash('success_message','Successfully saved');
            }
        }

        return redirect('/admin/custom_fields/campaign_tree?campaign_category_id='.$request['campaign_category_id'].'&campaign_tree_id='.$request['campaign_tree_id']);
    }

    public function campaignTreeEdit($id)
    {
        $custom_field = CustomField::where('id', $id)->first();
        return View('admin.custom_fields.edit_campaign_tree', compact('custom_field'));

    }

    public function campaignTreeUpdate(Request $request, $id)
    {
        $request->validate([
                            'name' => 'required'
                        ]);
        
        $check_name = CustomField::where('name', $request['name'])->where('campaign_category_id', $request['campaign_category_id'])->where('campaign_tree_id', $request['campaign_tree_id'])->where('id', '!=', $id)->first();

        if (!empty($check_name)) {
            Session::flash('error_message','The name has already been taken.');
        } else { 
            $data = array(
                        'type' => 'campaign_tree',
                        'campaign_category_id' => $request['campaign_category_id'],
                        'campaign_tree_id' => $request['campaign_tree_id'],
                        'name' => $request['name'],
                        'value' => $request['value']
                    );
            $update = CustomField::where('id', $id)->update($data);
            if ($update) {
                Session::flash('success_message','Successfully updated');
            }
        }

        return redirect('/admin/custom_fields/campaign_tree?campaign_category_id='.$request['campaign_category_id'].'&campaign_tree_id='.$request['campaign_tree_id']);
    }

    public function campaignTreeDelete($id)
    {
        $delete = CustomField::where('id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }

    public function userCategoryIndex()
    {
        $user_id = Auth::id();
        $user_campaing = UserCampaign::where('user_id', $user_id)->where('status', 'active')->first();

        if ($user_campaing) {
            $campaign_category_id = $user_campaing->campaign_category_id;
            $campaign_tree_id = $user_campaing->campaign_tree_id;
            $categories = CampaignCategory::where('status', 'active')->get();
            $campaign_trees = CampaignTree::where('status', 'active')->where('campaign_category_id', $campaign_category_id)->get();
            $campaign_category = CampaignCategory::where('id', $campaign_category_id)->first();
            $campaign_tree = CampaignTree::where('id', $campaign_tree_id)->first();

            $data_fields = DB::select("SELECT
                                            custom_fields.*,
                                            user_custom_fields.id AS user_custom_fields_id,
                                            user_custom_fields.value AS user_value,
                                            user_custom_fields.data_type AS user_data_type
                                        FROM custom_fields
                                        LEFT JOIN user_custom_fields ON user_custom_fields.custom_field_id = custom_fields.id
                                        WHERE custom_fields.type = 'category'
                                        AND custom_fields.campaign_category_id = $campaign_category_id
                                        AND (user_custom_fields.user_id = $user_id OR user_custom_fields.user_id IS NULL)");

            return View('custom_fields.category_index', compact('categories', 'campaign_trees', 'campaign_category', 'campaign_tree', 'data_fields', 'campaign_category_id', 'campaign_tree_id'));
        } else {
            abort(404);
        }
    }

    public function userIndex()
    {
        $user_id = Auth::id();
        $user_campaing = UserCampaign::where('user_id', $user_id)->where('status', 'active')->first();

        if ($user_campaing) {
            $campaign_category_id = $user_campaing->campaign_category_id;
            $campaign_tree_id = $user_campaing->campaign_tree_id;
            $categories = CampaignCategory::where('status', 'active')->get();
            $campaign_trees = CampaignTree::where('status', 'active')->where('campaign_category_id', $campaign_category_id)->get();
            $campaign_category = CampaignCategory::where('id', $campaign_category_id)->first();
            $campaign_tree = CampaignTree::where('id', $campaign_tree_id)->first();

            $data_fields = DB::select("SELECT
                                            custom_fields.*,
                                            user_custom_fields.id AS user_custom_fields_id,
                                            user_custom_fields.value AS user_value,
                                            user_custom_fields.data_type AS user_data_type
                                        FROM custom_fields
                                        LEFT JOIN user_custom_fields ON user_custom_fields.custom_field_id = custom_fields.id
                                        WHERE custom_fields.type = 'campaign_tree'
                                        AND custom_fields.campaign_category_id = $campaign_category_id
                                        AND custom_fields.campaign_tree_id = $campaign_tree_id
                                        AND (user_custom_fields.user_id = $user_id OR user_custom_fields.user_id IS NULL)");

            return View('custom_fields.index', compact('categories', 'campaign_trees', 'campaign_category', 'campaign_tree', 'data_fields', 'campaign_category_id', 'campaign_tree_id'));
        } else {
            abort(404);
        }
    }

    public function userCampaignTreeEdit($id, $user_custom_field_id)
    {
        $custom_field = CustomField::where('id', $id)->first();
        $user_custom_field = NULL;
        if ($user_custom_field_id != 0) {
            $user_custom_field = UserCustomField::where('id', $user_custom_field_id)->where('status', 'active')->first();
        }
        return View('custom_fields.edit_campaign_tree', compact('custom_field', 'user_custom_field'));

    }

    public function userCampaignTreeUpdate(Request $request, $id)
    {
        $user_id = Auth::id();
        if ($id != 0) {
            $data = array(
                        'value' => $request['value']
                    );
            $user_custom_field = UserCustomField::where('id', $request['user_custom_field_id'])->update($data);
        } else {
            $data = array(
                        'user_id' => $user_id,
                        'custom_field_id' => $request['custom_field_id'],
                        'value' => $request['value'],
                        'data_type' => 'text'
                    );
            $user_custom_field = UserCustomField::create($data);
        }
        if ($user_custom_field) {
            Session::flash('success_message','Successfully updated');
        }

        $custom_field = CustomField::where('id', $request['custom_field_id'])->first();

        $type = $custom_field->type;

        if ($type == 'category') {
            return redirect('/home/custom_fields/category');
        } elseif ($type == 'campaign_tree') {
            return redirect('/home/custom_fields/campaign_tree');
        } else {
            return redirect('/home/custom_fields/category');
        }
    }

    public function timeSettings()
    {
        $user_id = Auth::id();
        $user_campaing = UserCampaign::where('user_id', $user_id)->where('status', 'active')->first();

        if ($user_campaing) {
            $user_time_setting = UserTimeSetting::where('user_id', $user_id)->first();

            return View('custom_fields.time_settings', compact('user_time_setting'));
        } else {
            abort(404);
        }
    }

    public function saveUserTimeSettings(Request $request)
    {
        $user_id = Auth::id();

        $from_time = date("H:i:s", strtotime($request['from_time']));
        $to_time = date("H:i:s", strtotime($request['to_time']));

        $update_or_create = UserTimeSetting::updateOrCreate(
                                ['user_id' => $user_id],
                                ['user_id' => $user_id, 'from_time' => $from_time, 'to_time' => $to_time]
                            );

        if ($update_or_create) {
            Session::flash('success_message','Successfully updated');
        }

        return redirect('/home/custom_fields/time_settings');
    }
}
