<?php

declare(strict_types=1);

namespace collapse\player\skins;

use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\UpdateOneOperation;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\utils\SkinUtils;
use function base64_encode;

final class SkinManager{

	private const string COLLECTION = 'skins';

	public function save(CollapsePlayer $player) : void{
		$compressed = base64_encode(SkinUtils::skinToPNGdata($player->getSkin()->getSkinData()));
		MongoWrapper::push(new UpdateOneOperation(
			Practice::getDatabaseName(),
			self::COLLECTION,
			['xuid' => $player->getXuid()],
			['$set' => [
				'xuid' => $player->getXuid(),
				'skinDataCompressed' => $compressed,
				'skinId' => $player->getSkin()->getSkinId()
			]],
			[
				'upsert' => true
			]
		));
	}
}
