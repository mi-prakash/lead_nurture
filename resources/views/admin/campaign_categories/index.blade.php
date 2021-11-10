@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            @error('name')
                <div id="danger-msg" class="alert alert-danger custom-alert" role="alert">{{ $message }}</div>
            @enderror
            <h4>Manage Campaign Tree</h4>
            <div class="text-right mb-3">
                <a href="#" class="btn btn-primary add_campaign_category pl-4 pr-4" data-toggle="modal" data-target="#appModal"><i class="fa fa-plus"></i> Add Campaign Category</a>
            </div>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Category</th>
                        <th scope="col">Campaign Tree</th>
                        <th scope="col">Campaign Count</th>
                        <th scope="col">Status</th>
                        <th scope="col">Added</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $x = 1;
                    @endphp
                    @foreach ($campaign_categories as $campaign_category)
                        @php
                            $count = 0;
                        @endphp
                        <tr class="table-primary">
                            <th scope="row">{{$x}}</th>
                            <td>{{$campaign_category->name}}</td>
                            <td></td>
                            <td>
                                @foreach ($campaign_category->campaign_trees as $campaign_tree)
                                    @php
                                        $count = $count + count($campaign_tree->campaigns->toArray())
                                    @endphp
                                @endforeach
                                {{$count}}
                            </td>
                            <td>
                                @if ($campaign_category->status == 'active')
                                    <span class="badge badge-pill badge-success">{{ucfirst($campaign_category->status)}}</span>
                                @else
                                    <span class="badge badge-pill badge-danger">{{ucfirst($campaign_category->status)}}</span>
                                @endif
                            </td>
                            <td>{{$campaign_category->created_at}}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm edit_campaign_category" data-id="{{$campaign_category->id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm delete_campaign_category" data-id="{{$campaign_category->id}}"><i class="fa fa-trash"></i></a>
                                <a href="#" class="btn btn-success btn-sm add_campaign_tree" target="_blank" data-id="{{$campaign_category->id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-plus-square-o"></i></a>
                            </td>
                        </tr>
                        @foreach ($campaign_category->campaign_trees as $campaign_tree)
                            @php
                                ++$x;
                            @endphp
                            <tr>
                                <th scope="row">{{$x}}</th>
                                <td>{{$campaign_category->name}}</td>
                                <td>{{$campaign_tree->name}}</td>
                                <td>{{count($campaign_tree->campaigns->toArray())}}</td>
                                <td>
                                    @if ($campaign_tree->status == 'active')
                                        <span class="badge badge-pill badge-success">{{ucfirst($campaign_tree->status)}}</span>
                                    @else
                                        <span class="badge badge-pill badge-danger">{{ucfirst($campaign_tree->status)}}</span>
                                    @endif
                                </td>
                                <td>{{$campaign_tree->created_at}}</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm edit_campaign_tree" data-id="{{$campaign_tree->id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-edit"></i></a>
                                    <a href="#" class="btn btn-danger btn-sm delete_campaign_tree" data-id="{{$campaign_tree->id}}"><i class="fa fa-trash"></i></a>
                                    @if (count($campaign_tree->campaigns->toArray()) != 0)
                                        <a href="#" class="btn btn-info btn-sm copy_campaign_tree" data-id="{{$campaign_tree->id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-copy"></i></a>
                                    @endif
                                    <a href="{{ url('/admin/campaign/'.$campaign_tree->id) }}" class="btn btn-dark btn-sm"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('/admin/campaign/new/'.$campaign_tree->id) }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a>
                                </td>
                            </tr>
                        @endforeach
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
        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }
        $(".table").DataTable();
        $(".add_campaign_category").click(function(e) {
            $.ajax({
                url: base_url+"/admin/campaign_category/add",
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("New Campaign Category");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
        $(".edit_campaign_category").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/campaign_category/edit/"+id,
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
        $(".delete_campaign_category").click(function(e) {
            var id = $(this).data('id');
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: base_url+"/admin/campaign_category/destroy/"+id,
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
        });
        // Campaign tree
        $(".add_campaign_tree").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/campaign_tree/add/"+id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Add Campaign Tree");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
        $(".edit_campaign_tree").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/campaign_tree/edit/"+id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Edit Campaign Tree");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
        $(".copy_campaign_tree").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/campaign/campaign_tree/copy/"+id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Copy Campaign Tree");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
        $(".delete_campaign_tree").click(function(e) {
            var id = $(this).data('id');
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: base_url+"/admin/campaign_tree/destroy/"+id,
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
        });
    });
</script>
@endsection