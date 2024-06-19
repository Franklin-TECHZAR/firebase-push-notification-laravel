@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('You are logged in!') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://www.gstatic.com/firebasejs/5.5.9/firebase.js"></script>
    <script>
        // Your web app's Firebase configuration

        const firebaseConfig = {
            apiKey: "AIzaSyB-t7h38NAS9b5RQoKNf6xecv2Tdyq5oXE",
            authDomain: "laravel-push-notificatio-691b4.firebaseapp.com",
            projectId: "laravel-push-notificatio-691b4",
            storageBucket: "laravel-push-notificatio-691b4.appspot.com",
            messagingSenderId: "689647110437",
            appId: "1:689647110437:web:7a8f6af08911a0b5c4b2cc",
            measurementId: "G-D2LNZRSPZG"
        };

        firebase.initializeApp(firebaseConfig);

        const messaging = firebase.messaging();

        function initializeFCM() {

            messaging.requestPermission()
                .then(() => messaging.getToken())
                .then(token => {
                    console.log("FCM Token:", token);
                    sendTokenToServer(token);
                })
                .catch(error => {
                    console.error("Error getting FCM token:", error);
                });

            messaging.onMessage((payload) => {
                console.log('Message received. ', payload);

                const notificationTitle = payload.notification.title;
                const notificationOptions = {
                    body: payload.notification.body,
                    icon: "https://dummyimage.com/300.png",
                    sound: payload.notification.sound,
                    data: {
                        url: payload.notification.click_action
                    } // Include the URL in the notification data
                };

                if (Notification.permission === 'granted') {
                    const notification = new Notification(notificationTitle, notificationOptions);
                    notification.onclick = () => {
                        const url = notificationOptions.data.url;

                        // Check for existing open tabs with the URL
                        clients.matchAll({
                            type: 'window',
                            includeUncontrolled: true
                        }).then(windowClients => {
                            for (let client of windowClients) {
                                if (client.url === url && 'focus' in client) {
                                    return client.focus();
                                }
                            }
                            // If no matching tab is found, open a new one
                            if (clients.openWindow) {
                                clients.openWindow(url);
                            }
                        });
                    };
                }
            });
        }

        function sendTokenToServer(token) {
            fetch('{{ route('save-user-fcm-token') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _token: "{{ csrf_token() }}",
                        fcm_token: token
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('FCM token saved:', data);
                })
                .catch(error => {
                    console.error('Error saving FCM token:', error);
                });
        }
        initializeFCM();
    </script>
@endsection
