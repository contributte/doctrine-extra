<?php declare(strict_types = 1);

namespace Tests\Cases\Repository;

use Doctrine\DBAL\LockMode;
use Nettrine\Extra\Exception\Runtime\EntityNotFoundException;
use Nettrine\Extra\Repository\AbstractRepository;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$repository = new class extends AbstractRepository {

	public ?object $findEntity = null;

	public ?object $findOneByEntity = null;

	/** @var array<string, mixed>|null */
	public ?array $lastCriteria = null;

	/** @var array<string, mixed>|null */
	public ?array $lastOrderBy = null;

	public function __construct()
	{
		// Intentionally empty for isolated behavior testing.
	}

	public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object
	{
		return $this->findEntity;
	}

	/**
	 * @param array<string, mixed> $criteria
	 * @param array<string, string>|null $orderBy
	 */
	public function findOneBy(array $criteria, ?array $orderBy = null): ?object
	{
		$this->lastCriteria = $criteria;
		$this->lastOrderBy = $orderBy;

		return $this->findOneByEntity;
	}

	public function getClassName(): string
	{
		return 'App\\Model\\User';
	}

};

$entity = (object) ['id' => 'user-1'];
$repository->findEntity = $entity;

Assert::same($entity, $repository->fetch('user-1'));

$repository->findOneByEntity = $entity;

Assert::same($entity, $repository->fetchOneBy(['email' => 'john@example.com'], ['id' => 'DESC']));
Assert::same(['email' => 'john@example.com'], $repository->lastCriteria);
Assert::same(['id' => 'DESC'], $repository->lastOrderBy);

$repository->findEntity = null;

$idException = Assert::exception(
	fn () => $repository->fetch('missing-user'),
	EntityNotFoundException::class,
);

Assert::same('Entity "user" was not found by id "missing-user"', $idException->getMessage());

$repository->findOneByEntity = null;

$criteriaException = Assert::exception(
	fn () => $repository->fetchOneBy(['email' => 'missing@example.com']),
	EntityNotFoundException::class,
);

Assert::same(['email' => 'missing@example.com'], $criteriaException->criteria);
