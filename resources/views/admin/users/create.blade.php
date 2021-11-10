@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            <h4>Create User</h4>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10 ml-auto mr-auto">
                            <form method="POST" action="{{url('admin/user/save')}}">
                                @csrf
                                @foreach ($errors->all() as $error)
                                    <p class="text-danger text-center">{{ $error }}</p>
                                @endforeach
                                <div class="form-group row">
                                    <label for="name" class="col-md-3 col-form-label text-md-right">Name</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="name" name="name" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-md-3 col-form-label text-md-right">Email</label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" id="email" name="email" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-3 col-form-label text-md-right">Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="password" name="password" value="" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="confirm_password" class="col-md-3 col-form-label text-md-right">Confirm Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <a class="btn btn-danger" href="{{url('admin')}}"><i class="fa fa-undo"></i> Back</a>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Create</button>
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
});
</script>
@endsection