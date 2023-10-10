import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import commonjs from "vite-plugin-commonjs";

export default defineConfig({
    plugins: [
        commonjs({
            filter(id) {
                if (["ckeditor5/build/ckeditor.js"].includes(id)) {
                    return true;
                }
            },
        }),
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            // reactivityTransform: true,
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    optimizeDeps: {
        include: ['ckeditor5-custom-build']
    },
    build: {
        commonjsOptions: {
            exclude: ['ckeditor5-custom-build']
        },
    },
    // build: {
    //     chunkSizeWarningLimit: 1600,
    // },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, './resources/js'),
        },
    }
});
