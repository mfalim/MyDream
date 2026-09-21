import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/admin/layout.css",
                "resources/css/admin/vendors/index.css",
                "resources/css/admin/vendors/show.css",
                "resources/css/admin/members/index.css",
                "resources/css/admin/members/create.css",
                "resources/css/admin/members/form.css",
                "resources/css/admin/members/page.css",
                "resources/css/admin/members/show.css",
                "resources/js/app.js",
                "resources/js/admin/members/create.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
