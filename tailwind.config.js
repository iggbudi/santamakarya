export default {
    content: ['./resources/views/landing.blade.php', './resources/views/landing/**/*.blade.php', './resources/js/*.js'],
    theme: {
        extend: {
            colors: {
                brand: {
                    orange: '#D96B27', orangeHover: '#B85519', orangeLight: '#FFF4ED',
                    charcoal: '#121619', slate: '#1E242B', muted: '#6B7280',
                    offwhite: '#FAFAFA', surface: '#F3F4F6',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                display: ['"Space Grotesk"', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
