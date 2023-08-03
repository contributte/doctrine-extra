<?php declare(strict_types = 1);

namespace Tests\Fixtures\Query;

use Doctrine\ORM\QueryBuilder;
use Nettrine\Extra\Query\AbstractQuery;
use Tests\Fixtures\Entity\User;

class UserQuery extends AbstractQuery
{

	public function __construct()
	{
		$this->ons[] = static function (QueryBuilder $qb): QueryBuilder {
			$qb->from(User::class, 'u');
			$qb->select('u.id');

			return $qb;
		};
	}

	public function withName(string $name): self
	{
		$this->ons[] = static function (QueryBuilder $qb) use ($name): QueryBuilder {
			$qb->andWhere('u.name = :name')
				->setParameter('name', $name);

			return $qb;
		};

		return $this;
	}

}
