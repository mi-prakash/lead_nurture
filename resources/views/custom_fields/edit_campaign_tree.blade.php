<div class="col">    
    
    @if (!empty($user_custom_field))
        <form action="{{ url('home/custom_fields/update/'.$user_custom_field->id) }}" method="POST">
    @else
        <form action="{{ url('home/custom_fields/update/0') }}" method="POST">
    @endif
        @csrf
        <div class="mb-3">
            <input type="hidden" name="custom_field_id" value="{{$custom_field->id}}">
            <input type="hidden" name="campaign_category_id" value="{{$custom_field->campaign_category_id}}">
            <input type="hidden" name="campaign_tree_id" value="{{$custom_field->campaign_tree_id}}">
            @if (!empty($user_custom_field))
                <input type="hidden" name="user_custom_field_id" value="{{$user_custom_field->id}}">
            @endif
            <label for="name" class="form-label">Field Key</label>
            <input type="text" class="form-control" id="name" name="name" value="{{$custom_field->name}}" readonly>
        </div>
        <div class="mb-3">
            <label for="value" class="form-label">Field Value</label>
        @if (!empty($user_custom_field))
            <input type="text" class="form-control" id="value" name="value" value="{{$user_custom_field->value}}" required>
        @else
            <input type="text" class="form-control" id="value" name="value" value="" required>
        @endif
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