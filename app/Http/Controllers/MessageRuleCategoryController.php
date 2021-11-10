<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\MessageRuleCategory;
use App\Models\RuleExpression;

class MessageRuleCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message_rule_categories = MessageRuleCategory::with('user')->with('expressions')->get();
        return view('admin.message_rule_categories.index', compact('message_rule_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.message_rule_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $admin_id = Auth::id();
        $request->validate([
                    'name' => 'required'
                ]);
        $data = array(
                    'name' => $request['name'],
                    'added_by' => $admin_id
                );
        $create = MessageRuleCategory::create($data);
        if ($create) {
            Session::flash('success_message','Successfully saved');
        }
        return redirect('/admin/message_rule_categories');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $message_rule_category = MessageRuleCategory::where('id', $id)->first();
        return view('admin.message_rule_categories.edit', compact('message_rule_category')); 
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
        $admin_id = Auth::id();
        $request->validate([
                    'name' => 'required'
                ]);
        $data = array(
                    'name' => $request['name'],
                    'added_by' => $admin_id
                );
        $update = MessageRuleCategory::where('id', $id)->update($data);
        if ($update) {
            Session::flash('success_message','Successfully updated');
        }
        return redirect('/admin/message_rule_categories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = MessageRuleCategory::where('id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }

    public function expressionIndex($message_rule_category_id)
    {
        $rule_expressions = RuleExpression::with('rule_category')->where('message_rule_category_id', $message_rule_category_id)->get();
        return view('admin.message_rule_categories.expression_index', compact('rule_expressions', 'message_rule_category_id'));
    }

    public function expressionCreate($message_rule_category_id)
    {
        $message_rule_category = MessageRuleCategory::where('id', $message_rule_category_id)->first();
        return view('admin.message_rule_categories.expression_create', compact('message_rule_category', 'message_rule_category_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function expressionStore(Request $request, $message_rule_category_id)
    {
        $admin_id = Auth::id();
        $request->validate([
                    'name' => 'required'
                ]);
        $data = array(
                    'message_rule_category_id' => $message_rule_category_id,
                    'name' => $request['name']
                );
        $create = RuleExpression::create($data);
        if ($create) {
            Session::flash('success_message','Successfully saved');
        }
        return redirect('/admin/message_rule_categories/expressions/'.$message_rule_category_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function expressionEdit($id)
    {
        $rule_expression = RuleExpression::with('rule_category')->where('id', $id)->first();
        $message_rule_category = MessageRuleCategory::where('id', $rule_expression->message_rule_category_id)->first();
        return view('admin.message_rule_categories.expression_edit', compact('id', 'rule_expression', 'message_rule_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function expressionUpdate(Request $request, $id)
    {
        $admin_id = Auth::id();
        $request->validate([
                    'name' => 'required'
                ]);
        $rule_expression = RuleExpression::where('id', $id)->first();
        $data = array(
                    'message_rule_category_id' => $rule_expression->message_rule_category_id,
                    'name' => $request['name']
                );
        $update = RuleExpression::where('id', $id)->update($data);
        if ($update) {
            Session::flash('success_message','Successfully updated');
        }
        return redirect('/admin/message_rule_categories/expressions/'.$rule_expression->message_rule_category_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function expressionDestroy($id)
    {
        $delete = RuleExpression::where('id', $id)->delete();
        if ($delete) {
            Session::flash('success_message','Successfully deleted');
            echo "success";
        }
    }
}
