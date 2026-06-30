/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/views/**/*.blade.php", // Todos os arquivos Blade
    "./resources/**/*.vue", // Todos os arquivos Vue
    "./resources/**/*.js", // Todos os arquivos JavaScript
    "./resources/**/*.jsx", // Todos os arquivos JSX
    "./resources/**/*.ts", // Todos os arquivos TypeScript
    "./resources/**/*.tsx", // Todos os arquivos TSX
    "./resources/**/*.html", // Todos os arquivos HTML
    "./resources/**/*.php", // Todos os arquivos PHP
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

