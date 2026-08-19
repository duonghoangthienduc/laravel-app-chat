import {execSync} from 'child_process';
import fs from 'fs/promises';
import path from 'path';

const statuses = JSON.parse(
	await fs.readFile('modules_statuses.json', 'utf-8')
);

for (const [mod, enabled] of Object.entries(statuses)) {
	if (!enabled) {
		continue;
	}

	const cfg = path.join('app', 'Modules', mod, 'vite.config.js');

	try {
		await fs.access(cfg);
		console.log(`\n=== Building module: ${mod} ===`);
		execSync(`npx vite build --config "${cfg}"`, {stdio: 'inherit'});
	}
	catch {
		// module không có vite.config.js -> bỏ qua, không lỗi
	}
}

console.log('\nAll module builds done.');