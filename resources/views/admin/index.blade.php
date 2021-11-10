@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            <h4>End Users</h4>
            <div class="text-right mb-3">
                <a href="{{ url('admin/user/create') }}" class="btn btn-primary pl-4 pr-4"><i class="fa fa-plus"></i> Add User</a>
            </div>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Identifier</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <th scope="row">{{$user->identifier}}</th>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <a href="{{ url('/admin/user/edit/'.$user->identifier) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                <a href="{{ url('/admin/user/campaign_tree_assign/'.$user->id) }}" class="btn btn-success btn-sm"><i class="fa fa-list"></i> Campaign Tree Assign</a>
                                <a href="{{ url('/users/'.$user->id.'/impersonate') }}" class="btn btn-dark btn-sm" target="_blank"><i class="fa fa-user-secret"></i> Impersonate</a>
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

        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }
    });
</script>
@endsection