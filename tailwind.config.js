/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.php',
    './public/**/*.php',
    './lib/Templates/Errors/template.html'
  ],
  theme: {
    container: {
      center: true
    },
    extend: {},
  },
  plugins: [],
}