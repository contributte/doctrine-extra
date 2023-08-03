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

	public function findOne(AbstractQuery $query): mixed
	{
		return $query->doQuery($this->em)->getSingleResult();
	}

	public function findAll(AbstractQuery $query): mixed
	{
		return $query->doQuery($this->em)->getResult();
	}

}
