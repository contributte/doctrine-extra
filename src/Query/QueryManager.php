<?php declare(strict_types = 1);

namespace Nettrine\Extra\Query;

use Doctrine\ORM\EntityManagerInterface;

class QueryManager
{

	public function __construct(
		protected EntityManagerInterface $em
	)
	{
	}

	/**
	 * @template T
	 * @param Queryable<T> $query
	 * @return T
	 */
	public function findOne(Queryable $query): mixed
	{
		return $query->doQuery($this->em)->getSingleResult();
	}

	/**
	 * @template T
	 * @param Queryable<T> $query
	 * @return array<T>
	 */
	public function findAll(Queryable $query): mixed
	{
		return $query->doQuery($this->em)->getResult();
	}

}
