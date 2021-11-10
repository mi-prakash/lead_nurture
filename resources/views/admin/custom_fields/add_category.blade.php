<div class="col">    
    <form action="{{ url('admin/custom_fields/category/store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="hidden" name="campaign_category_id" value="{{$campaign_category_id}}">
            <label for="name" class="form-label">Field Key</label>
            <input type="text" class="form-control" id="name" name="name" value="" required>
            <small class="form-text text-muted"><em>without space and uppercase</em></small>
        </div>
        <div class="mb-3">
            <label for="value" class="form-label">Field Value</label>
            <input type="text" class="form-control" id="value" name="value" value="" required>
            <small class="form-text text-muted"><em>default value</em></small>
        </div>
        <div class="mb-3 float-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
    });
</script>