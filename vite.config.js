import {defineConfig, loadEnv} from 'vite';
import liveReload from "vite-plugin-live-reload";

export default ({ command, mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return defineConfig({
        base:'/assets',
        plugins: [
            liveReload([
                'public/index.php',
                'resources/**/**',
                'app/**/**',
                'site-*/**/**'
            ])
        ],
        server: {
            hmr: 'localhost',
            origin: 'http://' + env.APP_DOMAIN,
        },
        build: {
            copyPublicDir: false,
            outDir: 'public/assets',
            assetsDir: '',
            manifest: true,
            rollupOptions: {
                input: [
                    'resources/js/main.js',
                    'resources/js/test.js',
                ]
            }
        }
    });
}
