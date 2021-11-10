@extends('layouts.app')

@section('content')
<style>
    table { box-shadow: #bfbfbf 2px 2px 5px; }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            <h4>Lead Information</h4>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Lead ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Email</th>
                        <th scope="col" class="text-center">Appointment</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leads as $lead)
                        <tr>
                            <th scope="row">{{$lead->id}}</th>
                            <td>{{$lead->first_name}} {{$lead->last_name}}</td>
                            <td>{{$lead->phone}}</td>
                            <td>{{$lead->email}}</td>
                            <td class="text-center">@php echo !empty($lead->is_appointment) ? "<span class='badge badge-pill badge-success'>Yes</span>" : "<span class='badge badge-pill badge-danger'>No</span>"; @endphp</td>
                            <td>
                                @if (!empty($lead->is_appointment))
                                    <a href="{{url('/home/appointment/'.$lead->id)}}" class="btn btn-primary btn-sm"><i class="fa fa-file"></i> Appointments</a>
                                @else
                                    <a href="#" class="btn btn-primary btn-sm disabled"><i class="fa fa-file"></i> Appointments</a>
                                @endif
                                <a href="{{url('/home/messages/'.$lead->id)}}" class="btn btn-dark btn-sm"><i class="fa fa-comments"></i> Message</a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });
</script>
@endsection
