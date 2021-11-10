@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            @error('name')
                <div id="danger-msg" class="alert alert-danger custom-alert" role="alert">{{ $message }}</div>
            @enderror

            <h4>Settings</h4>

            <div class="mt-4">
                <form class="mt-5" action="{{ url('/admin/settings/save') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="timezone" class="form-label">Timezone</label>
                        <select class="form-control" id="timezone" name="timezone" style="width:60%;">
                            <option value="">Select Timezone</option>
                            @foreach ($timezones as $key => $value)
                                <option value="{{$key}}" @if (!empty($timezone_settings) && ($timezone_settings->timezone == $key)){{'selected'}}@endif>{{"(GMT ".$value." hrs) ".$key}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>                                
                    </div>
                </form>
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
    });
</script>
@endsection