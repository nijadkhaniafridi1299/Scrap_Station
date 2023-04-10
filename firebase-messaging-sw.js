importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
   
firebase.initializeApp({
        apiKey: "AIzaSyAD2sHK6ulWyRhaPCm4YO9bZYh18ytXhNc",
        authDomain: "scrapstation-2aa6d.firebaseapp.com",
        projectId: "scrapstation-2aa6d",
        storageBucket: "scrapstation-2aa6d.appspot.com",
        messagingSenderId: "367419160655",
        appId: "1:367419160655:web:5cfc9ee1dcc5f08d00923a",
        measurementId: "G-3L4X7RB0EC"
});
  
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler((payload) => {
    let title = payload.data.message; let body = payload.data.body;
    return self.registration.showNotification(title,{body});
});