@extends('layouts.app')

@section('content')
<style>
    table { box-shadow: #bfbfbf 2px 2px 5px; }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h4>Details for Appointment ID {{$appointment->appointment_id}}</h4>
            <p>Name: {{$appointment->lead->first_name}} {{$appointment->lead->last_name}}</p>
            <p>Phone: {{$appointment->lead->phone}}</p>
            <p>Email: {{$appointment->lead->email}}</p>
        	<table class="table table-hover">
        		<thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Action datetime</th>
                        <th scope="col">Appointment date</th>
                        <th scope="col">Time</th>
                        <th scope="col">User timezone</th>
                        <th scope="col">Webhook action</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $x = 1;
                    @endphp
                    @foreach ($appointment_logs as $appointment_log)
                    	@php
		            		$data = json_decode($appointment_log->json_data, true);
		            	@endphp
                        <tr>
                            <th scope="row">{{$x}}</th>
                            <td>{{date('m/d/Y h:i:s A', strtotime($appointment_log->created_at))}}</td>
                            <td>{{$data['date']}}</td>
                            <td>
                            	@if (isset($data['converted_time']) && $data['converted_end_time'])
                            		{{date('h:i A', strtotime($data['converted_time']))}} - {{date('h:i A', strtotime($data['converted_end_time']))}}
                            	@endif
                            </td>
                            <td>{{$data['timezone']}}</td>
                            <td>
                            	@if ($appointment_log->status == "scheduled")
                            	    <span class="badge badge-pill badge-primary">{{ucfirst($appointment_log->status)}}</span>
                            	@elseif ($appointment_log->status == "rescheduled")
                            	    <span class="badge badge-pill badge-dark">{{ucfirst($appointment_log->status)}}</span>
                            	@elseif ($appointment_log->status == "canceled")
                            	    <span class="badge badge-pill badge-danger">{{ucfirst($appointment_log->status)}}</span>
                            	@elseif ($appointment_log->status == "changed")
                            	    <span class="badge badge-pill badge-secondary">{{ucfirst($appointment_log->status)}}</span>
                            	@elseif ($appointment_log->status == "completed")
                            	    <span class="badge badge-pill badge-success">{{ucfirst($appointment_log->status)}}</span>
                            	@else
                            	    <span class="badge badge-pill badge-light">{{ucfirst($appointment_log->status)}}</span>
                            	@endif
                            </td>
                            <td>
                            	<a href="{{url('/home/appointment/detail/log/'.$appointment->appointment_id.'/'.$appointment_log->id)}}" class="btn btn-primary btn-sm"><i class="fa fa-list"></i> Details</a>
                            </td>
                        </tr>
                        @php
                            $x++;
                        @endphp
                    @endforeach
                </tbody>
        	</table>
            <div class="text-center">
            	<a href="{{url('/home/appointment/'.$appointment->lead_id)}}" class="btn btn-danger btn-sm"><i class="fa fa-undo"></i> Back</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });
</script>
@endsection