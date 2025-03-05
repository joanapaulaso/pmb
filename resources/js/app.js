import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import './register-form.js';

// Import all images and videos
const mediaFiles = import.meta.glob('../images/*.{png,jpg,jpeg,gif,svg,mp4}', { eager: true });

// Make media files available globally if needed
window.appMedia = mediaFiles;
window.Alpine = Alpine;
Livewire.start();
