import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/app/theme.css',
                'resources/css/filament/candidato/theme.css',
                'resources/css/filament/gps/theme.css',
            ],
            refresh: [
                ...refreshPaths,
                'resources/views/**',
                'app/Filament/**',
                'app/Http/Controllers/**',
                'app/Models/**',
                'app/Livewire/**',
                'app/Providers/Filament/**',
            ],
        }),
        tailwindcss(),
    ],
});
