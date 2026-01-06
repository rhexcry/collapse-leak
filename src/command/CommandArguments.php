<?php

declare(strict_types=1);

namespace collapse\command;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandHardEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use function array_map;
use function count;
use function strtolower;

final class CommandArguments{

	/** @var CommandParameter[][] */
	private array $parameters = [];

	/** @var CommandOverload[] */
	private array $overloads = [];

	public function addParameter(int $column, string $paramName, int $type = AvailableCommandsPacket::ARG_TYPE_RAWTEXT, bool $isOptional = false, string $enumName = null, array $enumValues = [], bool $customType = false, string $postfix = null) : int{
		$parameter = new CommandParameter();
		$parameter->paramName = $paramName;
		$parameter->paramType = $customType ? $type : AvailableCommandsPacket::ARG_FLAG_VALID | $type;
		$parameter->isOptional = $isOptional;
		$parameter->postfix = $postfix;

		$this->parameters[$column][] = $parameter;
		$columnKey = count($this->parameters[$column]) - 1;

		if($enumName !== null){
			$this->setEnum($column, $columnKey, $enumName, $enumValues);
		}

		$this->overloads = [];
		foreach($this->parameters as $parameters){
			$this->overloads[] = new CommandOverload(false, $parameters);
		}

		return $columnKey;
	}

	private function setEnum(int $columnId, int $columnKey, ?string $name, array $values = []) : void{
		$parameter = $this->parameters[$columnId][$columnKey] ?? null;
		if($parameter === null){
			return;
		}

		$parameter->enum = $name === null ? null : new CommandHardEnum($name, array_map(fn(string $enumValue) : string => strtolower($enumValue), $values));
	}

	public function addEnum(int $column, string $name, array $values, bool $isOptional = false) : void{
		$columnKey = $this->addParameter($column, $name, AvailableCommandsPacket::ARG_FLAG_ENUM | AvailableCommandsPacket::ARG_TYPE_STRING, $isOptional);
		$this->setEnum($column, $columnKey, $name, $values);
	}

	public function getOverloads() : array{
		return $this->overloads;
	}
}
