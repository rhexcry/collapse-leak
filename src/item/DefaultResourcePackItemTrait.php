<?php

declare(strict_types=1);

namespace collapse\item;

use collapse\item\component\ItemComponents;

trait DefaultResourcePackItemTrait{

	public function getRuntimeId() : int{
		return $this->getTypeId();
	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components;
	}
}
