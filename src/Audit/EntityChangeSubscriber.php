<?php declare(strict_types = 1);

namespace Nettrine\Extra\Audit;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Throwable;

final class EntityChangeSubscriber implements EventSubscriber
{

	private ChangesetQueue $queue;

	public function __construct(
		private readonly LoggerInterface $logger,
		private readonly EntityManagerInterface $em,
		private readonly ChangesetFactory $changesetFactory,
	)
	{
		$this->queue = new ChangesetQueue();
	}

	/**
	 * @return array<int, string>
	 */
	public function getSubscribedEvents(): array
	{
		return [
			Events::postPersist,
			Events::postUpdate,
			Events::preRemove,
			Events::postFlush,
		];
	}

	public function postPersist(PostPersistEventArgs $eventArgs): void
	{
		$this->log(
			$eventArgs->getObject(),
			fn (object $entity): Changeset => $this->changesetFactory->forCreate($entity),
		);
	}

	public function postUpdate(PostUpdateEventArgs $eventArgs): void
	{
		$this->log(
			$eventArgs->getObject(),
			fn (object $entity): Changeset => $this->changesetFactory->forUpdate($entity),
		);
	}

	public function preRemove(PreRemoveEventArgs $eventArgs): void
	{
		$this->log(
			$eventArgs->getObject(),
			fn (object $entity): Changeset => $this->changesetFactory->forDelete($entity),
		);
	}

	public function postFlush(): void
	{
		if ($this->queue->isEmpty()) {
			return;
		}

		while (($changeset = $this->queue->dequeue()) !== null) {
			$this->em->persist($changeset);
		}

		$this->em->flush();
	}

	/**
	 * @param callable(object): Changeset $factory
	 */
	private function log(object $object, callable $factory): void
	{
		if ($object instanceof Changeset) {
			return;
		}

		try {
			$changeset = $factory($object);
			$this->queue->enqueue($changeset);
		} catch (Throwable $e) {
			$this->logger->error($e->getMessage());
		}
	}

}
