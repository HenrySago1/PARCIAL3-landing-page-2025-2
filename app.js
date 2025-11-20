// Check if the browser supports service workers
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('service-worker.js')
        .then(function(registration) {
            console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch(function(error) {
            console.error('Service Worker registration failed:', error);
        });
}


window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault(); 
    const installButton = document.getElementById('installButton');
    installButton.style.display = 'block';

    installButton.addEventListener('click', () => {
        event.prompt(); 
        event.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('El usuario aceptó la instalación de PWA');
                installButton.style.display = 'none';
            } else {
                console.log('El usuario descartó la instalación de PWA');
            }
        });
    });
});
