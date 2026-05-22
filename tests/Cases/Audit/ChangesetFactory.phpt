<?php declare(strict_types = 1);

namespace Tests\Cases\Audit;

use Contributte\Tester\Toolkit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Nettrine\Extra\Audit\ChangesetFactory;
use Tester\Assert;
use Tests\Fixtures\Audit\DummyEntity;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$entity = new DummyEntity();

	$uow = Mockery::mock(UnitOfWork::class);
	$uow->shouldReceive('getEntityChangeSet')->with($entity)->once()->andReturn([
		'name' => [null, 'Felix'],
		'invalid' => [null, new \stdClass()],
	]);
	$uow->shouldReceive('getEntityIdentifier')->with($entity)->once()->andReturn(['id' => 10]);

	$metadata = new ClassMetadata(DummyEntity::class);
	$metadata->setPrimaryTable(['name' => 'dummy_table']);

	$em = Mockery::mock(EntityManagerInterface::class);
	$em->shouldReceive('getUnitOfWork')->times(2)->andReturn($uow);
	$em->shouldReceive('getClassMetadata')->with(DummyEntity::class)->once()->andReturn($metadata);

	$factory = (new ChangesetFactory('cli', $em))->withUserId(5);
	$changeset = $factory->forCreate($entity);

	Assert::same('cli', $changeset->getEntrypoint());
	Assert::same('dummy_table', $changeset->getEntityTable());
	Assert::same('10', $changeset->getEntityId());
	Assert::same(5, $changeset->getUser());
	Assert::null($changeset->getOldData());
	Assert::same(['name' => 'Felix'], json_decode((string) $changeset->getNewData(), true));

	Mockery::close();
});

Toolkit::test(function (): void {
	$entity = new DummyEntity();

	$uow = Mockery::mock(UnitOfWork::class);
	$uow->shouldReceive('getEntityChangeSet')->with($entity)->once()->andReturn([
		'name' => ['Felix', 'F3l1x'],
	]);
	$uow->shouldReceive('getOriginalEntityData')->with($entity)->once()->andReturn([
		'name' => 'F3l1x',
	]);
	$uow->shouldReceive('getEntityIdentifier')->with($entity)->times(2)->andReturn(['id' => 11]);

	$metadata = new ClassMetadata(DummyEntity::class);
	$metadata->setPrimaryTable(['name' => 'dummy_table']);

	$em = Mockery::mock(EntityManagerInterface::class);
	$em->shouldReceive('getUnitOfWork')->times(4)->andReturn($uow);
	$em->shouldReceive('getClassMetadata')->with(DummyEntity::class)->times(2)->andReturn($metadata);

	$factory = new ChangesetFactory('worker', $em);
	$update = $factory->forUpdate($entity);
	$delete = $factory->forDelete($entity);

	Assert::same(['name' => 'Felix'], json_decode((string) $update->getOldData(), true));
	Assert::same(['name' => 'F3l1x'], json_decode((string) $update->getNewData(), true));
	Assert::same(['name' => 'F3l1x'], json_decode((string) $delete->getOldData(), true));
	Assert::null($delete->getNewData());

	Mockery::close();
});
