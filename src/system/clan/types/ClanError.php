<?php

declare(strict_types=1);

namespace collapse\system\clan\types;

enum ClanError{
	case AlreadyHasClanWithTag;
	case AlreadyHasClanWithName;
}
