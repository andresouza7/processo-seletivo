import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import preset from './vendor/filament/support/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
export default {
  presets: [preset],
  content: [
    './resources/views/**/*.blade.php',
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: 'var(--color-primary)',
        'primary-hover': 'var(--color-primary-hover)',
        secondary: 'var(--color-secondary)',
        'secondary-hover': 'var(--color-secondary-hover)',
        negative: 'var(--color-negative)',
        'negative-hover': 'var(--color-negative-hover)',
        background: 'var(--color-background)',
        border: 'var(--color-border)',
        'text-primary': 'var(--color-text-primary)',
        'text-link': 'var(--color-text-link)',
        'text-link-hover': 'var(--color-text-link-hover)',
      },
    },
  },
  plugins: [forms, typography],
}