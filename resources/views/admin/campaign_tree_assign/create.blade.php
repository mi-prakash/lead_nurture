<div class="col">    
    <form action="{{ url('admin/user/save_campaign_tree') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="hidden" name="user_id" value="{{$user_id}}">
            <label for="campaign_category_id" class="form-label">Category</label>
            <select class="form-control" id="campaign_category_id" name="campaign_category_id" required>
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{$category->id}}" @if (isset($campaign_category_id) && ($campaign_category_id == $category->id)){{'selected'}}@endif>{{$category->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="campaign_tree_id" class="form-label">Campaign Tree</label>
            <select class="form-control" id="campaign_tree_id" name="campaign_tree_id" required>
                <option value="">Select Campaign Tree</option>
            </select>
        </div>
        <div class="mb-3 float-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        var base_url = "{{ url('/') }}";
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
    });
</script>