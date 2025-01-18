// Importar las librerías de Firebase
import { initializeApp } from 'https://www.gstatic.com/firebasejs/11.1.0/firebase-app.js';
import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/11.1.0/firebase-messaging.js';
import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-auth.js";

// Configuración de Firebase
const firebaseConfig = {
  apiKey: "AIzaSyA9xSYI6EtxMUfDMeAtVdVa1CJhCeekMNM",
  authDomain: "controlgastos-d9ba3.firebaseapp.com",
  projectId: "controlgastos-d9ba3",
  storageBucket: "controlgastos-d9ba3",
  messagingSenderId: "853409890181",
  appId: "1:853409890181:web:0d86b62d6694a1ebd78eec",
  measurementId: "G-83TWQV9B3R"
};

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);
const auth = getAuth();

// Solicitar permisos para recibir notificaciones
Notification.requestPermission().then((permission) => {
  if (permission === 'granted') {
    console.log('Notification permission granted.');

    // Obtener el token de FCM
    getToken(messaging, { vapidKey: 'BA7lgoSVTAOhN98lKcUc76V84CBMb_7l6wRfQQq_hshvPl0yG330fmQMsi5uknbZh8QU8G4m4glrxa6aI_WSR5M' }).then((currentToken) => {
      if (currentToken) {
        console.log('FCM Token:', currentToken);

        // Obtener el ID de usuario autenticado
        onAuthStateChanged(auth, (user) => {
          if (user) {
            const userId = user.uid;
            console.log('User ID:', userId);

            // Enviar el token al servidor
            fetch('save_token.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ token: currentToken, user_id: userId })
            }).then(response => response.json()).then(data => {
              console.log('Token saved:', data);
            }).catch(error => {
              console.error('Error saving token:', error);
            });
          } else {
            console.log('No user is signed in.');
          }
        });
      } else {
        console.log('No registration token available. Request permission to generate one.');
      }
    }).catch((err) => {
      console.log('An error occurred while retrieving token. ', err);
    });
  } else {
    console.log('Unable to get permission to notify.');
  }
});

// Registra el Service Worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/firebase-messaging-sw.js')
    .then((registration) => {
      console.log('Service Worker registered with scope:', registration.scope);
    }).catch((err) => {
      console.log('Service Worker registration failed:', err);
    });
}

// Manejo de mensajes en primer plano (opcional)
onMessage(messaging, (payload) => {
  console.log('Message received. ', payload);
  // Personaliza el manejo de notificaciones en primer plano aquí
});