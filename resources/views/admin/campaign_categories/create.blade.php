<div class="col">    
    <form action="{{ url('admin/campaign_category/store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Campaign Category Name</label>
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