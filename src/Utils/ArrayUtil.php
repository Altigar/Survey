<?php

namespace App\Utils;

class ArrayUtil
{
	public static function first(?array $data): mixed
	{
		return $data[0] ?? null;
	}
}
