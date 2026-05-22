<?php declare(strict_types = 1);

namespace Tests\Cases\Audit;

use Contributte\Tester\Toolkit;
use Nettrine\Extra\Audit\AuditException;
use Nettrine\Extra\Audit\ChangesetData;
use Tester\Assert;
use Tests\Fixtures\Audit\EntityWithId;
use Tests\Fixtures\Audit\LocalAuditState;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$data = new ChangesetData();

	$data->add('string', 'A');
	$data->add('int', 42);
	$data->add('float', 3.14);
	$data->add('bool', true);
	$data->add('null', null);
	$data->add('enum', LocalAuditState::Ready);
	$data->add('entity', new EntityWithId(15));

	Assert::same([
		'string' => 'A',
		'int' => 42,
		'float' => 3.14,
		'bool' => true,
		'null' => null,
		'enum' => 'ready',
		'entity' => 15,
	], $data->data());
});

Toolkit::test(function (): void {
	$data = new ChangesetData();

	Assert::exception(
		function () use ($data): void {
			$data->add('unsupported', new \stdClass());
		},
		AuditException::class,
	);
});
