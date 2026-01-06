<?php

declare(strict_types=1);

namespace collapse\game\duel\ranked;

use function max;
use function min;
use function pow;

final readonly class EloCalculator{

	public function __construct(
		private float $kPower,
		private int $minEloGain,
		private int $maxEloGain,
		private int $minEloLoss,
		private int $maxEloLoss
	){}

	public function calculate(int $winner, int $loser) : EloCalculateResult{
		$winnerQ = pow(10, $winner / 300.0);
		$loserQ = pow(10, $loser / 300.0);

		$winnerE = $winnerQ / ($winnerQ + $loserQ);
		$loserE = $loserQ / ($winnerQ + $loserQ);

		$winnerGain = (int) ($this->kPower * (1 - $winnerE));
		$loserGain = (int) ($this->kPower * (0 - $loserE));

		$winnerGain = min($winnerGain, $this->maxEloGain);
		$winnerGain = max($winnerGain, $this->minEloGain);

		$loserGain = min($loserGain, -$this->minEloLoss);
		$loserGain = max($loserGain, -$this->maxEloLoss);

		return new EloCalculateResult($winner + $winnerGain, $winnerGain, $loser + $loserGain, $loserGain);
	}
}
