/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./public/play-tailwind-1.0.0/**/*.html",
        "./public/play-tailwind-1.0.0/src/css/tailwind.css",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    darkMode: "class",
    theme: {
        extend: {},
    },
    plugins: [],
};
