@extends('layouts.app')

@section('content')
<style>
    table { box-shadow: #bfbfbf 2px 2px 5px; }
    #calendar a {
        cursor: pointer !important;
        color: #20232a;
    }
    .hidden {
        display: none;
    }
</style>
<div class="container">
    @if (Session::has('success_message'))
        <div id="success-msg" class="alert alert-primary custom-alert" role="alert">{{ Session::get('success_message') }}</div>
    @endif
    @if (Session::has('error_message'))
        <div id="danger-msg" class="alert alert-danger custom-alert" role="alert">{{ Session::get('error_message') }}</div>
    @endif
    
    <div id="success-msg-manual" class="alert alert-primary manual-alert hidden" role="alert">Successfully updated</div>
    <div id="danger-msg-manual" class="alert alert-danger manual-alert hidden" role="alert">Appointment Time is already booked by other client</div>

    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            <h4>Calendar</h4>
            <div id='calendar'></div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="appModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fa fa-refresh fa-spin fa-3x mt-4 mb-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var base_url = "{{ url('/') }}";
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            // initialDate: '2020-09-12',
            navLinks: true, // can click day/week names to navigate views
            businessHours: true, // display business hours
            editable: true, // enable drag and drop event
            selectable: false,
            eventClick: function(arg) {
                var id = arg.event.id;
                $.ajax({
                    url: base_url+"/home/calendar/show_schedule/"+id,
                    cache: false,
                    type: "GET",
                    data: {},
                    beforeSend: function() {
                        $("#modalLabel").text("Schedule Details");
                    },
                    success: function(response) {
                        $(".modal-body").html(response);
                        $('#appModal').modal('toggle');
                    }
                });
            },
            eventDrop: function(arg) {
                var appointment_id = arg.event.id;
                var date = arg.event.start.toISOString();
                // console.log("ID "+ appointment_id + " " + arg.event.title + " was dropped on " + arg.event.start.toISOString());

                if (!confirm("Are you sure about this change?")) {
                    arg.revert();
                } else {
                    $.ajax({
                        url: base_url+"/home/calendar/update_reschedule/"+appointment_id,
                        cache: false,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            from_calendar: 1,
                            date: date,

                        },
                        beforeSend: function() {
                        },
                        success: function(response) {
                            if (response == 'success') {
                                $("#danger-msg-manual").fadeOut();
                                $("#success-msg-manual").fadeIn();
                            } else if (response == 'error_appointment_time') {
                                $("#success-msg-manual").fadeOut();
                                $("#danger-msg-manual").fadeIn();
                                arg.revert();
                            }
                            setTimeout(function(){ $(".manual-alert").fadeOut(); }, 5000);
                        }
                    });
                }
            },
            events: [
            @php
                foreach ($appointments as $appointment) {
                    echo "
                    {
                        title: '".$appointment->lead->first_name." ".$appointment->lead->last_name."',
                        start: '$appointment->date_time',
                        end: '".date('Y-m-d '.$appointment->end_time.':00', strtotime($appointment->date_time))."',
                        id: $appointment->appointment_id
                    },
                    ";
                }
            @endphp
            ]
        });

        calendar.render();
    });

    $(document).ready(function() {
        var base_url = "{{ url('/') }}";
        if ($(".custom-alert").length) {
            setTimeout(function(){ $(".custom-alert").remove(); }, 5000);
        }
        $('.fc-header-toolbar').before("<div class='mb-2'><button class='fc-button fc-button-primary float-right add-schedule' type='button'>Add New Schedule</button></div>");

        $('.add-schedule').click(function(){
            // $('#appModal').modal('toggle');
            $.ajax({
                url: base_url+"/home/calendar/add_schedule",
                cache: false,
                type: "GET",
                data: {},
                beforeSend: function() {
                    $("#modalLabel").text("Add New Schedule");
                },
                success: function(response) {
                    $(".modal-body").html(response);
                    $('#appModal').modal('toggle');
                }
            });
        });
    });
</script>
@endsection
