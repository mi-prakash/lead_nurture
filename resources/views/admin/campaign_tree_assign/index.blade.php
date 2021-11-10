@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            <h4>User Campaign Tree Assign for <b>{{$user->name}}</b></h4>
            <div class="text-right mb-3">
                {{-- <a href="{{ url('admin/user/add_new_campaign_tree') }}" class="btn btn-primary pl-4 pr-4"><i class="fa fa-plus"></i> Add New Campaign Tree</a> --}}
                <a href="#" class="btn btn-primary add_campaign_tree pl-4 pr-4" data-toggle="modal" data-target="#appModal"><i class="fa fa-plus"></i> Add New Campaign Tree</a>
            </div>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Campaign Category</th>
                        <th scope="col">Campaign Tree</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                	@php
                		$x = 1;
                	@endphp
                    @foreach ($user_campaings as $user_campaing)
                        <tr>
                            <th scope="row">{{$x}}</th>
                            <td>{{$user_campaing->campaign_category->name}}</td>
                            <td>{{$user_campaing->campaign_tree->name}}</td>
                            <td>
                            	@if ($user_campaing->status == 'active')
                            		<span class='badge badge-pill badge-success'>{{ucfirst($user_campaing->status)}}</span>
                            	@else
                            		<span class='badge badge-pill badge-danger'>{{ucfirst($user_campaing->status)}}</span>
                            	@endif
                            	
                            </td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm edit_campaign_tree" data-id="{{$user_campaing->id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-edit"></i> Change Status</a>
                            </td>
                        </tr>
                        @php
                        	$x++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="appModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
    	var base_url = "{{ url('/') }}";
        $('.table').DataTable();

        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }

        $(".add_campaign_tree").click(function(e) {
        	var user_id = "{{$user_id}}";
            $.ajax({
                url: base_url+"/admin/user/add_new_campaign_tree/"+user_id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Add New Campaign Tree");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });

        $(".edit_campaign_tree").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/user/edit_campaign_tree/"+id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Edit Campaign Category");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
    });
</script>
@endsection