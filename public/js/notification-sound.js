document.addEventListener('livewire:load', () => {
    window.addEventListener('filament-notification.shown', () => {
        const sound = new Audio('/sonidos/notification.mp3');
        sound.play();
    });
});
