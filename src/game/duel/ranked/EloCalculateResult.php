<?php

declare(strict_types=1);

namespace collapse\game\duel\ranked;

final readonly class EloCalculateResult{

	public function __construct(
		private int $winner,
		private int $winnerGain,
		private int $loser,
		private int $loserGain
	){}

	public function getWinner() : int{
		return $this->winner;
	}

	public function getWinnerGain() : int{
		return $this->winnerGain;
	}

	public function getLoser() : int{
		return $this->loser;
	}

	public function getLoserGain() : int{
		return $this->loserGain;
	}
}
