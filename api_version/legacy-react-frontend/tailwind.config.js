/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{js,ts,jsx,tsx}',
  ],
  theme: {
    colors: {
      background: '#0F0F0F',
      foreground: '#FFFFFF',
      surface: '#1F1F1F',
      accent: '#F59E0B',
      'low-stock': '#F59E0B',
      'out-of-stock': '#EF4444',
      'in-stock': '#22C55E',
      black: '#000000',
      white: '#FFFFFF',
      gray: {
        400: '#9CA3AF',
        500: '#6B7280',
      },
      red: {
        400: '#F87171',
        900: '#7C2D12',
      },
      amber: {
        400: '#FBBF24',
        300: '#FCD34D',
      },
      green: {
        400: '#4ADE80',
      },
      blue: {
        400: '#60A5FA',
      },
      transparent: 'transparent',
    },
    fontFamily: {
      'mono': ['"IBM Plex Mono"', 'monospace'],
      'sans': ['"IBM Plex Sans"', 'sans-serif'],
    },
    extend: {},
  },
  plugins: [require('tailwindcss-animate')],
}
