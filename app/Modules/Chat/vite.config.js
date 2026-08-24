import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'node:path';

export default defineConfig({
	root: import.meta.dirname,
	envDir: path.resolve(import.meta.dirname, '../../../'),
	build: {
		emptyOutDir: true,
	},
	plugins: [laravel({
		input: ['resources/assets/js/app.js', 'resources/assets/js/sidebar.js'],
		publicDirectory: '../../../public',   // relative to `root`, not absolute
		buildDirectory: 'build-chat',
	}),],
	resolve: {
		alias: {
			'@': path.resolve(import.meta.dirname, '../../../resources/js'),
			'@status': path.resolve(import.meta.dirname, '../../../app/Modules/Log/resources/assets/js'),
		},
	},
});