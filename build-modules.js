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

	const modulePath = path.join('app', 'Modules', mod);

	let entries;
	try {
		entries = await fs.readdir(modulePath);
	}
	catch {
		continue;
	}

	const viteConfigs = entries
		.filter((file) => /^vite.*\.config\.js$/.test(file))
		.sort();

	if (viteConfigs.length === 0) {
		continue;
	}

	for (const configFile of viteConfigs) {
		const cfgPath = path.join(modulePath, configFile);
		console.log(`\n=== Building module: ${mod} (${configFile}) ===`);
		execSync(`npx vite build --config "${cfgPath}"`, {stdio: 'inherit'});
	}
}

console.log('\nAll module builds done.');