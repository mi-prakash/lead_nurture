@extends('layouts.app')
@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div id="copy-success" class="alert alert-primary invisible" role="alert">
			  Web Hooks URL copied to clipboard
			</div>
			@if (Session::has('success_message'))
				<div id="success-msg" class="alert alert-primary" role="alert">{{ Session::get('success_message') }}</div>
			@endif
			<h4>Integration Settings</h4>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Click Funnels</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Acuity Scheduling</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Twilio</a>
				</li>
			</ul>
			<form action="{{ url('home/integrations') }}" method="POST">
				@csrf
				<div class="tab-content" id="myTabContent">
					<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
						<div class="mt-4">
							<h4>Click Funnels Settings</h4>
							<div class="form-group row">
								<label for="click_funnel_email" class="col-sm-4 col-form-label">Click Funnels Email Address</label>
								<div class="col-sm-8">
									<input type="email" class="form-control" id="click_funnel_email" name="click_funnel_email" value="@if (!empty($user_settings)){{ $user_settings->click_funnel_email }}@endif">
								</div>
							</div>
							<div class="form-group row">
								<label for="click_funnel_api_key" class="col-sm-4 col-form-label">Click Funnels API Key</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="click_funnel_api_key" name="click_funnel_api_key" value="@if (!empty($user_settings)){{ $user_settings->click_funnel_api_key }}@endif">
								</div>
							</div>
							<div class="form-group row">
								<label for="click_funnel_webhook_url" class="col-sm-4 col-form-label">Web Hooks URL</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input readonly type="text" class="form-control" id="click_funnel_webhook_url" value="{{ url('/funnel_webhooks/test/get_lead/'.$user->identifier) }}">
										<div class="input-group-append">
											<button id="click-funnel-webhook-copy" class="btn btn-primary" type="button"><i class="fa fa-file"></i> Copy</button>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label for="click_funnel_id" class="col-sm-4 col-form-label">Funnel's Name(s)</label>
								<div class="col-sm-6">
									<div class="input-group">
										<select class="form-control" name="click_funnel_id" id="click_funnel_id" readonly>
											@if (!empty($user_settings->click_funnel_id) && !empty($user_settings->click_funnel_name))
												<option class="funnel-initial" value="{{ $user_settings->click_funnel_id }}">{{ $user_settings->click_funnel_name }}</option>
											@else
												<option class="funnel-initial" value="">Please Fetch Funnels</option>
											@endif
										</select>
										<div class="input-group-append">
											<button id="fetch-funnels" class="btn btn-primary" type="button"><i class="fa fa-list"></i> Fetch Funnels</button>
										</div>
									</div>
									<input type="hidden" id="click_funnel_name" name="click_funnel_name" value="@if (!empty($user_settings)){{ $user_settings->click_funnel_name }}@endif">
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
						<div class="mt-4">
							<h4>Acuity Scheduling Settings</h4>
							<div class="form-group row">
								<label for="acuity_user_id" class="col-sm-4 col-form-label">Acuity Scheduling User ID</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="acuity_user_id" name="acuity_user_id" value="@if (!empty($user_settings)){{ $user_settings->acuity_user_id }}@endif">
								</div>
							</div>
							<div class="form-group row">
								<label for="acuity_api_key" class="col-sm-4 col-form-label">Acuity Scheduling API Key</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="acuity_api_key" name="acuity_api_key" value="@if (!empty($user_settings)){{ $user_settings->acuity_api_key }}@endif">
								</div>
							</div>
							<div class="form-group row">
								<label for="acuity_webhook_url" class="col-sm-4 col-form-label">Web Hooks URL</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input readonly type="text" class="form-control" id="acuity_webhook_url" value="{{ url('/home/webhook_new_appointment/'.$user->identifier) }}">
										<div class="input-group-append">
											<button id="acuity-webhook-copy" class="btn btn-primary" type="button"><i class="fa fa-file"></i> Copy</button>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label for="acuity_calendar_id" class="col-sm-4 col-form-label">Active Calendar</label>
								<div class="col-sm-6">
									<div class="input-group">
										<select class="form-control" name="acuity_calendar_id" id="acuity_calendar_id" readonly>
											@if (!empty($user_settings->acuity_calendar_id) && !empty($user_settings->acuity_calendar_name))
												<option class="acuity-initial" value="{{ $user_settings->acuity_calendar_id }}">{{ $user_settings->acuity_calendar_name }}</option>
											@else
												<option class="acuity-initial" value="">Please Fetch Calendars</option>
											@endif
										</select>
										<div class="input-group-append">
											<button id="fetch-calendars" class="btn btn-primary" type="button"><i class="fa fa-calendar"></i> Fetch Calendars</button>
										</div>
									</div>
									<input type="hidden" id="acuity_calendar_name" name="acuity_calendar_name" value="@if (!empty($user_settings)){{ $user_settings->acuity_calendar_name }}@endif">
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
						<div class="mt-4">
							<h4>Twilio Settings</h4>
							<div class="form-group row">
								<label for="twilio_account_sid" class="col-sm-4 col-form-label">Twilio Account SID</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="twilio_account_sid" name="twilio_account_sid" value="@if (!empty($user_settings)){{ $user_settings->twilio_account_sid }}@endif">
								</div>
							</div>
							<div class="form-group row">
								<label for="twilio_auth_token" class="col-sm-4 col-form-label">Twilio Auth Token</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="twilio_auth_token" name="twilio_auth_token" value="@if (!empty($user_settings)){{ $user_settings->twilio_auth_token }}@endif">
								</div>
							</div>
							<div class="form-group row">
								<label for="twilio_number" class="col-sm-4 col-form-label">Twilio Number</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="twilio_number" name="twilio_number" value="@if (!empty($user_settings)){{ $user_settings->twilio_number }}@endif">
								</div>
							</div>
							<div class="form-group row">
								<label for="twilio_webhook_url" class="col-sm-4 col-form-label">Web Hooks URL</label>
								{{-- <div class="col-sm-8">
									<div class="form-control-plaintext">{{ url('/home/messages/get_sms/'.$user->identifier) }}</div>
								</div> --}}
								<div class="col-sm-8">
									<div class="input-group">
										<input readonly type="text" class="form-control" id="twilio_webhook_url" value="{{ url('/home/messages/get_sms/'.$user->identifier) }}">
										<div class="input-group-append">
											<button id="twilio-webhook-copy" class="btn btn-primary" type="button"><i class="fa fa-file"></i> Copy</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="text-center mt-5">
					<button type="submit" id="update" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		var base_url = "{{ url('/') }}";

	    function copyToClipboard(elem) {
	        // create hidden text element, if it doesn't already exist
	        var targetId = "_hiddenCopyText_";
	        var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
	        var origSelectionStart, origSelectionEnd;
	        if (isInput) {
	            // can just use the original source element for the selection and copy
	            target = elem;
	            origSelectionStart = elem.selectionStart;
	            origSelectionEnd = elem.selectionEnd;
	        } else {
	            // must use a temporary form element for the selection and copy
	            target = document.getElementById(targetId);
	            if (!target) {
	                var target = document.createElement("textarea");
	                target.style.position = "absolute";
	                target.style.left = "-9999px";
	                target.style.top = "0";
	                target.id = targetId;
	                document.body.appendChild(target);
	            }
	            target.textContent = elem.textContent;
	        }
	        // select the content
	        var currentFocus = document.activeElement;
	        target.focus();
	        target.setSelectionRange(0, target.value.length);

	        // copy the selection
	        var succeed;
	        try {
	            succeed = document.execCommand("copy");
	        } catch (e) {
	            succeed = false;
	        }
	        // restore original focus
	        if (currentFocus && typeof currentFocus.focus === "function") {
	            currentFocus.focus();
	        }

	        if (isInput) {
	            // restore prior selection
	            elem.setSelectionRange(origSelectionStart, origSelectionEnd);
	        } else {
	            // clear temporary content
	            target.textContent = "";
	        }
	        $("#copy-success").removeClass("invisible");
	        setTimeout(function(){ $("#copy-success").addClass("invisible"); }, 5000);
	        return succeed;
	    }

	    if ($("#success-msg").length) {
	    	setTimeout(function(){ $("#success-msg").remove(); }, 5000);
	    }

	    $("#click-funnel-webhook-copy").click(function(){
	    	copyToClipboard(document.getElementById("click_funnel_webhook_url"));
	    });
	    $("#acuity-webhook-copy").click(function(){
	    	copyToClipboard(document.getElementById("acuity_webhook_url"));
	    });
	    $("#twilio-webhook-copy").click(function(){
	    	copyToClipboard(document.getElementById("twilio_webhook_url"));
	    });

	    $("#fetch-funnels").click(function() {
	        var email = $("#click_funnel_email").val();
	        var api_key = $("#click_funnel_api_key").val();
	        if (email != '' && api_key != '') {
	        	$.ajax({
	        	    url: base_url+"/home/clickfunnel_fetch_funnel",
	        	    cache: false,
	        	    type: "POST",
	        	    data: {
	        	    	_token: "{{ csrf_token() }}",
	        	    	email: email,
	        	    	api_key: api_key

	        	    },
	        	    beforeSend: function() {
	        	        $("#fetch-funnels").html("<i class='fa fa-refresh fa-spin'></i> Fetching Data");
	        	        $("#fetch-funnels").attr('disabled', 'disabled');
	        	        $("#update").attr('disabled', 'disabled');
	        	    },
	        	    success: function(response) {
	        	    	$('.funnel-initial').remove();
	        	    	$("#fetch-funnels").html("<i class='fa fa-list'></i> Fetch Funnels");
	        	        $("#fetch-funnels").removeAttr('disabled');
	        	        $("#update").removeAttr('disabled');
	        	        $("#click_funnel_id").removeAttr('readonly');
	        	        $("#click_funnel_id").html(response);
	        	        $("#click_funnel_name").val($("#click_funnel_id option:selected").text());
	        	    }
	        	});
	        }
	    });
	    $("#click_funnel_id").change(function(){
	    	$("#click_funnel_name").val($("#click_funnel_id option:selected").text());
	    });

	    $("#fetch-calendars").click(function() {
	        var user_id = $("#acuity_user_id").val();
	        var api_key = $("#acuity_api_key").val();
	        if (user_id != '' && api_key != '') {
	        	$.ajax({
	        	    url: base_url+"/home/acuity_fetch_calendar",
	        	    cache: false,
	        	    type: "POST",
	        	    data: {
	        	    	_token: "{{ csrf_token() }}",
	        	    	user_id: user_id,
	        	    	api_key: api_key

	        	    },
	        	    beforeSend: function() {
	        	        $("#fetch-calendars").html("<i class='fa fa-refresh fa-spin'></i> Fetching Data");
	        	        $("#fetch-calendars").attr('disabled', 'disabled');
	        	        $("#update").attr('disabled', 'disabled');
	        	    },
	        	    success: function(response) {
	        	    	$('.acuity-initial').remove();
	        	    	$("#fetch-calendars").html("<i class='fa fa-calendar'></i> Fetch Calendars");
	        	        $("#fetch-calendars").removeAttr('disabled');
	        	        $("#update").removeAttr('disabled');
	        	        $("#acuity_calendar_id").removeAttr('readonly');
	        	        $("#acuity_calendar_id").html(response);
	        	        $("#acuity_calendar_name").val($("#acuity_calendar_id option:selected").text());
	        	    }
	        	});
	        }
	    });
	    $("#acuity_calendar_id").change(function(){
	    	$("#acuity_calendar_name").val($("#acuity_calendar_id option:selected").text());
	    });
	});
</script>
@endsection