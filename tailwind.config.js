/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./views/**/*.{html,php}", "./src/**/*.ts", "./app/Modules/Restaurant/Resources/ts/**/*.ts"],
  theme: {
    extend: {

      screens: {
        'xsp': '530px',
        'xxs': '480px',
        'xs': '540px',
        'tlg': '992px',
        'xlg': '1024px',
        '2xlg': '1280px',
        'between1200and1600': { 'raw': '(min-width: 1200px) and (max-width: 1600px)' },
      },

      keyframes: {
        menuSlideUp: {
          '0%': { opacity: '0', transform: 'translateY(24px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        ticker: {
          '0%': { transform: 'translateX(100%)' },
          '100%': { transform: 'translateX(-100%)' },
        },
        tickerMobile: {
          '0%': { transform: 'translateX(100%)' },
          '100%': { transform: 'translateX(-200%)' },
        },

        // 🔥 NUEVA ANIMACIÓN ALERTA
        fadeSlide: {
          '0%': {
            opacity: '0',
            transform: 'translateY(-12px) scale(0.96)',
          },
          '100%': {
            opacity: '1',
            transform: 'translateY(0) scale(1)',
          },
        }
      },

      animation: {
        menuSlideUp: 'menuSlideUp 250ms ease-out both',
        ticker: 'ticker 18s linear infinite',
        tickerMobile: 'tickerMobile 41s linear infinite',

        // 🔥 NUEVA ANIMACIÓN
        fadeSlide: 'fadeSlide 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards',
      },
    },
  },
  plugins: [],
}
