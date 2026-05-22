<?php declare(strict_types = 1);

namespace Nettrine\Extra\Utils;

use Stringable;

final class Helpers
{

	public static function value(mixed $input): string
	{
		if (is_scalar($input)) {
			return (string) $input;
		}

		if ($input instanceof Stringable) {
			return (string) $input;
		}

		if (is_object($input)) {
			return $input::class . '(' . spl_object_id($input) . ')';
		}

		return gettype($input);
	}

}
