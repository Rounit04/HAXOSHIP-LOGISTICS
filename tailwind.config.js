/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    container: { center: true, padding: '1rem' },
    extend: {
      colors: {
        brand: {
          primary: '#FF750F',
          dark: '#1b1b18',
          light: '#FDFDFC',
        },
      },
      fontFamily: {
        sans: ["Inter", "ui-sans-serif", "system-ui", "sans-serif"],
      },
      boxShadow: {
        card: '0 1px 2px rgba(0,0,0,0.06), 0 1px 1px rgba(0,0,0,0.04)'
      }
    },
  },
  plugins: [],
}


