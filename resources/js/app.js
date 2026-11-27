import './bootstrap';

import Alpine from 'alpinejs';
import { createQrCameraScannerMixin } from './qr-camera-scanner';

window.Alpine = Alpine;
window.createQrCameraScannerMixin = createQrCameraScannerMixin;

Alpine.start();
