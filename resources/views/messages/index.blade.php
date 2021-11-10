@extends('layouts.app')

@section('content')
<link href="{{ asset('css/message.css') }}" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 ml-auto mr-auto">
            <h3 class=" text-center">Messaging</h3>
            <div class="messaging">
                <div class="inbox_msg">
                    <div class="inbox_people">
                        <div class="headind_srch">
                            <div class="recent_heading">
                                <h4>Recent</h4>
                            </div>
                        </div>
                        <div class="inbox_chat">
                            @foreach ($latest_leads_data as $latest_lead_data)
                                <a href="{{url('/home/messages/'.$latest_lead_data['lead_id'])}}">
                                    <div class="chat_list @if ($lead_id == $latest_lead_data['lead_id']){{'active_chat'}}@endif">
                                        <div class="chat_people">
                                            <div class="chat_img"><img src="https://ptetutorials.com/images/user-profile.png"></div>
                                            <div class="chat_ib">
                                                <h5>{{$latest_lead_data['details']['lead']['first_name']}} {{$latest_lead_data['details']['lead']['last_name']}}<span class="chat_date">{{date('h:i A | d M y', strtotime($latest_lead_data['details']['created_at']))}}</span></h5>
                                                <p>
                                                    @if (strlen($latest_lead_data['details']['message']) > 100)
                                                        {{substr($latest_lead_data['details']['message'], 0, 100)."..."}}
                                                    @else
                                                        {{$latest_lead_data['details']['message']}}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            @foreach ($other_leads as $other_lead)
                                <a href="{{url('/home/messages/'.$other_lead->id)}}">
                                    <div class="chat_list @if ($lead_id == $other_lead->id){{'active_chat'}}@endif">
                                        <div class="chat_people">
                                            <div class="chat_img"><img src="https://ptetutorials.com/images/user-profile.png"></div>
                                            <div class="chat_ib">
                                                <h5>{{$other_lead->first_name}} {{$other_lead->last_name}}<span class="chat_date"></span></h5>
                                                <p></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="mesgs">
                        <h5 class="mesgs-name text-center">{{$name}} {{"($phone)"}}</h5>
                        <div id="msg_history" class="msg_history">
                            @foreach ($last_lead_messages as $last_lead_message)
                                @if ($last_lead_message->is_incoming != 0)
                                    <div class="incoming_msg">
                                        <div class="incoming_msg_img"><img src="https://ptetutorials.com/images/user-profile.png"></div>
                                        <div class="received_msg">
                                            <div class="received_withd_msg">
                                                <p>{{$last_lead_message->message}}</p>
                                                <span class="time_date">{{date('h:i A | d M Y', strtotime($last_lead_message->created_at))}}</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="outgoing_msg">
                                        <div class="sent_msg">
                                            <p>{{$last_lead_message->message}}</p>
                                            <span class="time_date">{{date('h:i A | d M Y', strtotime($last_lead_message->created_at))}}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="type_msg">
                            <div class="input_msg_write">
                                <form method="POST" action="{{url('home/messages/send_message')}}">
                                    @csrf
                                    <input type="hidden" name="lead_id" value="{{$lead_id}}">
                                    <input type="text" class="write_msg" name="message" placeholder="Type a message"/>
                                    <button class="msg_send_btn" type="submit"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        //scroll to the bottom of "#myDiv"
        var myDiv = document.getElementById("msg_history");
        myDiv.scrollTop = myDiv.scrollHeight;
    });
</script>
@endsection
