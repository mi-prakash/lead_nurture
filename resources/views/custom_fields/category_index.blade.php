@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            @if (Session::has('error_message'))
                <div id="danger-msg" class="alert alert-danger custom-alert" role="alert">{{ Session::get('error_message') }}</div>
            @endif
            @error('name')
                <div id="danger-msg" class="alert alert-danger custom-alert" role="alert">{{ $message }}</div>
            @enderror
            <h4>Custom Fields</h4>
            
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" id="campaign-tree-tab" href="{{ url('/home/custom_fields/category') }}">Campaign Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="campaign-tree-tab" href="{{ url('/home/custom_fields/campaign_tree') }}">Campaign Tree</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="campaign-tree-tab" href="{{ url('/home/custom_fields/time_settings') }}">Time Settings</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="mt-4">
                        @if (isset($campaign_category) && isset($campaign_tree) && isset($data_fields))
                            <h5 class="mt-3">Campaign Category: {{$campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign_tree->name}}</h5>
                            <table class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Field Name</th>
                                        <th scope="col">Field Value</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $x = 1;
                                    @endphp
                                    @foreach ($data_fields as $data)
                                        <tr>
                                            <td>{{$x}}</td>
                                            <td>{{$data->name}}</td>
                                            <td>{{$data->user_value}}</td>
                                            <td>
                                                <a href="#" class="btn btn-primary btn-sm edit_custom_field" data-id="{{$data->id}}" data-ucf_id="{{$data->user_custom_fields_id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-edit"></i></a>
                                            </td>
                                        </tr>
                                        @php
                                            $x++;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
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

        $(".edit_custom_field").click(function(e) {
            var id = $(this).data('id');
            var user_custom_field_id = $(this).data('ucf_id');
            if(user_custom_field_id == '') {
                user_custom_field_id = 0;
            }
            $.ajax({
                url: base_url+"/home/custom_fields/edit/"+id+"/"+user_custom_field_id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Edit Custom Field");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
        $(".delete_custom_field").click(function(e) {
            var id = $(this).data('id');
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: base_url+"/admin/custom_fields/campaign_tree/delete/"+id,
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