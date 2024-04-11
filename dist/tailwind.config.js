/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php'
  ],
  theme: {
    extend: {
      boxShadow: {
        'glow': '0 0 20px 0 rgba(1,1,1,0.3)'
      }
    },
  },
  plugins: [],
}