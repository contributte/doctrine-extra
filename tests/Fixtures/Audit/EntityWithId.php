<?php declare(strict_types = 1);

namespace Tests\Fixtures\Audit;

use Nettrine\Extra\Entity\AbstractEntity;

final class EntityWithId extends AbstractEntity
{

	public function __construct(
		private readonly int $id,
	)
	{
	}

	public function getId(): int
	{
		return $this->id;
	}

}
