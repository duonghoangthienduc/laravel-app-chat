import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import {bunny} from 'laravel-vite-plugin/fonts';
import tailwindcss from "@tailwindcss/vite";
import path from "node:path";

export default defineConfig({
	plugins: [
		laravel({
			input: [
				'resources/css/app.css',
				'resources/js/app.js',
				'resources/js/passkeys.js',
			],
			refresh: true,
			fonts: [
				bunny('Instrument Sans', {
					weights: [400, 500, 600],
				}),
			],
		}),
		tailwindcss(),
	],
	server: {
		cors: true,
		watch: {
			ignored: ['**/storage/framework/views/**'],
		},
	},
	resolve: {
		alias: {
			'@log': path.resolve('./app/Modules/Log/resources/assets/js'),
		},
	},
});
