<div class="col">    
    <form action="{{ url('admin/campaign/campaign_tree/save_copy/'.$campaign_tree_id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="to_category_id" class="form-label">To Campaign Category</label>
            <select class="form-control" id="to_category_id" name="to_category_id" required>
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="to_tree_id" class="form-label">To Campaign Tree</label>
            <select class="form-control" id="to_tree_id" name="to_tree_id" required>
                <option value="">Select Campaign Tree</option>
            </select>
        </div>
        <div class="text-center">OR</div>
        <div class="mb-3">
            <label for="to_new_tree" class="form-label">To New Campaign Tree</label>
            <input type="text" class="form-control" id="to_new_tree" name="to_new_tree" value="" required>
        </div>
        <div class="mb-3 float-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        var base_url = "{{ url('/') }}";

        $("#to_category_id").change(function() {
            var campaign_category_id = $(this).val();
            var copy_tree_id = '{{$campaign_tree_id}}';
            var copy_category_id = '{{$campaign_tree->campaign_category_id}}'
            if (campaign_category_id != "") {
                $.ajax({
                    url: base_url+"/admin/custom_fields/campaign_tree/get_campaign_tree/"+campaign_category_id,
                    cache: false,
                    type: "GET",
                    data: {},
                    beforeSend: function() {

                    },
                    success: function(response) {
                        $("#to_tree_id").html(response);
                        $("#to_tree_id option").each(function(){
                            if (campaign_category_id == copy_category_id && copy_tree_id == $(this).val()) {
                                $(this).remove();
                            }
                        });
                    }
                });
            }
        });

        $("#to_tree_id").change(function() {
            var campaign_tree_id = $(this).val();
            if (campaign_tree_id != "") {
                $("#to_new_tree").removeAttr('required');
                $("#to_tree_id").prop('required',true);
            }
        });

        $("#to_new_tree").change(function() {
            var campaign_new_tree = $(this).val();
            if (campaign_new_tree != "") {
                $("#to_tree_id").removeAttr('required');
                $("#to_new_tree").prop('required',true);
            }
        });
    });
</script>