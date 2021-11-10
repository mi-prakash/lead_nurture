<div class="col">    
    <form action="{{ url('admin/message_rule_categories/expressions/store/'.$message_rule_category_id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Category</label>
            <input type="text" class="form-control" value="{{$message_rule_category->name}}" readonly>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Expression</label>
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