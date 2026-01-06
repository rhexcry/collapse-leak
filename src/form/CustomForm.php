<?php

declare(strict_types=1);

namespace collapse\form;

use pocketmine\form\FormValidationException;
use pocketmine\lang\Translatable;
use function count;
use function gettype;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;

class CustomForm extends CollapseForm{

	private array $labelMap = [];
	private array $validationMethods = [];

	public function __construct(?\Closure $callable){
		parent::__construct($callable);
		$this->data['type'] = 'custom_form';
		$this->data['title'] = '';
		$this->data['content'] = [];
	}

	public function processData(&$data) : void{
		if($data !== null && !is_array($data)){
			throw new FormValidationException('Expected an array response, got ' . gettype($data));
		}
		if(is_array($data)){
			if(count($data) !== count($this->validationMethods)){
				throw new FormValidationException('Expected an array response with the size ' . count($this->validationMethods) . ', got ' . count($data));
			}
			$new = [];
			foreach($data as $i => $v){
				$validationMethod = $this->validationMethods[$i] ?? null;
				if($validationMethod === null){
					throw new FormValidationException('Invalid element ' . $i);
				}
				if(!$validationMethod($v)){
					throw new FormValidationException('Invalid type given for element ' . $this->labelMap[$i]);
				}
				$new[$this->labelMap[$i]] = $v;
			}
			$data = $new;
		}
	}

	public function setTitle(Translatable|string $title) : self{
		$this->data['title'] = $title;
		return $this;
	}

	public function getTitle() : Translatable|string{
		return $this->data['title'];
	}

	public function addLabel(Translatable|string $text, Translatable|string|null $label = null) : self{
		$this->addContent(['type' => 'label', 'text' => $text]);
		$this->labelMap[] = $label ?? count($this->labelMap);
		$this->validationMethods[] = static fn($v) => $v === null;
		return $this;
	}

	public function addToggle(Translatable|string $text, bool $default = null, Translatable|string|null $label = null) : self{
		$content = ['type' => 'toggle', 'text' => $text];
		if($default !== null){
			$content['default'] = $default;
		}
		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
		$this->validationMethods[] = static fn($v) => is_bool($v);
		return $this;
	}

	public function addSlider(Translatable|string $text, int $min, int $max, int $step = -1, int $default = -1, Translatable|string|null $label = null) : self{
		$content = ['type' => 'slider', 'text' => $text, 'min' => $min, 'max' => $max];
		if($step !== -1){
			$content['step'] = $step;
		}
		if($default !== -1){
			$content['default'] = $default;
		}
		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
		$this->validationMethods[] = static fn($v) => (is_float($v) || is_int($v)) && $v >= $min && $v <= $max;
		return $this;
	}

	public function addStepSlider(Translatable|string $text, array $steps, int $defaultIndex = -1, Translatable|string|null $label = null) : self{
		$content = ['type' => 'step_slider', 'text' => $text, 'steps' => $steps];
		if($defaultIndex !== -1){
			$content['default'] = $defaultIndex;
		}
		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
		$this->validationMethods[] = static fn($v) => is_int($v) && isset($steps[$v]);
		return $this;
	}

	public function addDropdown(Translatable|string $text, array $options, int $default = null, Translatable|string|null $label = null) : self{
		$this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default]);
		$this->labelMap[] = $label ?? count($this->labelMap);
		$this->validationMethods[] = static fn($v) => is_int($v) && isset($options[$v]);
		return $this;
	}

	public function addInput(Translatable|string $text, string $placeholder = "", string $default = null, Translatable|string|null $label = null) : self{
		$this->addContent(['type' => 'input', 'text' => $text, 'placeholder' => $placeholder, 'default' => $default]);
		$this->labelMap[] = $label ?? count($this->labelMap);
		$this->validationMethods[] = static fn($v) => is_string($v);
		return $this;
	}

	private function addContent(array $content) : self{
		$this->data['content'][] = $content;
		return $this;
	}

	public function setContent(array $content) : self{
		$this->data['content'] = $content;
		return $this;
	}

	public function resetContent() : void{
		$this->setContent([]);
		$this->data['buttons'] = ['text' => 'test'];
		$this->validationMethods = [];
	}
}
