<?php

declare(strict_types=1);

namespace collapse\command\attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final class OnlyForPlayerCommand{}
