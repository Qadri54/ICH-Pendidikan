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
                'ich-green':      '#3DA746',
                'ich-green-dark': '#2F8537',
                'ich-green-soft': '#51B059',
                'ich-teal':       '#3087A2',
                'ich-teal-dark':  '#246A80',
                'ich-yellow':     '#FDB625',
                'ich-yellow-dark':'#E09F17',
                'ich-blue':       '#155DFC',
                'ich-error':      '#E7000B',
                'ich-success':    '#009966',
                'ich-ink-900':    '#101828',
                'ich-ink-800':    '#1E2939',
                'ich-ink-600':    '#344153',
                'ich-ink-500':    '#465260',
                'ich-ink-400':    '#687182',
                'ich-ink-300':    '#99A1AF',
                'ich-line':       '#E5E7EB',
                'ich-surface':    '#F5F6FA',
                'ich-sidebar':    '#1A6B1A',
            },
            borderRadius: {
                'ich-sm': '5px',
                'ich-md': '10px',
                'ich-lg': '15px',
            },
            boxShadow: {
                'ich-card': '0 1px 3px rgba(89,96,120,0.1), 0 1px 2px rgba(70,79,96,0.06)',
                'ich-lift': '0 4px 4px rgba(0,0,0,0.25)',
                'ich-logo': '0 10px 24px rgba(0,0,0,0.2)',
                'ich-btn':  '0 4px 4px rgba(0,0,0,0.15)',
            },
        },
    },

    plugins: [forms({ strategy: 'class' })],
};
