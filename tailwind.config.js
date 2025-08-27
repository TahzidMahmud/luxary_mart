/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './app/**/*.php',
        './resources/**/*.(js|jsx|ts|tsx)',
    ],
    safelist: [{ pattern: /button--.*/ }],
    theme: {
        extend: {
            colors: {
                // both frontend and backend
                'theme-primary': 'rgb(var(--theme-primary) / <alpha-value>)',
                'theme-secondary':
                    'rgb(var(--theme-secondary) / <alpha-value>)',
                'theme-secondary-light':
                    'rgb(var(--theme-secondary-light) / <alpha-value>)',

                'theme-alert': 'rgb(var(--theme-alert) / <alpha-value>)', // #ee5858
                'theme-orange': 'rgb(var(--theme-orange) / <alpha-value>)', // #FF7E06
                'theme-green': 'rgb(var(--theme-green) / <alpha-value>)', // #31AF2E
                'theme-yellow': 'rgb(var(--theme-yellow) / <alpha-value>)', // #FFD34D

                // dark / light mood
                muted: 'rgb(var(--muted) / <alpha-value>)',

                foreground: 'rgb(var(--foreground) / <alpha-value>)',
                'foreground-invert':
                    'rgb(var(--foreground-invert) / <alpha-value>)',

                background: 'rgb(var(--background) / <alpha-value>)',
                'background-invert':
                    'rgb(var(--background-invert) / <alpha-value>)',
                'background-hover':
                    'rgb(var(--background-hover) / <alpha-value>)',
                'background-active':
                    'rgb(var(--background-active) / <alpha-value>)',
                'background-primary-light':
                    'rgb(var(--background-primary-light) / <alpha-value>)',

                popover: 'rgb(var(--popover) / <alpha-value>)',
                'popover-foreground':
                    'rgb(var(--popover-foreground) / <alpha-value>)',

                'background-primary-light-invert':
                    'rgb(var(--background-primary-light-invert) / <alpha-value>)',

                border: 'rgb(var(--border) / <alpha-value>)',
                'border-hover': 'rgb(var(--border-hover) / <alpha-value>)',

                card: 'rgb(var(--card) / <alpha-value>)',
                'card-foreground':
                    'rgb(var(--card-foreground) / <alpha-value>)',
            },
            fontFamily: {
                inter: ['Inter', 'sans-serif'],
                'public-sans': ['Public Sans', 'sans-serif'],
                sacramento: ['Sacramento', 'cursive'],
            },
            screens: {
                xxs: '400px',
                xs: '490px',
                '3xl': '1850px',
            },
            boxShadow: {
                theme: '0 3px 10px 0 var(--shadow-color)',
            },
            gridTemplateColumns: {
                14: 'repeat(14, minmax(0, 1fr))',
            },
            borderColor: {
                'theme-primary-14': 'rgb(var(--theme-primary) / 0.14)',
            },
            backgroundColor: {
                'badge-success': '#1D9679',
                'badge-danger': '#FF0404',
                'badge-processing': '#FF7E06',
                'badge-confirmed': '#74C1FD',
                'badge-shipped': '#AEB109',
                'badge-default': '#1F84D2',
            },
        },
    },
    plugins: [],
};
