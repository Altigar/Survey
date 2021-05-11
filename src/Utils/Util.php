<?php

namespace App\Utils;

class Util
{
	public static function reindexRelation(array $data, string $attribute): array
	{
		$method = 'get' . ucfirst($attribute);
		$result = [];
		foreach ($data as $object) {
			$result[$object->{$method}()->getId()] = $object;
		}
		return $result;
	}

	public static function reindex(array $data, string $attribute): array
	{
		$method = 'get' . ucfirst($attribute);
		$result = [];
		foreach ($data as $object) {
			$result[$object->{$method}()] = $object;
		}
		return $result;
	}

	public static function getColumn(array $data, string $attribute): array
	{
		$method = 'get' . ucfirst($attribute);
		$result = [];
		foreach ($data as $object) {
			$result[] = $object->{$method}();
		}
		return $result;
	}

	public static function first(?array $data): mixed
	{
		return $data[0] ?? null;
	}
}