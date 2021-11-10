<div class="col">    
    <form action="{{ url('admin/user/update_campaign_tree/'.$user_campaing->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{$user_campaing->campaign_category->name}} <i class="fa fa-long-arrow-right"></i> {{$user_campaing->campaign_tree->name}}</label>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Status</label>
            <div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="active" value="active" @if ($user_campaing->status == 'active'){{'checked'}} @endif>
                  <label class="form-check-label" for="active">Active</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="inactive" value="inactive" @if ($user_campaing->status == 'inactive'){{'checked'}} @endif>
                  <label class="form-check-label" for="inactive">Inactive</label>
                </div>
            </div>
        </div>
        <div class="mb-3 float-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
    });
</script>