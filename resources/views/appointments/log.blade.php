@extends('layouts.app')

@section('content')
<style>
    table { box-shadow: #bfbfbf 2px 2px 5px; }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h4>Log for Appointment ID {{$appointment_log->appointment_id}}</h4>
            <div class="col-md-10 ml-auto mr-auto mt-5">
            	@php
            		$data = json_decode($appointment_log->json_data, true);
            	@endphp
            	<table class="table table-hover table-sm">
	                <tbody>
	                	@if (array_key_exists("lead", $data))
	                		@foreach ($data['lead'] as $lead_key => $lead_data)
		                		@if ($lead_key == "first_name" || $lead_key == "last_name" || $lead_key == "phone" || $lead_key == "email")
		                			<tr>
		                				<th scope="row">{{ucfirst(str_replace('_', ' ', $lead_key))}}</th>
		                				<td>{{$lead_data}}</td>
		                			</tr>
		                		@endif
	                		@endforeach
	                	@endif
	                	@foreach ($data as $key => $value)
	                		<tr>
	                			@if ($key != 'id' && $key != 'lead_id' && $key != 'lead' && $key != 'json_data')
		                			<th scope="row">{{ucfirst(str_replace('_', ' ', $key))}}</th>
		                			<td>
		                				@if ($key == 'time' || $key == 'end_time' || $key == 'converted_time' || $key == 'converted_end_time')
		                					{{date('h:i A', strtotime($value))}}
		                				@elseif ($key == 'date_time' || $key == 'date_time_created')
		                					{{date('m/d/Y h:i:s A', strtotime($value))}}
		                				@elseif ($key == 'canceled' || $key == 'can_client_cancel' || $key == 'can_client_reschedule')
		                					{{(!empty($value)) ? 'Yes' : 'No'}}
		                				@elseif ($key == 'paid' || $key == 'status')
		                					{{ucfirst($value)}}
		                				@else
		                					{{$value}}
		                				@endif
		                			</td>
		                		@endif
	                		</tr>
	                	@endforeach
	                </tbody>
	            </table>
	            <div class="text-center">
	            	<a href="{{url('/home/appointment/detail/'.$appointment_log->appointment_id)}}" class="btn btn-danger btn-sm"><i class="fa fa-undo"></i> Back</a>
	            </div>
            </div>      
        </div>
    </div>
</div>
@endsection