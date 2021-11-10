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

            <h4>Campaign Trigger</h4>

            <div class="mt-4">
                <form class="mt-5" action="{{ url('/admin/campaign_trigger') }}" method="GET">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="campaign_category_id" class="form-label">Category</label>
                            <select class="form-control" id="campaign_category_id" name="campaign_category_id">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{$category->id}}" @if (isset($campaign_category_id) && ($campaign_category_id == $category->id)){{'selected'}}@endif>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="campaign_tree_id" class="form-label">Campaign Tree</label>
                            <select class="form-control" id="campaign_tree_id" name="campaign_tree_id">
                                <option value="">Select Campaign Tree</option>
                                @foreach ($campaign_trees as $campaign_trees)
                                    <option value="{{$campaign_trees->id}}" @if (isset($campaign_tree_id) && ($campaign_tree_id == $campaign_trees->id)){{'selected'}}@endif>{{$campaign_trees->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="campaign_name" class="form-label">&nbsp;</label>
                            <button class="btn btn-primary form-control">Go</button>                                
                        </div>
                    </div>
                </form>
                @if (isset($campaign_category->id) && isset($campaign_tree->id))
                    <div class="card mt-3 mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Selected Campaign Category Tree</h5>
                            <p class="card-text">{{$campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$campaign_tree->name}}</p>
                        </div>
                    </div>
                    <form action="{{ url('/admin/campaign_trigger/store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="campaign_category_id" value="{{$campaign_category_id}}">
                        <input type="hidden" name="campaign_tree_id" value="{{$campaign_tree_id}}">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Not yet scheduling trigger</h5>
                                        <div id="no_schedule">
                                            <div class="mb-3">
                                                <label class="form-label">Campaigns</label>
                                                @foreach ($no_schedules as $no_schedule)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="no_schedule_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}" @if ($campaign->id == $no_schedule->campaign_id){{"selected"}}@endif>{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (count($no_schedules->toArray()) == 0)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="no_schedule_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}">{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endif
                                                <button type="button" class="btn btn-outline-primary col add-more"><i class="fa fa-plus"></i> Add more</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Schedule trigger</h5>
                                        <div id="schedule">
                                            <div class="mb-3">
                                                <label class="form-label">Campaigns</label>
                                                @foreach ($schedules as $schedule)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="schedule_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}" @if ($campaign->id == $schedule->campaign_id){{"selected"}}@endif>{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (count($schedules->toArray()) == 0)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="schedule_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}">{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endif
                                                <button type="button" class="btn btn-outline-primary col add-more"><i class="fa fa-plus"></i> Add more</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Re-schedule trigger</h5>
                                        <div id="re_schedule">
                                            <div class="mb-3">
                                                <label class="form-label">Campaigns</label>
                                                @foreach ($re_schedules as $re_schedule)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="re_schedule_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}" @if ($campaign->id == $re_schedule->campaign_id){{"selected"}}@endif>{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (count($re_schedules->toArray()) == 0)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="re_schedule_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}">{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endif
                                                <button type="button" class="btn btn-outline-primary col add-more"><i class="fa fa-plus"></i> Add more</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Canceled trigger</h5>
                                        <div id="cancel">
                                            <div class="mb-3">
                                                <label class="form-label">Campaigns</label>
                                                @foreach ($cancels as $cancel)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="cancel_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}" @if ($campaign->id == $cancel->campaign_id){{"selected"}}@endif>{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (count($cancels->toArray()) == 0)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="cancel_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}">{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endif
                                                <button type="button" class="btn btn-outline-primary col add-more"><i class="fa fa-plus"></i> Add more</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">No show trigger</h5>
                                        <div id="no_show">
                                            <div class="mb-3">
                                                <label class="form-label">Campaigns</label>
                                                @foreach ($no_shows as $no_show)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="no_show_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}" @if ($campaign->id == $no_show->campaign_id){{"selected"}}@endif>{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (count($no_shows->toArray()) == 0)
                                                    <div class="input-group mb-3">
                                                        <select class="form-control change-campaign" name="no_show_campaign[]">
                                                            <option value="">Select Campaign</option>
                                                            @foreach ($campaigns as $campaign)
                                                                <option value="{{$campaign->id}}">{{$campaign->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
                                                        </div>
                                                    </div>
                                                @endif
                                                <button type="button" class="btn btn-outline-primary col add-more"><i class="fa fa-plus"></i> Add more</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- content --}}
<div class="select-content hidden">
    <div class="input-group mb-3">
        <select class="form-control change-campaign" name="name">
            <option value="">Select Campaign</option>
            @foreach ($campaigns as $campaign)
                <option value="{{$campaign->id}}">{{$campaign->name}}</option>
            @endforeach
        </select>
        <div class="input-group-append">
            <button class="btn btn-danger remove" type="button"><i class="fa fa-close"></i></button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var base_url = "{{ url('/') }}";
        var select_content = $(".select-content");

        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }
        $(".table").DataTable();

        $("#campaign_category_id").change(function() {
            var campaign_category_id = $(this).val();
            if (campaign_category_id != "") {
                $.ajax({
                    url: base_url+"/admin/custom_fields/campaign_tree/get_campaign_tree/"+campaign_category_id,
                    cache: false,
                    type: "GET",
                    data: {},
                    beforeSend: function() {

                    },
                    success: function(response) {
                        $("#campaign_tree_id").html(response);
                    }
                });
            }
        });

        $(".add-more").click(function(){
            var container = $(this).parent().parent().attr('id');
            var new_name = "";
            if (container == "no_schedule") {
                new_name = "no_schedule_campaign[]";
            } else if (container == "schedule") {
                new_name = "schedule_campaign[]";
            } else if (container == "re_schedule") {
                new_name = "re_schedule_campaign[]";
            } else if (container == "cancel") {
                new_name = "cancel_campaign[]";
            } else if (container == "no_show") {
                new_name = "no_show_campaign[]";
            }
            select_content.find(".change-campaign").attr("name", new_name);
            $(this).before(select_content.html());
        });

        $(".container").on('click', '.remove', function(){
            var parent = $(this).parent().parent();
            if (confirm('Are you sure?')) {
                parent.remove();
            }
        });

        $(".container").on('change', '.change-campaign', function(){
            var this_val = $(this);
            var container = $(this).parent().parent().parent().attr('id');
            var array_check = [];
            $("#"+container+" .change-campaign").each(function() {
                array_check.push($(this).val());
            });
            array_check = array_check.sort();
            for (var i = 0; i < array_check.length - 1; i++) {
                if (array_check[i + 1] == array_check[i]) {
                    this_val.val("");
                    alert('Duplicate data');
                }
            }
        });
    });
</script>
@endsection