<?php

declare(strict_types=1);

namespace collapse\player\client;

use pocketmine\network\mcpe\protocol\types\DeviceOS;

final readonly class DeviceUtils{

	private function __construct(){}

	public static function toDisplayName(int $os) : string{
		return match($os){
			DeviceOS::ANDROID => 'Android',
			DeviceOS::IOS => 'iOS',
			DeviceOS::OSX => 'osX',
			DeviceOS::AMAZON => 'Amazon',
			DeviceOS::GEAR_VR => 'Gear VR',
			DeviceOS::HOLOLENS => 'Hololens',
			DeviceOS::WINDOWS_10 => 'Windows',
			DeviceOS::WIN32 => 'Windows 32',
			DeviceOS::DEDICATED => 'Dedicated',
			DeviceOS::TVOS => 'tvOS',
			DeviceOS::PLAYSTATION => 'PlayStation',
			DeviceOS::NINTENDO => 'Nintendo',
			DeviceOS::XBOX => 'Xbox',
			DeviceOS::WINDOWS_PHONE => 'Windows Phone',
			default => 'Unknown',
		};
	}

	public static function toFont(int $os) : string{
		return match($os){
			DeviceOS::ANDROID => '',
			DeviceOS::IOS => '',
			DeviceOS::OSX => 'osX',
			DeviceOS::AMAZON => 'Amazon',
			DeviceOS::GEAR_VR => 'Gear VR',
			DeviceOS::HOLOLENS => 'Hololens',
			DeviceOS::WINDOWS_10, DeviceOS::WIN32 => '',
			DeviceOS::DEDICATED => 'Dedicated',
			DeviceOS::TVOS => 'tvOS',
			DeviceOS::PLAYSTATION => '',
			DeviceOS::NINTENDO => '',
			DeviceOS::XBOX => '',
			DeviceOS::WINDOWS_PHONE => 'Windows Phone',
			default => 'Unknown',
		};
	}
}
