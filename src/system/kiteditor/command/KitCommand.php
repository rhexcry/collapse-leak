<?php

declare(strict_types=1);

namespace collapse\system\kiteditor\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\game\kit\Kit;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\system\kiteditor\KitEditorManager;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
final class KitCommand extends CollapseCommand{

	public function __construct(
		private readonly KitEditorManager $kitEditorManager
	){
		$this->setPermission('collapse.command.kit');
		parent::__construct('kit', 'Manage kit editor');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addEnum(0, 'kit_action', ['save', 'cancel', 'reset']);
		$kitNames = array_map(fn(Kit $kit) => $kit->value, array_filter(Kit::cases(), fn(Kit $kit) => $kit !== Kit::SkyWars && $kit !== Kit::Sumo));
		$this->commandArguments->addEnum(0, 'kit', $kitNames, true);
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_usage());
			return;
		}

		$action = strtolower($args[0]);

		match($action){
			'save' => $this->handleSave($sender),
			'cancel' => $this->handleCancel($sender),
			'reset' => $this->handleReset($sender, $args[1] ?? null),
			default => $this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_usage())
		};
	}

	private function handleSave(CollapsePlayer $sender) : void{
		if(!$this->kitEditorManager->isEditing($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_save_not_editing());
			return;
		}

		$session = $this->kitEditorManager->getSession($sender);
		$session->updateNewLayout($sender->getInventory()->getContents());
		$this->kitEditorManager->stopEditing($sender, true);
	}

	private function handleCancel(CollapsePlayer $sender) : void{
		if(!$this->kitEditorManager->isEditing($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_cancel_not_editing());
			return;
		}

		$this->kitEditorManager->stopEditing($sender);
	}

	private function handleReset(CollapsePlayer $sender, ?string $kitName) : void{
		if($kitName === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_reset_usage());
			return;
		}
		if($this->kitEditorManager->isEditing($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_reset_in_editing());
			return;
		}

		$kit = Kit::tryFrom(strtolower($kitName));
		if($kit === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_reset_invalid_kit());
			return;
		}

		$profile = $sender->getProfile();
		$profile->removeKitLayout($kit);
		$profile->save();

		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kit_reset_successfully($kit->toDisplayName()));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_kit_description();
	}
}