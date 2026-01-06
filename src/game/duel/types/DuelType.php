<?php

declare(strict_types=1);

namespace collapse\game\duel\types;

enum DuelType : string{

	case Unranked = 'unranked';

	case Ranked = 'ranked';

	case Request = 'request';

	case PartyRequest = 'party_request';

	public function isSolo() : bool{
		return $this === self::Unranked || $this === self::Ranked || $this === self::Request;
	}
}
