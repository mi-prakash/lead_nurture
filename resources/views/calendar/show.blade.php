<div class="col">
    <div class="mb-3">
        <p class="mb-0"><i class="fa fa-user"></i> <b>Name:</b> {{$appointment->lead->first_name.' '.$appointment->lead->last_name}}</p>
        <p class="mb-0"><i class="fa fa-phone"></i> <b>Phone:</b> {{$appointment->lead->phone}}</p>
        <p class="mb-0"><i class="fa fa-calendar-o"></i> <b>Appointment Date:</b> {{date('d M, Y', strtotime($appointment->date_time))}}</p>
        <p class="mb-0"><i class="fa fa-clock-o"></i> <b>Appointment Time:</b> {{date('h:i A', strtotime($appointment->time)).' ~ '.date('h:i A', strtotime($appointment->end_time))}}</p>
    </div>
    <div class="mb-3 text-center">
        <button type="button" class="btn btn-danger cancel_schedule"><i class="fa fa-close"></i> Cancel</button>
        <button type="button" class="btn btn-primary reschedule"><i class="fa fa-undo"></i> Reschedule</button>
    </div>
</div>
<script>
    $(document).ready(function() {
        var base_url = "{{ url('/') }}";

        $('.reschedule').click(function(){
            var appointment_id = '{{$appointment->appointment_id}}';
            $.ajax({
                url: base_url+"/home/calendar/show_reschedule/"+appointment_id,
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Appointment Reschedule");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                }
            });
        });

         $('.cancel_schedule').click(function(){
            if (confirm("Are you sure you want to cancel this Appointment?")) {
                var appointment_id = '{{$appointment->appointment_id}}';
                $.ajax({
                    url: base_url+"/home/calendar/cancle_schedule/"+appointment_id,
                    cache: false,
                    type: "GET",
                    data: {},
                    beforeSend: function() {
                    },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload();
                        }
                    }
                });
            }
                
        });
    });
</script>