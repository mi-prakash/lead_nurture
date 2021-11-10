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

            <h4>Universal Message Rule Categories</h4>
            <div class="text-right mb-3">
                <a href="#" class="btn btn-primary add_new pl-4 pr-4" data-toggle="modal" data-target="#appModal"><i class="fa fa-plus"></i> Add Universal Message Rule Category</a>
            </div>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Category</th>
                        <th scope="col">Expression Count</th>
                        <th scope="col">Added by</th>
                        <th scope="col">Added Time</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $x = 1;
                    @endphp
                    @foreach ($message_rule_categories as $data)
                        <tr>
                            <td>{{$x}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{count($data->expressions)}}</td>
                            <td>{{$data->user->name}}</td>
                            <td>{{date('d M, Y - h:i A', strtotime($data->created_at))}}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm edit_item" data-id="{{$data->id}}" data-toggle="modal" data-target="#appModal"><i class="fa fa-edit"></i></a>
                                <a href="{{ url('/admin/message_rule_categories/expressions/'.$data->id) }}" class="btn btn-dark btn-sm"><i class="fa fa-eye"></i></a>
                                <a href="#" class="btn btn-danger btn-sm delete_custom_field" data-id="{{$data->id}}"><i class="fa fa-trash"></i></a>
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
        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }
        $(".table").DataTable();

        $(".add_new").click(function(e) {
            $.ajax({
                url: base_url+"/admin/message_rule_categories/add",
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Add Universal Message Rule Category");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });
        $(".edit_item").click(function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: base_url+"/admin/message_rule_categories/edit/"+id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Edit Universal Message Rule Category");
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
                    url: base_url+"/admin/message_rule_categories/delete/"+id,
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