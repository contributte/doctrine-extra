<?php declare(strict_types = 1);

namespace Nettrine\Extra\Data;

use Doctrine\DBAL\Driver\Result as DriverResult;
use Doctrine\DBAL\Result;
use LogicException;

final class DataUtils
{

	/**
	 * @return mixed[]
	 */
	public static function toPairs(Result|DriverResult $result, ?string $key = null, ?string $value = null): array
	{
		$rows = $result->fetchAllAssociative();

		if ($rows === []) {
			return [];
		}

		$keys = array_keys(reset($rows));
		if (count($keys) <= 0) {
			throw new LogicException('Result set does not contain any column.');
		} elseif ($key === null && $value === null) {
			if (count($keys) === 1) {
				[$value] = $keys;
			} else {
				[$key, $value] = $keys;
			}
		}

		$return = [];
		if ($key === null) {
			foreach ($rows as $row) {
				$return[] = ($value === null ? $row : $row[$value]);
			}
		} else {
			foreach ($rows as $row) {
				$return[$row[$key]] = ($value === null ? $row : $row[$value]);
			}
		}

		return $return;
	}

}
