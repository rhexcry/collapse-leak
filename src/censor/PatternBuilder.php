<?php

declare(strict_types=1);

namespace collapse\censor;

use function implode;
use function sprintf;

final class PatternBuilder{

	public function build(array $characterMap, array $exceptions) : string{
		$patterns = [
			$this->buildPizdaPattern($characterMap),
			$this->buildHuiPattern($characterMap),
			$this->buildBlyadPattern($characterMap),
			$this->buildEbatPattern($characterMap),
			$this->buildPidorPattern($characterMap),
			$this->buildZalupaPattern($characterMap),
			$this->buildMandaPattern($characterMap),
			$this->buildShlyhaPattern($characterMap),
			$this->buildGondonPattern($characterMap),
		];

		return sprintf('/\b(?:%s)\b/iu', implode('|', $patterns));
	}

	private function buildPizdaPattern(array $chars) : string{
		return sprintf(
			'\w*[%s][%s%s][%s][%s]\w*',
			$chars['P'],
			$chars['I'],
			$chars['E'],
			$chars['Z'],
			$chars['D']
		);
	}

	private function buildHuiPattern(array $chars) : string{
		return sprintf(
			'(?:[^%s%s\s]+|%s%s)?(?<!стра)[%s][%s][%s%s%s%s%s%s%s](?!иг)\w*',
			$chars['I'],
			$chars['U'],
			$chars['N'],
			$chars['I'],
			$chars['H'],
			$chars['U'],
			$chars['YI'],
			$chars['E'],
			$chars['YA'],
			$chars['YO'],
			$chars['I'],
			$chars['L'],
			$chars['YU']
		);
	}

	private function buildBlyadPattern(array $chars) : string{
		return sprintf(
			'\w*[%s][%s](?:[%s]+[%s%s]?|[%s]+[%s%s]+|[%s]+[%s]+)(?!х)\w*',
			$chars['B'],
			$chars['L'],
			$chars['YA'],
			$chars['D'],
			$chars['T'],
			$chars['I'],
			$chars['D'],
			$chars['T'],
			$chars['I'],
			$chars['A']
		);
	}

	private function buildEbatPattern(array $chars) : string{
		return sprintf(
			'(?:\w*[%s%s%s%s%s%s%s%s%s][%s%s%s%s][%s%s](?!ы\b|ол)\w*|[%s%s][%s]\w*|[%s][%s][%s]\w+|[%s][%s][%s%s]\w*)',
			$chars['YI'],
			$chars['U'],
			$chars['E'],
			$chars['A'],
			$chars['O'],
			$chars['HS'],
			$chars['SS'],
			$chars['Y'],
			$chars['YA'],
			$chars['E'],
			$chars['YO'],
			$chars['YA'],
			$chars['I'],
			$chars['B'],
			$chars['P'],
			$chars['E'],
			$chars['YO'],
			$chars['B'],
			$chars['I'],
			$chars['B'],
			$chars['A'],
			$chars['YI'],
			$chars['O'],
			$chars['B'],
			$chars['P']
		);
	}

	private function buildPidorPattern(array $chars) : string{
		return sprintf(
			'\w*(?:[%s][%s%s][%s][%s%s%s]?[%s](?!о)\w*|[%s][%s][%s][%s%s]?[%s%s])',
			$chars['P'],
			$chars['I'],
			$chars['E'],
			$chars['D'],
			$chars['A'],
			$chars['O'],
			$chars['E'],
			$chars['R'],
			$chars['P'],
			$chars['E'],
			$chars['D'],
			$chars['E'],
			$chars['I'],
			$chars['G'],
			$chars['K']
		);
	}

	private function buildZalupaPattern(array $chars) : string{
		return sprintf(
			'\w*[%s][%s%s][%s][%s][%s]\w*',
			$chars['Z'],
			$chars['A'],
			$chars['O'],
			$chars['L'],
			$chars['U'],
			$chars['P']
		);
	}

	private function buildMandaPattern(array $chars) : string{
		return sprintf(
			'\w*[%s][%s][%s][%s][%s%s%s]\w*',
			$chars['M'],
			$chars['A'],
			$chars['N'],
			$chars['D'],
			$chars['A'],
			$chars['O'],
			$chars['U']
		);
	}

	private function buildShlyhaPattern(array $chars) : string{
		return sprintf(
			'\w*[%s][%s][%s%s][%s]*\w*',
			$chars['SH'],
			$chars['L'],
			$chars['U'],
			$chars['YU'],
			$chars['H']
		);
	}

	private function buildGondonPattern(array $chars) : string{
		return sprintf('\w*[%s][%s%s][%s][%s]*\w*', $chars['G'], $chars['A'], $chars['O'], $chars['N'], $chars['D']);
	}
}
