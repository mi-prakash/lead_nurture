@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            <div id="copied" class="alert alert-primary hidden" role="alert">Copied successfully</div>
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            @if (Session::has('error_message'))
                <div id="error-danger-msg" class="alert alert-danger custom-alert" role="alert">{{ Session::get('error_message') }}</div>
            @endif
            @error('campaign_name')
                <div id="danger-msg" class="alert alert-danger custom-alert" role="alert">{{ $message }}</div>
            @enderror
            <h4>Edit Campaign</h4>
            <div class="card mt-3 mb-3">
              <div class="card-body">
                <h5 class="card-title">Selected Campaign Category Tree</h5>
                <p class="card-text">{{$campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign_tree->name}}</p>
              </div>
            </div>
            <div class="card mt-3 mb-3">
              <div class="card-body">
                <h5 class="card-title"><i class="fa fa-bullhorn"></i> Campaigns</h5>
                <form class="mt-5" action="{{ url('admin/campaign/update/'.$campaign->id.'/'.$campaign_tree->id) }}" method="POST">
                    @csrf
                    <div class="mb-3 col-md-6 ml-auto mr-auto">
                        <label for="campaign_name" class="form-label">Campaign Name</label>
                        <input type="text" class="form-control" id="campaign_name" name="campaign_name" value="{{$campaign->name}}" required>
                    </div>
                    <div class="mb-3 col-md-6 ml-auto mr-auto">
                        <label for="campaign_description" class="form-label">Campaign Description</label>
                        <textarea class="form-control" id="campaign_description" name="campaign_description" required>{{$campaign->description}}</textarea>
                    </div>
                    <div class="mb-3 form-check col-md-6 ml-auto mr-auto pl-5">
                      <input class="form-check-input" type="checkbox" id="is_reminder" name="is_reminder" @if ($campaign->is_reminder > 0) {{'checked'}} @endif>
                      <label class="form-check-label" for="is_reminder">
                        Is reminder campaign?
                      </label>
                    </div>
                    <div class="mb-3 col-md-6 ml-auto mr-auto before_hours @if ($campaign->is_reminder == 0) {{'hidden'}} @endif">
                        <label for="before_hours" class="form-label">Before Hours</label>
                        <input type="number" class="form-control" id="before_hours" name="before_hours" value="{{$campaign->before_hours}}">
                    </div>
                    <div class="card mt-3 mb-3 col-md-8 ml-auto mr-auto">
                        <div class="card-body text-center">
                            <h5 class="text-center">Start of Campaign</h5>
                            <button type="button" class="btn btn-primary btn-sm mt-4 mb-4 add-msg"><i class="fa fa-plus"></i> Add Message</button>
                            @php
                                $index = 1;
                            @endphp
                            @foreach ($messages as $message)
                                <div class="message-container">
                                    <div class="card">
                                        <div class="card-title">
                                            <h5 class="float-left mt-2 ml-3"><i class="fa fa-comment-o"></i> Text Message</h5>
                                            <button type="button" class="btn btn-danger btn-sm float-right mt-2 mr-2 rm-msg" data-id="{{$message->id}}"><i class="fa fa-trash"></i></button>
                                        </div>
                                        <div class="card-body text-left">
                                            <div class="mb-3">
                                                <input type="hidden" name="is_old[]" class="is_old" value="{{$message->id}}">
                                                <input type="hidden" class="msg_index" name="msg_index[]" value="{{$index}}">
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                      <input class="form-check-input w-d" type="checkbox" name="day" value="day" @if ($message->wait == "day"){{'checked'}}@endif>
                                                      <label class="form-check-label">Wait Days</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                      <input class="form-check-input w-m" type="checkbox" name="minute" value="minute" @if ($message->wait == "minute"){{'checked'}}@endif>
                                                      <label class="form-check-label">Wait Minutes</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="wait[]" class="wait" value="{{$message->wait}}">
                                            </div>
                                            <div class="row mb-3 wait-days @if ($message->wait == "minute"){{'hidden'}}@endif">
                                                <div class="col-md-6">
                                                    <label for="days" class="form-label">Days</label>
                                                    <input type="number" class="form-control days" name="days[]" value="{{$message->days}}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="delivery_time" class="form-label">Desired Delivery Time</label>
                                                    <input type="text" class="form-control delivery_time" name="delivery_time[]" value="{{$message->delivery_time}}" placeholder="hh:mm">
                                                </div>
                                            </div>
                                            <div class="row mb-3 wait-minutes @if ($message->wait == "day"){{'hidden'}}@endif">
                                                <div class="col-md-6">
                                                    <label for="time" class="form-label">Minutes</label>
                                                    <input type="number" class="form-control time" name="time[]" value="{{$message->time}}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="name" class="form-label">Message name for reference</label>
                                                    <input type="text" class="form-control name" name="name[]" value="{{$message->name}}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="name" class="form-label">Message body</label>
                                                    <textarea class="form-control body" name="body[]">{{$message->body}}</textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <small class="note mt-2">
                                                        <p class="placeholder mt-2"><b>Available Placeholder</b></p>
                                                        <p class="placeholder">System</p>
                                                        <span class="short-code">
                                                            @foreach ($system_placeholders as $system_placeholder)
                                                                <em class="s-code" data-value="{{$system_placeholder->name}}">[{{$system_placeholder->name}}]</em>
                                                            @endforeach
                                                        </span>
                                                        <p class="placeholder">{{$campaign_category->name}}</p>
                                                        <span class="short-code">
                                                            @foreach ($category_placeholders as $category_placeholder)
                                                                <em class="s-code" data-value="{{$category_placeholder->name}}">[{{$category_placeholder->name}}]</em>
                                                            @endforeach
                                                        </span>
                                                        <p class="placeholder">{{$campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign_tree->name}}</p>
                                                        <span class="short-code">
                                                            @foreach ($campaign_tree_placeholders as $campaign_tree_placeholder)
                                                                <em class="s-code" data-value="{{$campaign_tree_placeholder->name}}">[{{$campaign_tree_placeholder->name}}]</em>
                                                            @endforeach
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="media_url" class="form-label">Media URL</label>
                                                    <input type="text" class="form-control media_url" name="media_url[]" value="{{$message->media_url}}" placeholder="http://your_url">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-11 text-center ml-auto mr-auto">
                                                    @foreach ($message['rules'] as $rule)
                                                        <div class="rule-container mt-3 mb-3">
                                                            <div class="card border-info">
                                                                <div class="card-title">
                                                                    <h5 class="float-left mt-2 ml-3"><i class="fa fa-bolt"></i> Rule</h5>
                                                                    <button type="button" class="btn btn-danger btn-sm float-right mt-2 mr-2 rm-rule" data-id="{{$rule->id}}"><i class="fa fa-trash"></i></button>
                                                                </div>
                                                                <div class="card-body text-left">
                                                                    <div class="row mb-3">
                                                                        <input type="hidden" name="is_old_rule[{{$index}}][]" value="{{$rule->id}}">
                                                                        <div class="col-md-12">
                                                                            <label for="execute_when" class="form-label">Execute rule when</label>
                                                                            <select  class="form-control rule_check" name="execute_when[{{$index}}][]">
                                                                                <option value="is_sent" @if ($rule->execute_when == 'is_sent'){{"selected"}}@endif>Message is sent</option>
                                                                                <option value="reply_any_response" @if ($rule->execute_when == 'reply_any_response'){{"selected"}}@endif>Contact replies with any response</option>
                                                                                <option value="reply_with_expression" @if ($rule->execute_when == 'reply_with_expression'){{"selected"}}@endif>Contact replies with any expression</option>
                                                                                <option value="reply_with_category" @if ($rule->execute_when == 'reply_with_category'){{"selected"}}@endif>Contact replies with a category</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12">
                                                                            <div class="form-check form-check-inline">
                                                                                <input type="hidden" name="removed[{{$index}}][]" value="@if ($rule->removed > 0){{$rule->removed}}@else{{'0'}}@endif">
                                                                                <input class="form-check-input" type="checkbox" value="removed" @if ($rule->removed > 0){{"checked"}}@endif onclick="this.previousElementSibling.value=1-this.previousElementSibling.value">
                                                                                <label class="form-check-label">Remove the contact from this campaign?</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3 reply_expression @if ($rule->execute_when != 'reply_with_expression'){{"hidden"}}@endif">
                                                                        <div class="col-md-12">
                                                                            <label for="expression_value" class="form-label">Contact Replies with an Expression</label>
                                                                            <input type="text" class="form-control" name="expression_value[{{$index}}][]" value="{{$rule->expression_value}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3 reply_category @if ($rule->execute_when != 'reply_with_category'){{"hidden"}}@endif">
                                                                        <div class="col-md-12">
                                                                            <label for="category" class="form-label">Contact Replies with a Category</label>
                                                                            <select  class="form-control name" name="category[{{$index}}][]">
                                                                                <option value="">Select Category</option>
                                                                                @foreach ($message_rule_categories as $message_rule_category)
                                                                                    <option value="{{$message_rule_category->id}}" @if ($rule->category == $message_rule_category->id){{"selected"}}@endif>{{$message_rule_category->name}}</option> 
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12">
                                                                            <label for="add_to_campaign" class="form-label">Add the contact to another campaign</label>
                                                                            <select  class="form-control name" name="add_to_campaign[{{$index}}][]">
                                                                                <option value="">Select Campaign</option>
                                                                                @foreach ($rule_campaigns as $rule_campaign)
                                                                                    <option value="{{$rule_campaign->id}}" @if ($rule->add_to_campaign == $rule_campaign->id){{"selected"}}@endif>{{$rule_campaign->name}}</option> 
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12">
                                                                            <label for="instant_reply" class="form-label">Instant Reply</label>
                                                                            <textarea class="form-control" name="instant_reply[{{$index}}][]">{{$rule->instant_reply}}</textarea>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <small class="note mt-2">
                                                                                <p class="placeholder mt-2"><b>Available Placeholder</b></p>
                                                                                <p class="placeholder">System</p>
                                                                                <span class="short-code">
                                                                                    @foreach ($system_placeholders as $system_placeholder)
                                                                                        <em class="s-code" data-value="{{$system_placeholder->name}}">[{{$system_placeholder->name}}]</em>
                                                                                    @endforeach
                                                                                </span>
                                                                                <p class="placeholder">{{$campaign_category->name}}</p>
                                                                                <span class="short-code">
                                                                                    @foreach ($category_placeholders as $category_placeholder)
                                                                                        <em class="s-code" data-value="{{$category_placeholder->name}}">[{{$category_placeholder->name}}]</em>
                                                                                    @endforeach
                                                                                </span>
                                                                                <p class="placeholder">{{$campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign_tree->name}}</p>
                                                                                <span class="short-code">
                                                                                    @foreach ($campaign_tree_placeholders as $campaign_tree_placeholder)
                                                                                        <em class="s-code" data-value="{{$campaign_tree_placeholder->name}}">[{{$campaign_tree_placeholder->name}}]</em>
                                                                                    @endforeach
                                                                                </span>
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" class="btn btn-outline-primary btn-sm add-rule"><i class="fa fa-plus"></i> Add Rule</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm mt-4 mb-4 add-msg"><i class="fa fa-plus"></i> Add Message</button>
                                @php
                                    $index++;
                                @endphp
                            @endforeach
                            <h5 class="text-center">End of Campaign</h5>
                        </div>
                    </div>

                    <div class="mb-3 float-right">
                        <a href="{{ url('/admin/campaign/'.$campaign_tree->id) }}" class="btn btn-danger"><i class="fa fa-close"></i> Cancel</a>
                        <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
              </div>
            </div>
        </div>
    </div>
</div>

{{-- Html conent --}}
<div id="html-content" class="hidden">
    <div class="message-container">
        <div class="card">
            <div class="card-title">
                <h5 class="float-left mt-2 ml-3"><i class="fa fa-comment-o"></i> Text Message</h5>
                <button type="button" class="btn btn-danger btn-sm float-right mt-2 mr-2 rm-msg"><i class="fa fa-trash"></i></button>
            </div>
            <div class="card-body text-left">
                <div class="mb-3">
                    <input type="hidden" name="is_old[]" class="is_old" value="0">
                    <input type="hidden" class="msg_index" name="msg_index[]" value="{{count($messages->toArray())}}">
                    <div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input w-d" type="checkbox" name="day" value="day" checked>
                          <label class="form-check-label">Wait Days</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input w-m" type="checkbox" name="minute" value="minute">
                          <label class="form-check-label">Wait Minutes</label>
                        </div>
                    </div>
                    <input type="hidden" name="wait[]" class="wait" value="day">
                </div>
                <div class="row mb-3 wait-days">
                    <div class="col-md-6">
                        <label for="days" class="form-label">Days</label>
                        <input type="number" class="form-control days" name="days[]" value="">
                    </div>
                    <div class="col-md-6">
                        <label for="delivery_time" class="form-label">Desired Delivery Time</label>
                        <input type="text" class="form-control delivery_time" name="delivery_time[]" value="" placeholder="hh:mm">
                    </div>
                </div>
                <div class="row mb-3 wait-minutes hidden">
                    <div class="col-md-6">
                        <label for="time" class="form-label">Minutes</label>
                        <input type="number" class="form-control time" name="time[]" value="">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Message name for reference</label>
                        <input type="text" class="form-control name" name="name[]" value="">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Message body</label>
                        <textarea class="form-control body" name="body[]"></textarea>
                    </div>
                    <div class="col-md-12">
                        <small class="note">
                            <p class="placeholder mt-2"><b>Available Placeholder</b></p>
                            <p class="placeholder">System</p>
                            <span class="short-code">
                                @foreach ($system_placeholders as $system_placeholder)
                                    <em class="s-code" data-value="{{$system_placeholder->name}}">[{{$system_placeholder->name}}]</em>
                                @endforeach
                            </span>
                            <p class="placeholder">{{$campaign_category->name}}</p>
                            <span class="short-code">
                                @foreach ($category_placeholders as $category_placeholder)
                                    <em class="s-code" data-value="{{$category_placeholder->name}}">[{{$category_placeholder->name}}]</em>
                                @endforeach
                            </span>
                            <p class="placeholder">{{$campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign_tree->name}}</p>
                            <span class="short-code">
                                @foreach ($campaign_tree_placeholders as $campaign_tree_placeholder)
                                    <em class="s-code" data-value="{{$campaign_tree_placeholder->name}}">[{{$campaign_tree_placeholder->name}}]</em>
                                @endforeach
                            </span>
                        </small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="media_url" class="form-label">Media URL</label>
                        <input type="text" class="form-control media_url" name="media_url[]" value="" placeholder="http://your_url">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-11 text-center ml-auto mr-auto">
                        <button type="button" class="btn btn-outline-primary btn-sm add-rule"><i class="fa fa-plus"></i> Add Rule</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary btn-sm mt-4 mb-4 add-msg"><i class="fa fa-plus"></i> Add Message</button>
</div>

{{-- Rule content --}}
<div id="rule-content" class="hidden">
    <div class="rule-container mt-3 mb-3">
        <div class="card border-info">
            <div class="card-title">
                <h5 class="float-left mt-2 ml-3"><i class="fa fa-bolt"></i> Rule</h5>
                <button type="button" class="btn btn-danger btn-sm float-right mt-2 mr-2 rm-rule"><i class="fa fa-trash"></i></button>
            </div>
            <div class="card-body text-left">
                <div class="row mb-3">
                    <input type="hidden" name="is_old_rule" value="0">
                    <div class="col-md-12">
                        <label for="execute_when" class="form-label">Execute rule when</label>
                        <select  class="form-control rule_check" name="execute_when">
                            <option value="is_sent">Message is sent</option>
                            <option value="reply_any_response">Contact replies with any response</option>
                            <option value="reply_with_expression">Contact replies with any expression</option>
                            <option value="reply_with_category">Contact replies with a category</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check form-check-inline">
                            <input type="hidden" name="removed" value="0">
                            <input class="form-check-input" type="checkbox" value="removed" onclick="this.previousElementSibling.value=1-this.previousElementSibling.value">
                            <label class="form-check-label">Remove the contact from this campaign?</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3 reply_expression hidden">
                    <div class="col-md-12">
                        <label for="expression_value" class="form-label">Contact Replies with an Expression</label>
                        <input type="text" class="form-control" name="expression_value">
                    </div>
                </div>
                <div class="row mb-3 reply_category hidden">
                    <div class="col-md-12">
                        <label for="category" class="form-label">Contact Replies with a Category</label>
                        <select  class="form-control name" name="category">
                            <option value="">Select Category</option>
                            @foreach ($message_rule_categories as $message_rule_category)
                                <option value="{{$message_rule_category->id}}">{{$message_rule_category->name}}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="add_to_campaign" class="form-label">Add the contact to another campaign</label>
                        <select  class="form-control name" name="add_to_campaign">
                            <option value="">Select Campaign</option>
                            @foreach ($rule_campaigns as $rule_campaign)
                                <option value="{{$rule_campaign->id}}">{{$rule_campaign->name}}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="instant_reply" class="form-label">Instant Reply</label>
                        <textarea class="form-control" name="instant_reply"></textarea>
                    </div>
                    <div class="col-md-12">
                        <small class="note mt-2">
                            <p class="placeholder mt-2"><b>Available Placeholder</b></p>
                            <p class="placeholder">System</p>
                            <span class="short-code">
                                @foreach ($system_placeholders as $system_placeholder)
                                    <em class="s-code" data-value="{{$system_placeholder->name}}">[{{$system_placeholder->name}}]</em>
                                @endforeach
                            </span>
                            <p class="placeholder">{{$campaign_category->name}}</p>
                            <span class="short-code">
                                @foreach ($category_placeholders as $category_placeholder)
                                    <em class="s-code" data-value="{{$category_placeholder->name}}">[{{$category_placeholder->name}}]</em>
                                @endforeach
                            </span>
                            <p class="placeholder">{{$campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign_tree->name}}</p>
                            <span class="short-code">
                                @foreach ($campaign_tree_placeholders as $campaign_tree_placeholder)
                                    <em class="s-code" data-value="{{$campaign_tree_placeholder->name}}">[{{$campaign_tree_placeholder->name}}]</em>
                                @endforeach
                            </span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var base_url = "{{ url('/') }}";
        var html_content = $("#html-content");
        var rule_content = $("#rule-content");
        var msg_index = $("#html-content .msg_index").val();

        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }

        $('.delivery_time').timepicker({
            minuteStep: 1,
            showMeridian: false
        });

        $(".container").on('click', '.add-msg', function(){
            msg_index++;
            html_content.find(".msg_index").val(msg_index);
            $(this).after(html_content.html());
            $('.delivery_time').timepicker({
                minuteStep: 1,
                showMeridian: false
            });
        });

        $(".container").on('click', '.rm-msg', function(){
            var id = $(this).data('id');
            var parent = $(this).parent().parent().parent();
            if (confirm('Are you sure?')) {
                if (typeof id  !== "undefined") {
                    $.ajax({
                        url: base_url+"/admin/campaign/message_delete/"+id,
                        cache: false,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        beforeSend: function() {
                        },
                        success: function(response) {
                            if (response == 'success') {
                                location.reload();
                            }
                        }
                    });
                }
                parent.next().remove();
                parent.remove();
            }
        });

        $(".container").on('change', '.w-d, .w-m', function(){
            var parent = $(this).parent().parent().parent().parent();
            if ($(this).val() == "minute") {
                if (!$(this).is(":checked") && !$(this).parent().parent().find(".w-d").is(":checked")) {
                    $(this).prop("checked", true);
                    return false;
                }
                $(this).parent().parent().find(".w-d").removeAttr("checked");
                $(this).parent().parent().find(".w-d").prop("checked", false);
                $(this).parent().parent().parent().find(".wait").val("minute");
                parent.find(".wait-days").addClass("hidden");
                parent.find(".wait-minutes").removeClass("hidden");
            } else {
                if (!$(this).is(":checked") && !$(this).parent().parent().find(".w-m").is(":checked")) {
                    $(this).prop("checked", true);
                    return false;
                }
                $(this).parent().parent().find(".w-m").removeAttr("checked");
                $(this).parent().parent().find(".w-m").prop("checked", false);
                $(this).parent().parent().parent().find(".wait").val("day");
                parent.find(".wait-minutes").addClass("hidden");
                parent.find(".wait-days").removeClass("hidden");
            }
        });

        $("#is_reminder").change(function(){
            if ($("#is_reminder").is(":checked")) {
                $(".before_hours").removeClass("hidden");
            } else {
                $(".before_hours").addClass("hidden");
            }
        });

        $(".container").on('click', '.s-code', function(){
            var text = "["+$(this).data('value')+"]";
            copyToClipboard(text);
            $("#copied").fadeIn();
            setTimeout(function(){ $("#copied").fadeOut(); }, 1500);
        });

        function copyToClipboard(text) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();
        }

        $(".container").on('change', '.rule_check', function(){
            var parent = $(this).parent().parent().parent();
            if ($(this).val() == "reply_with_expression") {
                parent.find(".reply_expression").removeClass("hidden");
                parent.find(".reply_category").addClass("hidden");
            } else if ($(this).val() == "reply_with_category") {
                parent.find(".reply_category").removeClass("hidden");
                parent.find(".reply_expression").addClass("hidden");
            } else {
                parent.find(".reply_expression").addClass("hidden");
                parent.find(".reply_category").addClass("hidden");
            }
        });

        $(".container").on('click', '.add-rule', function(){
            var parent = $(this).parent().parent().parent();
            var this_index = parent.find(".msg_index").val();
            
            $(this).before(rule_content.html());

            $(this).prev().find("[name='is_old_rule']").attr('name', 'is_old_rule['+this_index+'][]');
            $(this).prev().find("[name='execute_when']").attr('name', 'execute_when['+this_index+'][]');
            $(this).prev().find("[name='removed']").attr('name', 'removed['+this_index+'][]');
            $(this).prev().find("[name='add_to_campaign']").attr('name', 'add_to_campaign['+this_index+'][]');
            $(this).prev().find("[name='expression_value']").attr('name', 'expression_value['+this_index+'][]');
            $(this).prev().find("[name='category']").attr('name', 'category['+this_index+'][]');
            $(this).prev().find("[name='instant_reply']").attr('name', 'instant_reply['+this_index+'][]');
        });

        $(".container").on('click', '.rm-rule', function(){
            var id = $(this).data('id');
            var parent = $(this).parent().parent().parent();
            if (confirm('Are you sure?')) {
                if (typeof id  !== "undefined") {
                    $.ajax({
                        url: base_url+"/admin/campaign/rule_delete/"+id,
                        cache: false,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        beforeSend: function() {
                        },
                        success: function(response) {
                            if (response == 'success') {
                                location.reload();
                            }
                        }
                    });
                }
                parent.remove();
            }
        });
    });
</script>
@endsection