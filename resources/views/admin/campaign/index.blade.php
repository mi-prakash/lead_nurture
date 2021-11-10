@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            @if (Session::has('success_message'))
                <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
            @endif
            <h4>Campaigns</h4>
            <div class="text-right mb-3">
                <a href="{{ url('/admin/campaign/new/'.$campaign_tree_id) }}" class="btn btn-primary pl-4 pr-4"><i class="fa fa-plus"></i> Add Campaign</a>
            </div>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Campaign</th>
                        <th scope="col">Category</th>
                        <th scope="col">Added</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $x = 1;
                    @endphp
                    @foreach ($campaigns as $campaign)
                        <tr>
                            <th scope="row">{{$x}}</th>
                            <td>{{$campaign->name}}</td>
                            <td>{{$campaign->campaign_tree->campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign->campaign_tree->name}}</td>
                            <td>{{$campaign->created_at}}</td>
                            <td>
                                <a href="{{ url('/admin/campaign/edit/'.$campaign->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm delete_campaign" data-id="{{$campaign->id}}"><i class="fa fa-trash"></i></a>
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

<script>
    $(document).ready(function() {
        var base_url = "{{ url('/') }}";
        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }
        $(".table").DataTable();

        $(".delete_campaign").click(function(e) {
            var id = $(this).data('id');
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: base_url+"/admin/campaign/destroy/"+id,
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