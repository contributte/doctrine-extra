<?php declare(strict_types = 1);

namespace Nettrine\Extra\Exception\Runtime;

use Nettrine\Extra\Exception\RuntimeException;
use Nettrine\Extra\Utils\Helpers;
use Throwable;

final class EntityNotFoundException extends RuntimeException
{

	/** @var array<mixed>|null */
	public ?array $criteria = null;

	private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	public static function createForId(string $class, mixed $id): self
	{
		return new self(sprintf('Entity "%s" was not found by id "%s"', self::shortClassName($class), Helpers::value($id)));
	}

	/**
	 * @param array<mixed> $criteria
	 */
	public static function createForCriteria(string $class, array $criteria): self
	{
		$self = new self(sprintf('Entity "%s" was not found by criteria', self::shortClassName($class)));
		$self->criteria = $criteria;

		return $self;
	}

	private static function shortClassName(string $class): string
	{
		$shortName = strrchr($class, '\\');

		if ($shortName === false) {
			return strtolower($class);
		}

		return strtolower(substr($shortName, 1));
	}

}
