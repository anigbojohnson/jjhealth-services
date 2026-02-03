import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['public/css/app.css', 'resources/js/app.js','resources/js/medical-certificate-studies.js','resources/js/medical-ceticate-work.js','resources/js/weight-loss.js','resources/js/specialist-refferals.js','resources/js/telehealth-consultation.js'],
            refresh: true,
        }),
    ],
});
