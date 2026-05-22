<?php declare(strict_types = 1);

namespace Tests\Cases\Audit;

use Contributte\Tester\Toolkit;
use DateTimeImmutable;
use Nettrine\Extra\Audit\Changeset;
use Nettrine\Extra\Audit\ChangesetQueue;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$queue = new ChangesetQueue();

	Assert::true($queue->isEmpty());
	Assert::null($queue->dequeue());

	$first = new Changeset(new DateTimeImmutable('2026-01-01 10:00:00.000000'), 'api', 'users', '1', null, ['name' => 'Felix'], null);
	$second = new Changeset(new DateTimeImmutable('2026-01-01 10:00:01.000000'), 'api', 'users', '2', ['name' => 'Felix'], ['name' => 'F3l1x'], 7);

	$queue->enqueue($first);
	$queue->enqueue($second);

	Assert::false($queue->isEmpty());
	Assert::same($first, $queue->dequeue());
	Assert::same($second, $queue->dequeue());
	Assert::null($queue->dequeue());
	Assert::true($queue->isEmpty());
});
