<?php

namespace App\Support\Dashboard;

use Closure;
use Illuminate\Support\Collection;

class DashboardWidgetRegistry{

	/** @var array<int, array{view: string, data: \Closure|array, priority: int}> */
	protected array $widgets = [];

	/**
	 * @param string $view view Blade @include
	 * @param \Closure|array $data Data into view. Using Closure for render
	 */
	public function register(string $view, Closure|array $data = [], int $priority = 0)
	: void{
		$this->widgets[] = compact('view', 'data', 'priority');
	}

	/** @return Collection<int, array{view: string, data: array}> */
	public function widgets()
	: Collection{
		return collect($this->widgets)
			->sortBy('priority')
			->map(fn(array $w) => [
				'view' => $w['view'],
				'data' => $w['data'] instanceof Closure ? ($w['data'])() : $w['data'],
			])
			->values();
	}
}