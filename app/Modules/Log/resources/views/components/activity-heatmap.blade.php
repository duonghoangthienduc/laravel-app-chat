<div x-data="activityHeatmap(@js($data), @js($today))" class="rounded-xl border border-zinc-800 bg-zinc-900/40 p-5">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm font-semibold text-white">{{ __('Your activity') }}</p>
        <p class="text-xs text-zinc-500" x-text="activeDayCount + ' {{ __('active days') }}'"></p>
    </div>

    <div x-ref="container" style="width:100%;">
        <div x-ref="scrollWrap" :style="`overflow-x:${isScrollable ? 'auto' : 'hidden'};`">
            <div :style="`display:${isScrollable ? 'inline-flex' : 'flex'}; flex-direction:column; gap:4px; width:${isScrollable ? 'auto' : '100%'};`">
                <div :style="`display:flex; gap:${gap}px;`">
                    <template x-for="(month, i) in monthLabels" :key="i">
                        <div :style="`width:${monthLabelWidth(month.weekCount)}px; font-size:11px; color:#71717a;`" x-text="month.label"></div>
                    </template>
                </div>

                <div :style="`display:flex; gap:${gap}px; ${isScrollable ? '' : 'width:100%; justify-content:space-between;'}`">
                    <template x-for="(week, wi) in weeks" :key="wi">
                        <div :style="`display:flex; flex-direction:column; gap:${gap}px;`">
                            <template x-for="(day, di) in week" :key="di">
                                <div :title="day ? `${day.count} ${day.count === 1 ? 'activity' : 'activities'} on ${day.date}` : ''" :style="`width:${cellSize}px; height:${cellSize}px; border-radius:2px; background:${day ? levelColor(day.count) : 'transparent'};`"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px; margin-top:12px;">
        <span style="font-size:11px; color:#71717a;">{{ __('Less') }}</span>
        <template x-for="level in [0, 1, 2, 3, 4]" :key="level">
            <div :style="`width:11px; height:11px; border-radius:2px; background:${levelColor(level, true)};`"></div>
        </template>
        <span style="font-size:11px; color:#71717a;">{{ __('More') }}</span>
    </div>
</div>

@once('log-script')
    @push('scripts')
        @vite('resources/assets/js/app.js', 'build-log')
    @endpush
@endonce