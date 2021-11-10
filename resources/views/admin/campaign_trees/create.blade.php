<div class="col">    
    <form action="{{ url('admin/campaign_tree/store') }}" method="POST">
        @csrf
        <input type="hidden" name="campaign_category_id" value="{{$campaign_category->id}}">
        <div class="mb-3">
            <label for="name" class="form-label">Campaign Category Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{$campaign_category->name}}" readonly>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Campaign Tree Name</label>
            <input type="text" class="form-control" id="name" name="name" value="" required>
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