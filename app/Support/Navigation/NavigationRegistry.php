<?php
// app/Support/Navigation/NavigationRegistry.php

namespace App\Support\Navigation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Nwidart\Modules\Facades\Module;

class NavigationRegistry{

	protected const string DEFAULT_CHILDREN_VIEW = 'layouts.partials.nav-children';

	public function groups()
	: Collection{
		return $this->collectRawGroups()
		            ->sortBy(fn(array $group) => $group['priority'] ?? 0)
		            ->map(fn(array $group) => [
			            'heading' => $group['heading'],
			            'items'   => collect($group['items'])->map($this->resolveItem(...))->all(),
		            ])
		            ->values();
	}

	protected function resolveItem(array $item)
	: array{
		$patterns            = $item['active_on'] ?? [$item['route']];
		$children            = collect();
		$hasChildrenResolver = isset($item['children_resolver']);

		if ($hasChildrenResolver){
			$resolver = app($item['children_resolver']);

			if ($resolver instanceof ResolvesNavChildren){
				$children = $resolver->resolve();
			}
		}

		return [
			'label'                 => __($item['label']),
			'icon'                  => $item['icon'],
			'href'                  => route($item['route']),
			'active'                => request()->routeIs($patterns),
			'children'              => $children,
			'children_view'         => $this->resolveChildrenView($item['children_view'] ?? NULL),
			'has_children_resolver' => $hasChildrenResolver,
		];
	}

	protected function resolveChildrenView(?string $view)
	: string{
		if ($view === NULL){
			return self::DEFAULT_CHILDREN_VIEW;
		}

		if (View::exists($view)){
			return $view;
		}

		Log::warning("Navigation children_view [{$view}] not found — falling back to default.");

		return self::DEFAULT_CHILDREN_VIEW;
	}

	protected function collectRawGroups()
	: Collection{
		$groups = collect();

		$this->pushGroupsFrom($groups, config('navigation', []), 'config/navigation.php');

		foreach (Module::allEnabled() as $module){
			$key = $module->getLowerName();
			$this->pushGroupsFrom(
				$groups,
				config("{$key}.navigation", []),
				"Modules/{$module->getName()}/config/config.php"
			);
		}

		return $groups;
	}

	protected function pushGroupsFrom(Collection $groups, array $source, string $sourceLabel)
	: void{
		if ($source === []){
			return;
		}

		$candidates = $this->looksLikeGroup($source) ? [$source] : $source;

		foreach ($candidates as $candidate){
			if (!$this->looksLikeGroup($candidate)){
				Log::warning("Navigation config malformed in {$sourceLabel}: expected group shape ['heading' => ..., 'items' => [...]], got: " . json_encode($candidate));
				continue;
			}

			$groups->push($candidate);
		}
	}

	protected function looksLikeGroup(mixed $value)
	: bool{
		return is_array($value)
		       && array_key_exists('heading', $value)
		       && array_key_exists('items', $value)
		       && is_array($value['items']);
	}
}