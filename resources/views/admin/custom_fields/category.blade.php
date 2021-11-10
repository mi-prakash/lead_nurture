@extends('layouts.admin')

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
            
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="system-field-tab" href="{{ url('/admin/custom_fields') }}">System Fields</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="category-tab" href="{{ url('/admin/custom_fields/category') }}">Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="campaign-tree-tab" href="{{ url('/admin/custom_fields/campaign_tree') }}">Campaign Tree</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="mt-4">
                        <form class="mt-5" action="{{ url('/admin/custom_fields/category') }}" method="GET">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="campaign_name" class="form-label">Category</label>
                                    <select class="form-control" id="campaign_category_id" name="campaign_category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{$category->id}}" @if (isset($campaign_category_id) && ($campaign_category_id == $category->id)){{'selected'}}@endif>{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="campaign_name" class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control submit">Go</button>                                
                                </div>
                            </div>
                        </form>
                        @if (isset($campaign_category) && isset($data_fields))
                            <h5 class="mt-3">Selected Campaign Category: {{$campaign_category->name}}</h5>
                            <div class="text-right mb-3">
                                <a href="#" class="btn btn-primary add_custom_field pl-4 pr-4" data-toggle="modal" data-target="#appModal" data-id="{{$campaign_category_id}}"><i class="fa fa-plus"></i> Add New Custom Field</a>
                            </div>
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
                                            <td>{{$data->value}}</td>
                                            <td>
                                                <a href="#" class="btn btn-primary btn-sm edit_custom_field" data-id="{{$data->id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-edit"></i></a>
                                                <a href="#" class="btn btn-danger btn-sm delete_custom_field" data-id="{{$data->id}}"><i class="fa fa-trash"></i></a>
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

        $(".add_custom_field").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/custom_fields/category/add/"+id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Add New Custom Field");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
        $(".edit_custom_field").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/custom_fields/category/edit/"+id,
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
                    url: base_url+"/admin/custom_fields/category/delete/"+id,
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