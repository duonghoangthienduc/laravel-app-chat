export default function activityHeatmap(rawData, serverToday = null) {
	return {
		rawData,
		weeks: [],
		monthLabels: [],
		activeDayCount: 0,

		gap: 3,
		minCell: 6,
		maxCell: 14,
		cellSize: 11,
		isScrollable: false,

		init() {
			this.activeDayCount = Object.values(this.rawData).filter(count => count > 0).length;
			this.buildGrid();

			this.$nextTick(() => {
				this.recalculateCellSize();
				this.setupResizeObserver();
			});
		},

		setupResizeObserver() {
			const el = this.$refs.container;
			if (!el) {
				return;
			}

			let timer;
			const observer = new ResizeObserver(() => {
				clearTimeout(timer);
				timer = setTimeout(() => this.recalculateCellSize(), 100);
			});
			observer.observe(el);
		},

		recalculateCellSize() {
			const el = this.$refs.container;
			if (!el || this.weeks.length === 0) {
				return;
			}

			const containerWidth = el.clientWidth;
			const weekCount = this.weeks.length;
			const raw = (containerWidth - (weekCount - 1) * this.gap) / weekCount;
			const fitted = Math.floor(raw);

			if (fitted < this.minCell) {
				this.cellSize = this.minCell;
				this.isScrollable = true;
			}
			else {
				this.cellSize = Math.min(fitted, this.maxCell);
				this.isScrollable = false;
			}
		},

		buildGrid() {
			const totalDays = 371;

			const today = serverToday
				? this.parseIsoDateLocal(serverToday)
				: (() => {
					const d = new Date();
					d.setHours(0, 0, 0, 0);
					return d;
				})();

			const start = new Date(today);
			start.setDate(start.getDate() - totalDays + 1);
			while (start.getDay() !== 0) {
				start.setDate(start.getDate() - 1);
			}

			const weeks = [];
			let currentWeek = [];
			let cursor = new Date(start);

			while (cursor <= today) {
				const iso = this.toIsoDateLocal(cursor);
				const count = this.rawData[iso] ?? 0;

				currentWeek.push(cursor < start || cursor > today ? null : {
					date: iso,
					count
				});

				if (cursor.getDay() === 6) {
					weeks.push(currentWeek);
					currentWeek = [];
				}

				cursor.setDate(cursor.getDate() + 1);
			}

			if (currentWeek.length) {
				weeks.push(currentWeek);
			}

			this.weeks = weeks;
			this.monthLabels = this.buildMonthLabels(weeks);
		},

		parseIsoDateLocal(iso) {
			const [y, m, d] = iso.split('-').map(Number);
			const date = new Date(y, m - 1, d);
			date.setHours(0, 0, 0, 0);
			return date;
		},

		toIsoDateLocal(date) {
			const y = date.getFullYear();
			const m = String(date.getMonth() + 1).padStart(2, '0');
			const d = String(date.getDate()).padStart(2, '0');
			return `${y}-${m}-${d}`;
		},

		buildMonthLabels(weeks) {
			const labels = [];

			weeks.forEach((week) => {
				const firstDay = week.find((d) => d !== null);
				if (!firstDay) {
					if (labels.length) {
						labels[labels.length - 1].weekCount += 1;
					}
					return;
				}

				const month = this.parseIsoDateLocal(firstDay.date).getMonth();
				const last = labels[labels.length - 1];

				if (!last || last.month !== month) {
					labels.push({
						month,
						label: this.parseIsoDateLocal(firstDay.date).toLocaleDateString('en-GB', {month: 'short'}),
						weekCount: 1,
					});
				}
				else {
					last.weekCount += 1;
				}
			});

			return labels;
		},

		monthLabelWidth(weekCount) {
			return weekCount * (this.cellSize + this.gap) - this.gap;
		},

		levelColor(count, isLegend = false) {
			const levels = [
				'rgba(255,255,255,0.04)',
				'rgba(99,102,241,0.3)',
				'rgba(99,102,241,0.5)',
				'rgba(99,102,241,0.75)',
				'rgba(99,102,241,1)',
			];

			if (isLegend) {
				return levels[count];
			}

			if (count === 0) {
				return levels[0];
			}
			if (count === 1) {
				return levels[1];
			}
			if (count <= 3) {
				return levels[2];
			}
			if (count <= 6) {
				return levels[3];
			}
			return levels[4];
		},
	};
}