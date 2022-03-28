@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Users</div>

                <div class="card-body">
                    <div class="container">
                        <ul id = "users" class="list-group">

                        </ul>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const usersElement = document.getElementById('users');

    function addUserItem(id , name , active = false)
    {
        
        let element = document.createElement('li');
        element.setAttribute('id' , id);
        element.innerText = name;
        element.classList.add('list-group-item' , 'list-group-item-success');
        if(active)
        {
            element.classList.add('active');
        }
        usersElement.appendChild(element);
    }

    function removeActiveClass()
    {
        let li = document.getElementsByClassName('list-group-item');

        for (i = 0; i < li .length; i++) { 
            li[i].classList.remove('active');
        }
    }

    window.axios.get('/api/users')
        .then( (response) => {

            let users = response.data;

            users.forEach( (user , index) => {
              
                addUserItem(user.id , user.name);
            });
        } )
</script>

<script>
    Echo.channel('users')
        .listen('UserCreated' ,  (e) => {

            removeActiveClass();

            addUserItem(e.user.id , e.user.name , true);

        })

        .listen('UserUpdated' ,  (e) => {

            removeActiveClass();

            let element = document.getElementById(e.user.id);

            element.innerText = e.user.name;

            element.classList.add("active");

        })

        .listen('UserDeleted' ,  (e) => {

            removeActiveClass();

            let element = document.getElementById(e.user.id);

            element.parentNode.removeChild(element);

        });

</script>
@endpush