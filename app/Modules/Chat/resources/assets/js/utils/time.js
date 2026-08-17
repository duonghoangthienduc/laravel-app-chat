export function formatRelativeTime(isoString, now = Date.now()) {
	if (!isoString) {
		return '';
	}

	const date = new Date(isoString);
	const diffMs = now - date.getTime();
	const diffSec = Math.floor(diffMs / 1000);
	const diffMin = Math.floor(diffSec / 60);
	const diffHour = Math.floor(diffMin / 60);
	const diffDay = Math.floor(diffHour / 24);

	if (diffSec < 60) {
		return 'just now';
	}

	if (diffMin < 60) {
		return diffMin === 1 ? '1 minute ago' : `${diffMin} minutes ago`;
	}

	if (diffHour < 24) {
		return diffHour === 1 ? '1 hour ago' : `${diffHour} hours ago`;
	}

	const currentYear = new Date(now).getFullYear();
	const options = {day: 'numeric', month: 'long'};
	if (date.getFullYear() !== currentYear) {
		options.year = 'numeric';
	}

	return new Intl.DateTimeFormat('en-GB', options).format(date);
}

export function formatDayLabel(isoString, now = Date.now()) {
	if (!isoString) {
		return '';
	}

	const date = new Date(isoString);
	const nowDate = new Date(now);

	const startOfDay = (d) => new Date(d.getFullYear(), d.getMonth(), d.getDate());
	const diffDays = Math.round((startOfDay(nowDate) - startOfDay(date)) / 86_400_000);

	const dateOptions = {day: 'numeric', month: 'long'};
	if (date.getFullYear() !== nowDate.getFullYear()) {
		dateOptions.year = 'numeric';
	}
	const fullDate = new Intl.DateTimeFormat('en-GB', dateOptions).format(date);

	if (diffDays === 0) {
		return `Today`;
	}
	if (diffDays === 1) {
		return `Yesterday, ${fullDate}`;
	}

	if (diffDays > 1 && diffDays < 7) {
		const weekday = new Intl.DateTimeFormat('en-GB', {weekday: 'long'}).format(date);
		return `${weekday}, ${fullDate}`;
	}

	return fullDate;
}