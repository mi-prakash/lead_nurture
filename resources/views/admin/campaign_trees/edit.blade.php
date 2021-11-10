<div class="col">    
    <form action="{{ url('admin/campaign_tree/update/'.$campaign_tree->id) }}" method="POST">
        @csrf
        <input type="hidden" name="campaign_category_id" value="{{$campaign_category->id}}">
        <div class="mb-3">
            <label for="name" class="form-label">Campaign Category Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{$campaign_category->name}}" readonly>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Campaign Tree Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{$campaign_tree->name}}" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Status</label>
            <div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="active" value="active" @if ($campaign_tree->status == 'active'){{'checked'}} @endif>
                  <label class="form-check-label" for="active">Active</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="inactive" value="inactive" @if ($campaign_tree->status == 'inactive'){{'checked'}} @endif>
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