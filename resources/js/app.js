require('./bootstrap');

Echo.private('notifications')
    .listen( 'UserSessionChanged' , (e) => {
        console.log(e);
        const notificationElement = document.getElementById('notification');

        notificationElement.innerText = e.message;

        notificationElement.classList.remove('invisible');

        notificationElement.classList.remove('alert-danger');

        notificationElement.classList.remove('alert-success');

        notificationElement.classList.add('alert-' + e.type);
    });

