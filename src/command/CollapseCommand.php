<?php

declare(strict_types=1);

namespace collapse\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\Practice;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;

abstract class CollapseCommand extends Command{

	/** @var RequiresRank[] */
	private array $requiresRankAttributes = [];

	private bool $isOnlyForPlayers;

	protected ?CommandArguments $commandArguments = null;

	public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
		$this->recognizeAttributes();
		parent::__construct($name, $description, $usageMessage, $aliases);
	}

	private function recognizeAttributes() : void{
		$reflectionClass = new \ReflectionClass($this);
		$requiresRankAttributes = $reflectionClass->getAttributes(RequiresRank::class);

		foreach($requiresRankAttributes as $attribute){
			/** @var RequiresRank $requiresRank */
			$requiresRank = $attribute->newInstance();
			$this->requiresRankAttributes[] = $requiresRank;
		}

		$this->isOnlyForPlayers = !empty($reflectionClass->getAttributes(OnlyForPlayerCommand::class));
	}

	final public function getCommandArguments() : ?CommandArguments{
		return $this->commandArguments;
	}

	final protected function sendTranslatedMessage(CommandSender $sender, Translatable $translation, bool $prefix = true) : void{
		if($sender instanceof CollapsePlayer){
			$sender->sendTranslatedMessage($translation, $prefix);
		}else{
			$sender->sendMessage(Practice::getInstance()->getTranslatorManager()->getDefaultTranslator()->translate($translation));
		}
	}

	public function getCooldown() : ?float{
		return CommandManager::DEFAULT_COMMAND_COOLDOWN;
	}

	final public function testPermissionSilent(CommandSender $target, ?string $permission = null) : bool{
		if($target instanceof CollapsePlayer && $target->getProfile() !== null){
			$playerRank = $target->getProfile()->getRank();
			foreach($this->requiresRankAttributes as $requiresRank){
				if($playerRank->getPriority() < $requiresRank->getRank()->getPriority()){
					return false;
				}
			}
		}

		return parent::testPermissionSilent($target, $permission);
	}

	final public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if($this->isOnlyForPlayers && !$sender instanceof CollapsePlayer){
			$sender->sendMessage(TextFormat::RED . 'This command can only be used by players.');
			return;
		}

		if(!$this->testPermissionSilent($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::no_permission());
			return;
		}

		$this->onExecute($sender, $commandLabel, $args);
	}

	abstract protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void;

	abstract public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable;
}
