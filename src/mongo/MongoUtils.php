<?php

declare(strict_types=1);

namespace collapse\mongo;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use function array_filter;
use function is_array;
use function preg_match;

final readonly class MongoUtils{

	private function __construct(){}

	private static function bsonDocumentToArrayRecursive(array $array) : array{
		foreach(array_filter($array, fn(mixed $value) => ($value instanceof BSONDocument || $value instanceof BSONArray)) as $key => $value){
			$array[$key] = $value->getArrayCopy();
			$array = self::bsonDocumentToArrayRecursive($array);
		}
		return $array;
	}

	public static function bsonDocumentToArray(BSONDocument $document) : array{
		$array = $document->getArrayCopy();
		foreach(array_filter($array, fn(mixed $value) => ($value instanceof BSONDocument || $value instanceof BSONArray)) as $key => $value){
			$array[$key] = self::bsonDocumentToArrayRecursive($value->getArrayCopy());
		}
		return $array;
	}

	public static function bsonArrayToArray(BSONArray $array) : array{
		$result = $array->getArrayCopy();
		foreach(array_filter($result, fn(mixed $value) => ($value instanceof BSONDocument || $value instanceof BSONArray)) as $key => $value){
			$result[$key] = self::bsonDocumentToArrayRecursive($value->getArrayCopy());
		}
		return $result;
	}

	public static function sanitize(array &$data) : array{
		foreach($data as $key => $value){
			is_array($value) && !empty($item) && $data[$key] = self::sanitize($item);
			if(is_array($data) && preg_match('/^\$/', $key)){
				unset($data[$key]);
			}
		}
		return $data;
	}
}
