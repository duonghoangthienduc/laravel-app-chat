import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'node:path';

export default defineConfig({
	root: import.meta.dirname,
	envDir: path.resolve(import.meta.dirname, '../../../'),
	build: {
		emptyOutDir: true,
	},
	plugins: [
		laravel({
			input: [
				'resources/assets/js/app.js',
			],
			publicDirectory: path.resolve(import.meta.dirname, '../../../public'),
			buildDirectory: 'build-log',
		}),
	],
	resolve: {
		alias: {
			'@': path.resolve(import.meta.dirname, '../../../resources/js'),
		},
	},
});