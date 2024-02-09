/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.twig',
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