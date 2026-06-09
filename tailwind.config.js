import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:    ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', ...defaultTheme.fontFamily.sans],
                ui:      ['"Nunito Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                special: ['Raleway', 'Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'ich-green':         '#4A9E5C',
                'ich-green-dark':    '#3B8249',
                'ich-green-soft':    '#6DB578',
                'ich-green-surface': '#EEF6F0',
                'ich-teal':          '#3A8FA8',
                'ich-teal-dark':     '#2E7A90',
                'ich-yellow':        '#F5A623',
                'ich-yellow-dark':   '#D9961A',
                'ich-blue':          '#155DFC',
                'ich-blue-soft':     '#DBEAFE',
                'ich-error':         '#E7000B',
                'ich-error-soft':    '#FEE2E2',
                'ich-success':       '#009966',
                'ich-success-soft':  '#D1FAE5',
                'ich-purple':        '#8B5CF6',
                'ich-purple-soft':   '#EDE9FE',
                'ich-warning':       '#D9961A',
                'ich-warning-soft':  '#FEF5DC',
                'ich-info':          '#155DFC',
                'ich-info-soft':     '#F4F7FC',
                'ich-pink-soft':     '#FCE7F3',
                'ich-gradient-end':  '#2D7A5E',
                'ich-ink-900':       '#101828',
                'ich-ink-800':       '#1E2939',
                'ich-ink-600':       '#344153',
                'ich-ink-500':       '#465260',
                'ich-ink-400':       '#687182',
                'ich-ink-300':       '#99A1AF',
                'ich-line':          '#E5E7EB',
                'ich-surface':       '#F5F6FA',
                'ich-sidebar':       '#1E3A2F',
            },
            borderRadius: {
                'ich-sm': '5px',
                'ich-md': '10px',
                'ich-lg': '15px',
            },
            boxShadow: {
                'ich-card': '0 1px 3px rgba(89,96,120,0.1), 0 1px 2px rgba(70,79,96,0.06)',
                'ich-lift': '0 4px 4px rgba(0,0,0,0.08)',
                'ich-logo': '0 10px 24px rgba(0,0,0,0.2)',
                'ich-btn':  '0 4px 4px rgba(0,0,0,0.06)',
            },
        },
    },

    plugins: [forms({ strategy: 'class' })],
};
