@extends('layouts.app')

@section('content')
<style>
    table { box-shadow: #bfbfbf 2px 2px 5px; }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h4>Scheduled Information</h4>
            <p>Name: {{$name}}</p>
            <p>Phone: {{$phone}}</p>
            <p>Email: {{$email}}</p>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Created date</th>
                        <th scope="col">Appointment ID</th>
                        <th scope="col">Appointment date</th>
                        <th scope="col">Date time</th>
                        <th scope="col">Time</th>
                        <th scope="col">User timezone</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $x = 1;
                    @endphp
                    @foreach ($appointment_by_lead_id as $appointment)
                        <tr>
                            <th scope="row">{{$x}}</th>
                            <td>{{$appointment->date_created}}</td>
                            <td>{{$appointment->appointment_id}}</td>
                            <td>{{$appointment->date}}</td>
                            <td>{{date('Y-m-d H:i:s', strtotime($appointment->date_time))}}</td>
                            <td>{{date('h:i A', strtotime($appointment->converted_time))}} - {{date('h:i A', strtotime($appointment->converted_end_time))}}</td>
                            <td>{{$appointment->timezone}}</td>
                            <td>
                                @if ($appointment->status == "scheduled")
                                    <span class="badge badge-pill badge-primary">{{ucfirst($appointment->status)}}</span>
                                @elseif ($appointment->status == "rescheduled")
                                    <span class="badge badge-pill badge-dark">{{ucfirst($appointment->status)}}</span>
                                @elseif ($appointment->status == "canceled")
                                    <span class="badge badge-pill badge-danger">{{ucfirst($appointment->status)}}</span>
                                @elseif ($appointment->status == "changed")
                                    <span class="badge badge-pill badge-secondary">{{ucfirst($appointment->status)}}</span>
                                @elseif ($appointment->status == "completed")
                                    <span class="badge badge-pill badge-success">{{ucfirst($appointment->status)}}</span>
                                @else
                                    <span class="badge badge-pill badge-light">{{ucfirst($appointment->status)}}</span>
                                @endif 
                            </td>
                            <td>
                            	<a href="{{url('/home/appointment/detail/'.$appointment->appointment_id)}}" class="btn btn-primary btn-sm" style="min-width: 74px;"><i class="fa fa-list"></i> Details</a>
                            </td>
                        </tr>
                        @php
                            $x++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            <div class="text-center">
            	<a href="{{url('/home')}}" class="btn btn-danger btn-sm"><i class="fa fa-undo"></i> Back</a>
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