import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Candidato/**/*.php',
        './resources/views/filament/candidato/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
