/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9f7',
          100: '#dcf2ee',
          200: '#bce5de',
          300: '#8dd1c7',
          400: '#5bb5ab',
          500: '#138c79',
          600: '#0f7a69',
          700: '#0d6356',
          800: '#0c5046',
          900: '#0a423b',
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
