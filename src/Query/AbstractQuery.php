<?php declare(strict_types = 1);

namespace Nettrine\Extra\Query;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T
 * @implements Queryable<T>
 */
class AbstractQuery implements Queryable
{

	/** @var array<(callable(QueryBuilder):QueryBuilder)> */
	protected array $ons = [];

	public function setup(): void
	{
		// Can be defined in child.
	}

	public function doQuery(EntityManagerInterface $em): Query
	{
		$qb = $em->createQueryBuilder();

		$this->setup();

		foreach ($this->ons as $on) {
			$qb = $on($qb);
		}

		return $qb->getQuery();
	}

}
