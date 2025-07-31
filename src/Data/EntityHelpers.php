<?php declare(strict_types = 1);

namespace Nettrine\Extra\Data;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;

final class EntityHelpers
{

	public static function count(EntityManagerInterface $em, QueryBuilder $qb): int
	{
		$clonedQb = clone $qb;
		$clonedQb->setFirstResult(0);
		$clonedQb->resetDQLPart('orderBy');

		// @phpstan-ignore-next-line
		return $em->getConnection()
			->createQueryBuilder()
			->select('COUNT(*)')
			->from(sprintf('(%s) as dc', $clonedQb->getQuery()->getSQL())) // @phpstan-ignore-line
			->setParameters(array_map(static fn (Parameter $param) => $param->getValue(), $qb->getParameters()->toArray())) // @phpstan-ignore-line
			->executeQuery()
			->fetchOne();
	}

}
