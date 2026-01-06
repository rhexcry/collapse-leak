<?php

declare(strict_types=1);

namespace collapse\skin\geometry;

enum GeometryType : string{

	case HUMAN = 'humanoid';
	case SLIM = 'slim';
	case ARMOR = 'armor';
	case CUSTOM = 'custom';

	public function createGeometry() : SkinGeometry{
		return match($this){
			self::HUMAN => (new GeometryBuilder())
				->withBone('head', [
					['origin' => [-4, 0, -4], 'size' => [8, 8, 8]]
				])
				->build('geometry.humanoid'),

			self::SLIM => (new GeometryBuilder())
				->withBone('head', [
					['origin' => [-4, 0, -4], 'size' => [8, 8, 8]]
				])
				->withBone('leftArm', [
					['origin' => [-5, 2, -2], 'size' => [3, 12, 4]]
				])
				->build('geometry.slim'),

			self::ARMOR => (new GeometryBuilder())
				->withBone('armorHead', [
					['origin' => [-4, 0, -4], 'size' => [8, 8, 8], 'inflate' => 0.5]
				])
				->build('geometry.armor'),

			// TODO: CUSTOM
		};
	}
}
