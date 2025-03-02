import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Import all images (with Vite)
const images = import.meta.glob('../images/*.{png,jpg,jpeg,gif,svg}', { eager: true });

// Make images available globally if needed
window.appImages = images;
window.Alpine = Alpine;
Livewire.start();
