<?php declare(strict_types = 1);

namespace Nettrine\Extra\Data;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

final class QueryHelpers
{

	public static function applyCriteria(QueryBuilder $qb, QueryFilter $filter, string $alias): QueryBuilder
	{
		foreach ($filter->getCriteria() as $key => $value) {
			$qb->andWhere(sprintf('%s.%s = :%s', $alias, $key, $key))->setParameter($key, $value);
		}

		return $qb;
	}

	public static function applyOrders(QueryBuilder $qb, QueryFilter $filter, string $alias): QueryBuilder
	{
		foreach ($filter->getOrders() as $sort => $order) {
			$qb->orderBy($alias . '.' . $sort, $order);
		}

		return $qb;
	}

	public static function applyLimits(QueryBuilder $qb, QueryFilter $filter): QueryBuilder
	{
		if ($filter->getLimit() !== null) {
			$qb->setMaxResults($filter->getLimit());

			if ($filter->getPage() !== null) {
				$qb->setFirstResult(($filter->getPage() - 1) * $filter->getLimit());
			}
		}

		return $qb;
	}

	public static function count(EntityManagerInterface $em, QueryBuilder $qb): int
	{
		$clonedQb = clone $qb;
		$clonedQb->setMaxResults(null);
		$clonedQb->setFirstResult(0);
		$clonedQb->resetOrderBy();

		$cacheKey = sha1((string) json_encode([$clonedQb->getSQL(), $qb->getParameters()]));

		// @phpstan-ignore-next-line
		return $em->getConnection()
			->createQueryBuilder()
			->select('COUNT(*)')
			->from(sprintf('(%s) as dc', $clonedQb->getSQL()))
			->setParameters($qb->getParameters(), $qb->getParameterTypes())
			->enableResultCache(new QueryCacheProfile(60, $cacheKey))
			->executeQuery()
			->fetchOne();
	}

}
