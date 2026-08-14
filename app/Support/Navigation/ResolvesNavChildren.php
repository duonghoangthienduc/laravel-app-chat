<?php
// app/Support/Navigation/ResolvesNavChildren.php

namespace App\Support\Navigation;

use Illuminate\Support\Collection;

interface ResolvesNavChildren{

	/**
	 * @return Collection<int, array{label: string, href: string, active: bool, initial?: string,
	 *     color?: string}>
	 */
	public function resolve()
	: Collection;
}