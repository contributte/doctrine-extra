<?php declare(strict_types = 1);

namespace Nettrine\Extra\Audit;

final class ChangesetQueue
{

	/** @var list<Changeset> */
	private array $queue = [];

	public function enqueue(Changeset $changeset): void
	{
		$this->queue[] = $changeset;
	}

	public function dequeue(): ?Changeset
	{
		if ($this->queue === []) {
			return null;
		}

		return array_shift($this->queue);
	}

	public function isEmpty(): bool
	{
		return $this->queue === [];
	}

}
