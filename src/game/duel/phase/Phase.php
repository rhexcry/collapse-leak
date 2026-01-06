<?php

declare(strict_types=1);

namespace collapse\game\duel\phase;

use collapse\game\duel\Duel;
use collapse\player\CollapsePlayer;

abstract class Phase implements PhaseEventHandlerInterface{
	use PhaseEventHandlerDefaultImpl;

	public function __construct(
		protected readonly Duel $duel
	){}

	public function onSet() : void{}

	public function setScoreboard(CollapsePlayer $player) : void{}

	abstract public function onUpdate() : void;
}
