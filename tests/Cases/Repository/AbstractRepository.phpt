<?php declare(strict_types = 1);

namespace Tests\Cases\Repository;

use Nettrine\Extra\Exception\Runtime\EntityNotFoundException;
use Tester\Assert;
use Tests\Mocks\Repository\RepositoryMock;

require_once __DIR__ . '/../../bootstrap.php';

$repository = new RepositoryMock();

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
