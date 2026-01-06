<?php

declare(strict_types=1);

namespace collapse\system\internal\punish;

use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\FindOperation;
use collapse\mongo\operation\DeleteManyOperation;
use pocketmine\scheduler\Task;

final class PunishTask extends Task{
	public function __construct(
		private readonly PunishManager $manager
	){}

	public function onRun() : void{
		$currentTime = time();
		$db = $this->manager->getCollection()->getDatabaseName();
		$collection = $this->manager->getCollection()->getCollectionName();
		MongoWrapper::push(new FindOperation(
			$db,
			$collection,
			['expires_at' => ['$lte' => $currentTime, '$ne' => null]]
		))->onResolve(function(array $expiredDocs) use ($currentTime, $db, $collection) : void{
			$ipsToCheck = [];
			foreach($expiredDocs as $doc){
				$ipsToCheck[$doc['ip']] = true;
			}
			foreach(array_keys($ipsToCheck) as $ip){
				MongoWrapper::push(new FindOperation(
					$db,
					$collection,
					['ip' => $ip, '$or' => [['expires_at' => null], ['expires_at' => ['$gt' => $currentTime]]]],
					['limit' => 1]
				))->onResolve(function(array $activeDocs) use ($ip, $currentTime, $db, $collection) : void{
					if(empty($activeDocs)){
						$this->manager->removeIptablesRule($ip);
						MongoWrapper::push(new DeleteManyOperation(
							$db,
							$collection,
							['ip' => $ip, 'expires_at' => ['$lte' => $currentTime, '$ne' => null]]
						));
					}
				});
			}
		});
	}
}