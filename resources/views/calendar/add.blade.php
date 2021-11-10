<div class="col">
    <form action="{{ url('home/calendar/save_schedule') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="date" class="form-label">Contacts</label>
            <select class="form-control" id="lead_id" name="lead_id" required>
                <option value="">Select a Contact</option>
                @foreach ($leads as $lead)
                    <option value="{{$lead->id}}">{{$lead->first_name.' '.$lead->last_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="text" class="form-control datepicker" id="date" name="date" value="" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="time" class="form-label">Time</label>
            <input type="text" class="form-control timepicker" id="time" name="time" value="" required>
        </div>
        <div class="mb-3 float-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            startDate: new Date()
        });
        $('.timepicker').timepicker({
            minuteStep: 5
        });
    });
</script>