import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Fira Sans', ...defaultTheme.fontFamily.sans],
                mono: ['Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                industrial: {
                    bg: 'var(--color-background)',
                    card: 'var(--color-card)',
                    field: 'var(--color-field)',
                    accent: 'var(--color-accent)',
                    blue: 'var(--color-secondary-accent)',
                    foreground: 'var(--color-foreground)',
                    muted: 'var(--color-muted)',
                    border: 'var(--color-border)',
                },
                success: {
                    bg: 'var(--color-success-bg)',
                    border: 'var(--color-success-border)',
                    text: 'var(--color-success-text)',
                },
                warning: {
                    bg: 'var(--color-warning-bg)',
                    border: 'var(--color-warning-border)',
                    text: 'var(--color-warning-text)',
                },
                danger: {
                    bg: 'var(--color-destructive-bg)',
                    border: 'var(--color-destructive-border)',
                    text: 'var(--color-destructive-text)',
                }
            }
        },
    },

    plugins: [forms],
};
