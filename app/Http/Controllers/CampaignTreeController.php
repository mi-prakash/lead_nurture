<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\CampaignCategory;
use App\Models\CampaignTree;


class CampaignTreeController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($campaign_category_id)
    {
        $campaign_category = CampaignCategory::where('id', $campaign_category_id)->first();
        return view('admin.campaign_trees.create', compact('campaign_category'));
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
                            'name' => 'required|unique:campaign_trees'
                        ]);

        $data = array(
                    'campaign_category_id' => $request['campaign_category_id'],
                    'name' => $request['name'],
                    'status' => 'active'
                );
        $create = CampaignTree::create($data);
        if ($create) {
            Session::flash('success_message','Successfully saved');
        }

        return redirect('/admin/campaign/categorytree');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CampaignTree  $campaignTree
     * @return \Illuminate\Http\Response
     */
    public function show(CampaignTree $campaignTree)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CampaignTree  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $campaign_tree = CampaignTree::where('id', $id)->first();
        $campaign_category = CampaignCategory::where('id', $campaign_tree->campaign_category_id)->first();
        return view('admin.campaign_trees.edit', compact('campaign_category', 'campaign_tree'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CampaignTree  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
                            'name' => 'required|unique:campaign_trees,name,'.$id
                        ]);
        
        $data = array(
                    'campaign_category_id' => $request['campaign_category_id'],
                    'name' => $request['name'],
                    'status' => $request['status']
                );
        $update = CampaignTree::where('id', $id)->update($data);
        if ($update) {
            Session::flash('success_message','Successfully updated');
        }

        return redirect('/admin/campaign/categorytree');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CampaignTree  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = CampaignTree::where('id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }
}
