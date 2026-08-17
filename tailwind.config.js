import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                background: '#0a0a0f',
                surface: {
                    DEFAULT: '#131318',
                    secondary: '#0f0f14',
                    elevated: '#151220',
                },
                border: {
                    DEFAULT: '#1f1f28',
                    strong: '#2a2a35',
                    accent: '#5e4cff',
                },
                text: {
                    primary: '#f5f5f7',
                    secondary: '#9ca3af',
                    muted: '#6b7280',
                    faint: '#4b5563',
                },
                accent: {
                    DEFAULT: '#7c5cfc',
                    dark: '#5e4cff',
                    light: '#a78bfa',
                    muted: 'rgba(124, 92, 252, 0.12)',
                    foreground: '#ffffff',
                },
                success: {
                    DEFAULT: '#34d399',
                    muted: 'rgba(52, 211, 153, 0.08)',
                },
                warning: '#fb923c',
                error: '#f87171',
            },
            borderRadius: {
                sm: '4px',
                md: '8px',
                lg: '12px',
            },
        },
    },
    plugins: [],
};
