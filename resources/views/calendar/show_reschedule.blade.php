<div class="col">
    <form action="{{ url('home/calendar/update_reschedule/'.$appointment->appointment_id ) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="lead_name" class="form-label">Contact</label>
            <input type="text" class="form-control" id="lead_name" value="{{$appointment->lead->first_name.' '.$appointment->lead->last_name}}" readonly>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="text" class="form-control datepicker" id="date" name="date" value="{{date('Y-m-d', strtotime($appointment->date_time))}}" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="time" class="form-label">Time</label>
            <input type="text" class="form-control timepicker" id="time" name="time" value="{{date('h:i A', strtotime($appointment->time))}}" required>
        </div>
        <div class="mb-3 float-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
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
            minuteStep: 5,
            // defaultTime: false
        });
    });
</script>