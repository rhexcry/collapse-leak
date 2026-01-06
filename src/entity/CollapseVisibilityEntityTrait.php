<?php

declare(strict_types=1);

namespace collapse\entity;

use collapse\player\CollapsePlayer;
use function array_filter;
use function array_merge;

trait CollapseVisibilityEntityTrait{

	private function getTargets() : ?array{
		$owningEntity = $this->getOwningEntity();
		return !$owningEntity instanceof CollapsePlayer ? null : array_merge(array_filter($owningEntity->getViewers(), static function(CollapsePlayer $player) use ($owningEntity) : bool{
			return $player->canSee($owningEntity);
		}), [$owningEntity]);
	}
}
