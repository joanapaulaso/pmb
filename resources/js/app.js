import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Importar o CSS do Quill diretamente no JavaScript
import 'quill/dist/quill.snow.css';

import Quill from 'quill';

const mediaFiles = import.meta.glob('../images/*.{png,jpg,jpeg,gif,svg,mp4}', { eager: true });

window.appMedia = mediaFiles;
window.Alpine = Alpine;

Livewire.start();

window.Quill = Quill;
