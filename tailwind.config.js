/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/**/*.php',
        './config/tools*.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50:  '#eef4ff',
                    100: '#dbe6fe',
                    200: '#bfd3fe',
                    300: '#93b4fd',
                    400: '#6090fa',
                    500: '#3b6cf5',
                    600: '#254bea',
                    700: '#1d3ad7',
                    800: '#1e31ae',
                    900: '#1e2f89',
                    950: '#171e54',
                },
                surface: {
                    50:  '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                },
            },
            fontFamily: {
                display: ['"Cabinet Grotesk"', 'system-ui', 'sans-serif'],
                body:    ['"DM Sans"', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
