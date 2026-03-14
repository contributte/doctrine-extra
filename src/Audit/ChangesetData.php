<?php declare(strict_types = 1);

namespace Nettrine\Extra\Audit;

use BackedEnum;
use Doctrine\ORM\PersistentCollection;
use Nettrine\Extra\Entity\AbstractEntity;

class ChangesetData
{

	/** @var array<string, mixed> */
	private array $data = [];

	/**
	 * @throws AuditException
	 */
	public function add(string $property, mixed $value): void
	{
		if ($value instanceof PersistentCollection) {
			return;
		}

		$serializable = match (true) {
			$value instanceof BackedEnum => $value->value,
			$value instanceof AbstractEntity => $this->resolveEntityId($value, $property),
			is_scalar($value),
			$value === null => $value,
			default => throw new AuditException(sprintf('Unknown changeset value type for "%s".', $property)),
		};

		$this->data[$property] = $serializable;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function data(): array
	{
		return $this->data;
	}

	private function resolveEntityId(AbstractEntity $entity, string $property): mixed
	{
		if (!method_exists($entity, 'getId')) {
			throw new AuditException(sprintf('Entity value for "%s" does not provide getId().', $property));
		}

		$reflection = new \ReflectionMethod($entity, 'getId');

		/** @var mixed $id */
		$id = $reflection->invoke($entity);
		if (!is_scalar($id) && $id !== null) {
			throw new AuditException(sprintf('Entity id for "%s" must be scalar or null.', $property));
		}

		return $id;
	}

}
