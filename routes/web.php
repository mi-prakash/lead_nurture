<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\TimezoneSetting;

$application_timezone = TimezoneSetting::where('type', 'application')->first();
if (!empty($application_timezone)) {
    $timezone = $application_timezone->timezone;
    config(['app.timezone' => $timezone]);
}

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => ['impersonate', 'user']], function()
{
    // Route::get('/get_no_schedule', [App\Http\Controllers\HomeController::class, 'getNoSchedule'])->name('getNoSchedule');
    
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    Route::get('/home/appointment/{lead_id}', [App\Http\Controllers\AppointmentController::class, 'index'])->name('appointments');
    Route::get('/home/appointment/detail/{appointment_id}', [App\Http\Controllers\AppointmentController::class, 'show'])->name('appointment_detail');
    Route::get('/home/appointment/detail/log/{appointment_id}/{id}', [App\Http\Controllers\AppointmentController::class, 'log'])->name('appointment_log');

    Route::post('/home/acuity_fetch_calendar', [App\Http\Controllers\HomeController::class, 'acuity_fetch_calendar'])->name('acuity_fetch_calendar');

    Route::post('/home/clickfunnel_fetch_funnel', [App\Http\Controllers\HomeController::class, 'clickfunnel_fetch_funnel'])->name('clickfunnel_fetch_funnel');

    Route::get('/home/messages/{lead_id}', [App\Http\Controllers\MessageController::class, 'index'])->name('message');
    Route::post('/home/messages/send_message', [App\Http\Controllers\MessageController::class, 'send_message'])->name('send_message');

    Route::get('/home/integrations', [App\Http\Controllers\SettingController::class, 'index'])->name('settings');
    Route::post('/home/integrations', [App\Http\Controllers\SettingController::class, 'store'])->name('save_settings');

    Route::get('/users/stop', [App\Http\Controllers\AdminController::class, 'stopImpersonate']);

    Route::get('/home/custom_fields/category', [App\Http\Controllers\CustomFieldController::class, 'userCategoryIndex']);
    Route::get('/home/custom_fields/campaign_tree', [App\Http\Controllers\CustomFieldController::class, 'userIndex']);
    Route::get('/home/custom_fields/time_settings', [App\Http\Controllers\CustomFieldController::class, 'timeSettings']);
    Route::get('/home/custom_fields/edit/{id}/{user_custom_field_id}', [App\Http\Controllers\CustomFieldController::class, 'userCampaignTreeEdit']);
    Route::post('/home/custom_fields/update/{id}', [App\Http\Controllers\CustomFieldController::class, 'userCampaignTreeUpdate']);
    Route::post('/home/save_user_time_settings', [App\Http\Controllers\CustomFieldController::class, 'saveUserTimeSettings']);
    
    Route::get('/home/calendar', [App\Http\Controllers\HomeController::class, 'calendar']);
    Route::get('/home/calendar/add_schedule', [App\Http\Controllers\HomeController::class, 'calendarAddSchedule']);
    Route::post('/home/calendar/save_schedule', [App\Http\Controllers\HomeController::class, 'calendarStoreSchedule']);
    Route::get('/home/calendar/show_schedule/{id}', [App\Http\Controllers\HomeController::class, 'calendarShowSchedule']);
    Route::get('/home/calendar/show_reschedule/{id}', [App\Http\Controllers\HomeController::class, 'calendarShowReschedule']);
    Route::post('/home/calendar/update_reschedule/{id}', [App\Http\Controllers\HomeController::class, 'calendarUpdateReschedule']);
    Route::get('/home/calendar/cancle_schedule/{id}', [App\Http\Controllers\HomeController::class, 'calendarCancelSchedule']);

    Route::get('/test_time', [App\Http\Controllers\HomeController::class, 'testTime']);
});


/*Admin routes*/
Route::group(['middleware' => 'admin'], function()
{
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/user/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('admin.create_user');
    Route::post('/admin/user/save', [App\Http\Controllers\AdminController::class, 'saveUser'])->name('admin.save_user');
    Route::get('/admin/user/edit/{identifier}', [App\Http\Controllers\AdminController::class, 'editUser'])->name('admin.edit_user');
    Route::post('/admin/user/update/{identifier}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.update_user');

    Route::get('/users/{id}/impersonate', [App\Http\Controllers\AdminController::class, 'impersonate']);

    Route::get('/admin/campaign/categorytree', [App\Http\Controllers\CampaignCategoryController::class, 'index']);
    Route::get('/admin/campaign_category/add', [App\Http\Controllers\CampaignCategoryController::class, 'create']);
    Route::post('/admin/campaign_category/store', [App\Http\Controllers\CampaignCategoryController::class, 'store']);
    Route::get('/admin/campaign_category/edit/{id}', [App\Http\Controllers\CampaignCategoryController::class, 'edit']);
    Route::post('/admin/campaign_category/update/{id}', [App\Http\Controllers\CampaignCategoryController::class, 'update']);
    Route::delete('/admin/campaign_category/destroy/{id}', [App\Http\Controllers\CampaignCategoryController::class, 'destroy']);
    Route::get('/admin/campaign/campaign_tree/copy/{id}', [App\Http\Controllers\CampaignCategoryController::class, 'copy']);
    Route::post('/admin/campaign/campaign_tree/save_copy/{id}', [App\Http\Controllers\CampaignCategoryController::class, 'save_copy']);

    Route::get('/admin/campaign_tree/add/{id}', [App\Http\Controllers\CampaignTreeController::class, 'create']);
    Route::post('/admin/campaign_tree/store', [App\Http\Controllers\CampaignTreeController::class, 'store']);
    Route::get('/admin/campaign_tree/edit/{id}', [App\Http\Controllers\CampaignTreeController::class, 'edit']);
    Route::post('/admin/campaign_tree/update/{id}', [App\Http\Controllers\CampaignTreeController::class, 'update']);
    Route::delete('/admin/campaign_tree/destroy/{id}', [App\Http\Controllers\CampaignTreeController::class, 'destroy']);

    Route::get('/admin/campaign/{tree_id}', [App\Http\Controllers\CampaignController::class, 'index']);
    Route::get('/admin/campaign/new/{tree_id}', [App\Http\Controllers\CampaignController::class, 'create']);
    Route::post('/admin/campaign/store/{tree_id}', [App\Http\Controllers\CampaignController::class, 'store']);
    Route::get('/admin/campaign/edit/{id}', [App\Http\Controllers\CampaignController::class, 'edit']);
    Route::post('/admin/campaign/update/{id}/{tree_id}', [App\Http\Controllers\CampaignController::class, 'update']);
    Route::delete('/admin/campaign/destroy/{id}', [App\Http\Controllers\CampaignController::class, 'destroy']);
    Route::delete('/admin/campaign/message_delete/{id}', [App\Http\Controllers\CampaignController::class, 'messageDelete']);
    Route::delete('/admin/campaign/rule_delete/{id}', [App\Http\Controllers\CampaignController::class, 'ruleDelete']);

    Route::get('/admin/custom_fields', [App\Http\Controllers\CustomFieldController::class, 'index']);
    Route::get('/admin/custom_fields/category', [App\Http\Controllers\CustomFieldController::class, 'categoryIndex']);
    Route::get('/admin/custom_fields/category/add/{category_id}', [App\Http\Controllers\CustomFieldController::class, 'categoryCreate']);
    Route::post('/admin/custom_fields/category/store', [App\Http\Controllers\CustomFieldController::class, 'categoryStore']);
    Route::get('/admin/custom_fields/category/edit/{id}', [App\Http\Controllers\CustomFieldController::class, 'categoryEdit']);
    Route::post('/admin/custom_fields/category/update/{id}', [App\Http\Controllers\CustomFieldController::class, 'categoryUpdate']);
    Route::delete('/admin/custom_fields/category/delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'categoryDelete']);

    Route::get('/admin/custom_fields/campaign_tree', [App\Http\Controllers\CustomFieldController::class, 'campaignTreeIndex']);
    Route::get('/admin/custom_fields/campaign_tree/get_campaign_tree/{category_id}', [App\Http\Controllers\CustomFieldController::class, 'getCampaignTree']);
    Route::get('/admin/custom_fields/campaign_tree/add/{category_id}/{tree_id}', [App\Http\Controllers\CustomFieldController::class, 'campaignTreeCreate']);
    Route::post('/admin/custom_fields/campaign_tree/store', [App\Http\Controllers\CustomFieldController::class, 'campaignTreeStore']);
    Route::get('/admin/custom_fields/campaign_tree/edit/{id}', [App\Http\Controllers\CustomFieldController::class, 'campaignTreeEdit']);
    Route::post('/admin/custom_fields/campaign_tree/update/{id}', [App\Http\Controllers\CustomFieldController::class, 'campaignTreeUpdate']);
    Route::delete('/admin/custom_fields/campaign_tree/delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'campaignTreeDelete']);

    Route::get('/admin/campaign_trigger', [App\Http\Controllers\CampaignTriggerController::class, 'index']);
    Route::post('/admin/campaign_trigger/store', [App\Http\Controllers\CampaignTriggerController::class, 'store']);

    Route::get('/admin/user/campaign_tree_assign/{user_id}', [App\Http\Controllers\AdminController::class, 'userCampaignTreeAssign']);
    Route::get('/admin/user/add_new_campaign_tree/{user_id}', [App\Http\Controllers\AdminController::class, 'userNewCampaignTreeAssign']);
    Route::post('/admin/user/save_campaign_tree', [App\Http\Controllers\AdminController::class, 'userSaveCampaignTree']);
    Route::get('/admin/user/edit_campaign_tree/{id}', [App\Http\Controllers\AdminController::class, 'userEditCampaignTree']);
    Route::post('/admin/user/update_campaign_tree/{id}', [App\Http\Controllers\AdminController::class, 'userUpdateCampaignTree']);

    Route::get('/admin/settings', [App\Http\Controllers\AdminSettingController::class, 'index']);
    Route::post('/admin/settings/save', [App\Http\Controllers\AdminSettingController::class, 'store']);

    Route::get('/admin/message_rule_categories', [App\Http\Controllers\MessageRuleCategoryController::class, 'index']);
    Route::get('/admin/message_rule_categories/add', [App\Http\Controllers\MessageRuleCategoryController::class, 'create']);
    Route::post('/admin/message_rule_categories/store', [App\Http\Controllers\MessageRuleCategoryController::class, 'store']);
    Route::get('/admin/message_rule_categories/edit/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'edit']);
    Route::post('/admin/message_rule_categories/update/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'update']);
    Route::delete('/admin/message_rule_categories/delete/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'destroy']);

    Route::get('/admin/message_rule_categories/expressions/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'expressionIndex']);
    Route::get('/admin/message_rule_categories/expressions/add/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'expressionCreate']);
    Route::post('/admin/message_rule_categories/expressions/store/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'expressionStore']);
    Route::get('/admin/message_rule_categories/expressions/edit/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'expressionEdit']);
    Route::post('/admin/message_rule_categories/expressions/update/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'expressionUpdate']);
    Route::delete('/admin/message_rule_categories/expressions/delete/{id}', [App\Http\Controllers\MessageRuleCategoryController::class, 'expressionDestroy']);
});

/* Time */
Route::get('/get_timer', [App\Http\Controllers\AdminController::class, 'get_timer'])->name('get_timer');

/* Webhooks */
Route::post('/home/webhook_new_appointment/{identifier}', [App\Http\Controllers\HomeController::class, 'webhook_new_appointment'])->name('webhook_new_appointment');
Route::post('/home/webhook_rescheduled', [App\Http\Controllers\HomeController::class, 'webhook_rescheduled'])->name('webhook_rescheduled');
Route::post('/home/webhook_canceled', [App\Http\Controllers\HomeController::class, 'webhook_canceled'])->name('webhook_canceled');
Route::post('/home/webhook_changed', [App\Http\Controllers\HomeController::class, 'webhook_changed'])->name('webhook_changed');
Route::post('/home/webhook_complete', [App\Http\Controllers\HomeController::class, 'webhook_complete'])->name('webhook_complete');

Route::post('/home/funnel_webhooks/test/get_lead/{identifier}', [App\Http\Controllers\HomeController::class, 'clickfunnel_webhook'])->name('clickfunnel_webhook');

Route::post('/home/messages/pre_event', [App\Http\Controllers\MessageController::class, 'pre_event'])->name('pre_event');
Route::post('/home/messages/get_sms/{identifier}', [App\Http\Controllers\MessageController::class, 'get_sms'])->name('get_sms');

/* Artisan commends */
Route::get('/home/artisan_add_appointments', [App\Http\Controllers\HomeController::class, 'artisan_add_appointments'])->name('artisan_add_appointments');
Route::get('/home/artisan_send_queue_messages', [App\Http\Controllers\HomeController::class, 'artisan_send_queue_messages'])->name('artisan_send_queue_messages');
