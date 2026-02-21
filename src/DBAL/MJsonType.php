<?php declare(strict_types = 1);

namespace Nettrine\Extra\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use JsonException;

final class MJsonType extends JsonType
{

	public const MJSON_TYPE = 'mjson';

	public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
	{
		if ($value === null) {
			return null;
		}

		try {
			return json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE);
		} catch (JsonException $e) {
			throw new ConversionException(
				sprintf(
					"Could not convert PHP type '%s' to '%s', as an '%s' error was triggered by the serialization",
					gettype($value),
					'json',
					$e->getMessage(),
				),
				0,
				$e,
			);
		}
	}

	public function getName(): string
	{
		return self::MJSON_TYPE;
	}

}
