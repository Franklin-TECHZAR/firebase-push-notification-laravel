importScripts('https://www.gstatic.com/firebasejs/5.5.9/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/5.5.9/firebase-messaging.js');

// Initialize Firebase
firebase.initializeApp({
    apiKey: "AIzaSyB-t7h38NAS9b5RQoKNf6xecv2Tdyq5oXE",
    authDomain: "laravel-push-notificatio-691b4.firebaseapp.com",
    projectId: "laravel-push-notificatio-691b4",
    storageBucket: "laravel-push-notificatio-691b4.appspot.com",
    messagingSenderId: "689647110437",
    appId: "1:689647110437:web:7a8f6af08911a0b5c4b2cc",
    measurementId: "G-D2LNZRSPZG"
});

// Retrieve an instance of Firebase Messaging so that it can handle background messages
const messaging = firebase.messaging();

// Handle background messages
messaging.setBackgroundMessageHandler(function (payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: 'https://dummyimage.com/300.png', // Icon URL
        sound: "default",
        data: {
            url: payload.data.click_action
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);

    self.addEventListener('notificationclick', function (event) {
        event.notification.close();
        const url = event.notification.data.click_action;

        event.waitUntil(
            clients.matchAll({
                type: 'window',
                includeUncontrolled: true
            }).then(windowClients => {
                console.log('Current clients:', windowClients);
                for (let client of windowClients) {
                    if (client.url === url && 'focus' in client) {
                        console.log('Found matching client, focusing:', client);
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    console.log('Opening new window for URL:', url);
                    return clients.openWindow(url);
                }
            }).catch(error => {
                console.error('Error handling notification click:', error);
            })
        );

    });
});