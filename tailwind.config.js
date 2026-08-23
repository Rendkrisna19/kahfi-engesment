import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class', '[data-theme="dark"]'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['var(--font-family-base)', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    blue: 'var(--brand-blue)',
                    'blue-hover': 'var(--brand-blue-hover)',
                    purple: 'var(--brand-purple)',
                    'purple-hover': 'var(--brand-purple-hover)',
                },
                body: 'var(--bg-body)',
                surface: {
                    body: 'var(--bg-body)',
                    card: 'var(--bg-card)',
                    DEFAULT: 'var(--bg-surface)',
                },
                border: 'var(--border-color)',
                status: {
                    success: 'var(--status-success)',
                    warning: 'var(--status-warning)',
                    danger: 'var(--status-danger)',
                    info: 'var(--status-info)',
                }
            },
            textColor: {
                primary: 'var(--text-primary)',
                secondary: 'var(--text-secondary)',
                muted: 'var(--text-muted)',
            },
            backgroundImage: {
                'brand-gradient': 'var(--brand-gradient)',
            }
        },
    },

    plugins: [forms],
};
