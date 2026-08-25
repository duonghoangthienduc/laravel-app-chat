import activityHeatmap from './components/activity-heatmap.js';

function registerComponents() {
	window.Alpine.data('activityHeatmap', activityHeatmap);
}

if (window.Alpine) {
	registerComponents();
}
else {
	document.addEventListener('alpine:init', registerComponents, {
		once: true,
	});
}