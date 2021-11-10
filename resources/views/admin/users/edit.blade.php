@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            <h4>Manage User - {{$user->identifier}}</h4>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10 ml-auto mr-auto">
                            <form method="POST" action="{{url('admin/user/update/'.$user->identifier)}}">
                                @csrf
                                @foreach ($errors->all() as $error)
                                    <p class="text-danger text-center">{{ $error }}</p>
                                @endforeach 
                                <div class="form-group row">
                                    <label for="identifier" class="col-md-3 col-form-label text-md-right">Identifier</label>
                                    <div class="col-md-9">
                                        <input type="text" readonly class="form-control-plaintext" id="identifier" value="{{$user->identifier}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="name" class="col-md-3 col-form-label text-md-right">Name</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-md-3 col-form-label text-md-right">Email</label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}">
                                    </div>
                                </div>
                                <p class="form-group mt-4 text-center"><b>Change Password</b></p>
                                <div class="form-group row">
                                    <label for="new_password" class="col-md-3 col-form-label text-md-right">New Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="new_password" name="new_password" value="" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="confirm_new_password" class="col-md-3 col-form-label text-md-right">Confirm New Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" value="" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <a class="btn btn-danger" href="{{url('admin')}}"><i class="fa fa-undo"></i> Back</a>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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