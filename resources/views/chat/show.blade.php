@extends('layouts.app')
@push('styles')
<style>
   #users li 
   {
       cursor: pointer;
   }

   .sender_message 
    {
        background: #71c7a0;
        width: 50%;
        padding: 6px 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        font-weight: bold;
        clear: both;
        margin: 10px;
    }

    .receiver_message 
    {
        background: #91aad1;
        width: 50%;
        padding: 6px 15px;
        border-radius: 10px;
        margin-bottom: 10px;
        font-weight: bold;
        clear: both;
        float: right;
        text-align: right;
        margin: 10px;
    }

    .chat 
    {
        background-image: url("{{asset('img/bg2.jpg')}}");
        background-size: cover;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Chat</div>

                <div class="card-body">
                  <div class="row">
                      {{--messages --}}
                      <div class="col-9">
                          <div class="row">
                            {{-- list of messages --}}
                            <div class="col-12 border rounded-lg p-3 chat">
                                <ul id = "messages" class = "list-unstyled overflow-auto" style = "height: 45vh;">
                                   
                                </ul>
                                <div class = "text-center d-none" id = "typing_container" style = "font-weight: bold;"><span id = "typing_text"></span> <img src="{{asset('img/typing1.gif')}}" style = "width: 70px;" alt="typing"></div>
                            </div>
                          </div>
                          {{--send message--}}
                          <form>
                              <div class = "row py-3">
                                  <div class="col-10">
                                    <input type="text" id = "message" class = "form-control" placeholder="Type Your Message">
                                  </div>
                                  <div class="col-2">
                                    <button id = "send_message" type = "submit" class = "btn btn-primary btn-block">Send</button>
                                  </div>

                              </div>
                          </form>
                      
                      </div>
                      {{--online users --}}
                      <div class="col-3">
                        <p><strong>Online Now</strong></p>
                        <ul id = "users" class = "list-unstyled overflow-auto text-info" style = "height: 45vh;">
                            {{-- <li>ISraa M. <img src="{{asset('img/waving.png')}}" style = "width: 40px;" alt=""></li> --}}
                        </ul>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const usersElement       = document.getElementById("users");
    const messagesElement    = document.getElementById("messages");
    const typingContainer    = document.getElementById("typing_container");
    const typingText         = document.getElementById("typing_text");
    let chat_channel         = Echo.join('chat');
    let timer;

    //listen for chat changes using pusher in presense channgel
    chat_channel
        .here( (users) => {

            users.forEach( (user , index) => {

                addUserItem(user.id , user.name);

            } );

        } )
        .joining( (user) => {

            addUserItem(user.id , user.name);

        } )
        .leaving( (user) => {

            const element = document.getElementById(user.id);
            element.parentNode.removeChild(element);

        } )
        .listen('NewMessage' , (e) => {
            let sender =  e.user.id == "{{auth()->user()->id}}" ? true : false;
            
            addMessage(e.user.name + ': ' + e.message , sender);
        })
        .listenForWhisper('typing', (e) => {
            typingContainer.classList.remove("d-none");
            typingText.innerText = e.name + " is typing";

            clearTimeout(timer);
            timer = setTimeout( () => {
                console.log("Hello");
                typingContainer.classList.add("d-none");
                typingText.innerText = "";
            }, 1000)
        });
       


    function addUserItem(id , name , active = false)
    {       
        let element = document.createElement('li');
        element.setAttribute('id' , id);
        element.setAttribute('onclick' , 'greetUser("' + id + '")' );

        element.innerHTML = name + '<img src="{{asset('img/waving.png')}}" style = "width: 30px;">';
        element.classList.add('list-group-item' , 'list-group-item-info');
        if(active)
        {
            element.classList.add('active');
        }
        usersElement.appendChild(element);
    }

    function addMessage(message , sender = true)
    {
        let element = document.createElement('li');

        element.innerText = message;

        //element.classList.add('list-group-item' , 'list-group-item-success');
        if(sender)
        {
            element.classList.add('sender_message');
        }
        else 
        {
            element.classList.add('receiver_message');
        }
        messagesElement.appendChild(element);

        messagesElement.scrollTop = messagesElement.scrollHeight;
    }


</script>

<script>
    const sendMessageElement = document.getElementById("send_message");
    const messageElement     = document.getElementById("message");

    sendMessageElement.addEventListener('click' , (e) => {
        e.preventDefault();

        window.axios.post('chat/send_message' , {
            message : messageElement.value
        });

        messageElement.value = "";
    } );

    messageElement.addEventListener('change' , (e) => {
        e.preventDefault();

        window.axios.post('chat/send_message' , {
            message : messageElement.value
        });

        messageElement.value = "";
    } );
    //when typing
    messageElement.addEventListener('keydown' , (e) => {

        chat_channel.whisper('typing' , {
            name : "{{auth()->user()->name}}"
        });
    } );

</script>

<script>
    function greetUser(id) 
    {
        console.log("ID : " + id);
        window.axios.post('chat/greet/' + id);
    }
</script>

<script>
    Echo.private('chat.greet.{{auth()->user()->id}}')
        .listen('GreetingSent' , (e) => {
            addMessage(e.message , e.sender);
        } );
</script>
@endpush