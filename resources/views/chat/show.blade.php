@extends('layouts.app')
@push('styles')
<style>
   #users li 
   {
       cursor: pointer;
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
                            <div class="col-12 border rounded-lg p-3">
                                <ul id = "messages" class = "list-unstyled overflow-auto" style = "height: 45vh;">
                                   
                                </ul>
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


    Echo.join('chat')
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
            addMessage(e.user.name + ': ' + e.message);
        });
       


    function addUserItem(id , name , active = false)
    {
        
        let element = document.createElement('li');
        element.setAttribute('id' , id);
        element.setAttribute('onclick' , 'greetUser("' + id + '")' );
        element.innerText = name;
        element.classList.add('list-group-item' , 'list-group-item-info');
        if(active)
        {
            element.classList.add('active');
        }
        usersElement.appendChild(element);
    }

    function addMessage(message , active = false)
    {
        let element = document.createElement('li');

        element.innerText = message;

        element.classList.add('list-group-item' , 'list-group-item-success');
        if(active)
        {
            element.classList.add('active');
        }
        messagesElement.appendChild(element);
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
            addMessage(e.message);
        } );
</script>
@endpush