<?php

declare(strict_types=1);

namespace collapse\skin\geometry;

use collapse\Practice;
use Symfony\Component\Filesystem\Path;
use function file_exists;
use function file_get_contents;

final readonly class SkinGeometry{

	private string $data;

	public function __construct(
		private string $name,
		string $file
	){
		$this->data = $this->loadFromFile($file);
	}

	public function getName() : string{
		return $this->name;
	}

	public function getData() : string{
		return $this->data;
	}

	public function toArray() : array{
		return [
			'name' => $this->name,
			'data' => $this->data
		];
	}

	private function loadFromFile(string $file) : string{
		$path = $this->getResourcePath($file);
		if(!file_exists($path)){
			throw new \InvalidArgumentException('File with geometry ' . $path . ' not found.');
		}

		return file_get_contents($path);
	}

	private function getResourcePath(string $path) : string{
		return Path::join(Practice::getInstance()->getDataFolder(), 'geometry', $path);
	}
}
