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
                    <a class="nav-link" id="campaign-tree-tab" href="{{ url('/home/custom_fields/category') }}">Campaign Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="campaign-tree-tab" href="{{ url('/home/custom_fields/campaign_tree') }}">Campaign Tree</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="campaign-tree-tab" href="{{ url('/home/custom_fields/time_settings') }}">Time Settings</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="mt-4">
                        <h5 class="mt-3">Time Settings</h5>
                        <form action="{{ url('home/save_user_time_settings') }}" method="POST">
                            @csrf
                            <p>Do not send any auto SMS</p>
                            @php
                                $from_time = "";
                                $to_time = "";
                                if(!empty($user_time_setting)) {
                                    $from_time = date('h:i a', strtotime($user_time_setting->from_time));
                                    $to_time = date('h:i a', strtotime($user_time_setting->to_time));
                                }
                            @endphp
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="from_time" class="form-label">From</label>
                                    <input id="from_time" type="text" class="form-control" name="from_time" value="{{$from_time}}" required>
                                </div>
                                <div class="col-6">
                                    <label for="to_time" class="form-label">To</label>
                                    <input id="to_time" type="text" class="form-control" name="to_time" value="{{$to_time}}" required>
                                </div>
                            </div>
                            <div class="mb-3 text-center">
                                <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                            </div>
                        </form>
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

        $('#from_time, #to_time').timepicker({
            minuteStep: 1,
            defaultTime: false
        });

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