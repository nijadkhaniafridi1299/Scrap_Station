<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
    https://firebase.google.com/docs/web/setup#available-libraries -->

<script>
    function initFirebaseMessagingRegistration() {
        messaging.requestPermission().then(function () {
            return messaging.getToken()
        }).then(function(token) {
            // console.log(token);
            $("#fcmtoken").val(token);

            // $('#fcmtoken').val(token);
            

        }).catch(function (err) {
            console.log(`Token Error :: ${err}`);
        });
    }

    // Your web app's Firebase configuration
    var firebaseConfig = {
        apiKey: "AIzaSyAD2sHK6ulWyRhaPCm4YO9bZYh18ytXhNc",
        authDomain: "scrapstation-2aa6d.firebaseapp.com",
        projectId: "scrapstation-2aa6d",
        storageBucket: "scrapstation-2aa6d.appspot.com",
        messagingSenderId: "367419160655",
        appId: "1:367419160655:web:5cfc9ee1dcc5f08d00923a",
        measurementId: "G-3L4X7RB0EC"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    initFirebaseMessagingRegistration();

    // messaging.onMessage(function({data:{title,body,arg3,arg4}}){
    //     console.log(title,"+",body,"+",arg3,"+",arg4);
    //     new Notification(title, {body});
    // });

    messaging.onMessage((payload) => {
        let title = payload.data.message; let body = payload.data.body;
        new Notification(title, {body});
    });

</script>