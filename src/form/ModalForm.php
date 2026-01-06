<?php

declare(strict_types=1);

namespace collapse\form;

use pocketmine\form\FormValidationException;
use pocketmine\lang\Translatable;
use function gettype;
use function is_bool;

class ModalForm extends CollapseForm{

	private string $content = '';

	public function __construct(?\Closure $callable){
		parent::__construct($callable);
		$this->data['type'] = 'modal';
		$this->data['title'] = '';
		$this->data['content'] = $this->content;
		$this->data['button1'] = '';
		$this->data['button2'] = '';
	}

	public function processData(&$data) : void{
		if(!is_bool($data)){
			throw new FormValidationException('Expected a boolean response, got ' . gettype($data));
		}
	}

	public function setTitle(Translatable|string $title) : self{
		$this->data['title'] = $title;
		return $this;
	}

	public function getTitle() : Translatable|string{
		return $this->data['title'];
	}

	public function getContent() : Translatable|string{
		return $this->data['content'];
	}

	public function setContent(Translatable|string $content) : self{
		$this->data['content'] = $content;
		return $this;
	}

	public function setButton1(Translatable|string $text) : self{
		$this->data['button1'] = $text;
		return $this;
	}

	public function getButton1() : Translatable|string{
		return $this->data['button1'];
	}

	public function setButton2(Translatable|string $text) : self{
		$this->data['button2'] = $text;
		return $this;
	}

	public function getButton2() : Translatable|string{
		return $this->data['button2'];
	}
}
