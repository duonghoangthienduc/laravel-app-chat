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