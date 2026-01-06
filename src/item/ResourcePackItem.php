<?php

declare(strict_types=1);

namespace collapse\item;

use collapse\item\component\ItemComponents;

interface ResourcePackItem{

	public function getRuntimeId() : int;

	public function addComponents(ItemComponents $components) : ItemComponents;
}
