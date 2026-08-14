const COLOR_PAIRS = [
	['#6366f1', '#7c3aed'],
	['#f43f5e', '#f97316'],
	['#06b6d4', '#2563eb'],
	['#10b981', '#0d9488'],
	['#d946ef', '#db2777'],
	['#f59e0b', '#ea580c'],
];

export function getInitials(name) {
	if (!name) {
		return '';
	}
	const parts = name.trim().split(/\s+/);
	const initials = parts.map(p => p[0]).join('').toUpperCase();
	return initials.length > 1 ? initials[0] + initials[initials.length - 1] : initials;
}

function avatarColor(name) {
	if (!name) {
		return COLOR_PAIRS[0];
	}
	const sum = [...name].reduce((acc, ch) => acc + ch.codePointAt(0), 0);
	return COLOR_PAIRS[sum % COLOR_PAIRS.length];
}

export function avatarStyle(name) {
	const [from, to] = avatarColor(name);
	return `background: linear-gradient(135deg, ${from}, ${to});height: 1.5rem; width: 1.5rem;`;
}