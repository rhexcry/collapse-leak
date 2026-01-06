<?php

declare(strict_types=1);

namespace collapse\player\rank\attribute;

use Attribute;
use collapse\player\rank\Rank;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class RequiresRank{

	public function __construct(private Rank $rank){}

	public function getRank() : Rank{
		return $this->rank;
	}
}
