<?php declare(strict_types = 1);

namespace Nettrine\Extra\Audit;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class ChangesetFactory
{

	private ?int $userId = null;

	public function __construct(
		private readonly string $entrypoint,
		private readonly EntityManagerInterface $em,
	)
	{
	}

	public function withUserId(?int $userId): self
	{
		$this->userId = $userId;

		return $this;
	}

	public function forCreate(object $entity): Changeset
	{
		$uow = $this->em->getUnitOfWork();
		$changeSet = $uow->getEntityChangeSet($entity);

		$newData = new ChangesetData();
		foreach ($changeSet as $property => [, $newValue]) {
			try {
				$newData->add($property, $newValue);
			} catch (AuditException) {
				// Ignore unsupported property values in audit payload.
			}
		}

		return $this->create($entity, null, $newData->data());
	}

	public function forUpdate(object $entity): Changeset
	{
		$uow = $this->em->getUnitOfWork();
		$changeSet = $uow->getEntityChangeSet($entity);

		$oldData = new ChangesetData();
		$newData = new ChangesetData();
		foreach ($changeSet as $property => [$oldValue, $newValue]) {
			try {
				$oldData->add($property, $oldValue);
				$newData->add($property, $newValue);
			} catch (AuditException) {
				// Ignore unsupported property values in audit payload.
			}
		}

		return $this->create($entity, $oldData->data(), $newData->data());
	}

	public function forDelete(object $entity): Changeset
	{
		$uow = $this->em->getUnitOfWork();
		$originalEntityData = $uow->getOriginalEntityData($entity);

		$oldData = new ChangesetData();
		foreach ($originalEntityData as $property => $value) {
			try {
				$oldData->add($property, $value);
			} catch (AuditException) {
				// Ignore unsupported property values in audit payload.
			}
		}

		return $this->create($entity, $oldData->data(), null);
	}

	/**
	 * @param array<string, mixed>|null $oldData
	 * @param array<string, mixed>|null $newData
	 */
	private function create(object $entity, ?array $oldData, ?array $newData): Changeset
	{
		$table = $this->em->getClassMetadata($entity::class)->getTableName();
		$id = implode('-', array_map($this->normalizeIdentifier(...), $this->em->getUnitOfWork()->getEntityIdentifier($entity)));

		return new Changeset(
			new DateTimeImmutable(),
			$this->entrypoint,
			$table,
			$id,
			$oldData,
			$newData,
			$this->userId,
		);
	}

	private function normalizeIdentifier(mixed $value): string
	{
		if (is_scalar($value) || $value === null) {
			return (string) $value;
		}

		if ($value instanceof \Stringable) {
			return (string) $value;
		}

		throw new AuditException('Entity identifier contains unsupported value type.');
	}

}
