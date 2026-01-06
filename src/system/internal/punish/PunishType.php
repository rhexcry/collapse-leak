<?php

declare(strict_types=1);

namespace collapse\system\internal\punish;

enum PunishType : string{
	case PacketSpam = 'packet_spam';
	case Manual = 'manual';
}