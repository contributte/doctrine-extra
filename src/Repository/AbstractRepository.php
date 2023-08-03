<?php declare(strict_types = 1);

namespace Nettrine\Extra\Repository;

use Doctrine\ORM\EntityRepository;
use Nettrine\Extra\Entity\AbstractEntity;
use Nettrine\Extra\Exception\Runtime\EntityNotFoundException;

/**
 * @phpstan-template TEntityClass of AbstractEntity
 * @phpstan-extends EntityRepository<TEntityClass>
 */
abstract class AbstractRepository extends EntityRepository
{

	/**
	 * @param 0|1|2|4|null $lockMode
	 * @return TEntityClass
	 * @throws EntityNotFoundException
	 */
	public function fetch(int $id, ?int $lockMode = null, ?int $lockVersion = null): object
	{
		$entity = $this->find($id, $lockMode, $lockVersion);

		if ($entity === null) {
			throw EntityNotFoundException::createForId($this->getClassName(), $id);
		}

		return $entity;
	}

	/**
	 * @param array<string,mixed> $criteria
	 * @param array<string,string>|null $orderBy
	 * @return TEntityClass
	 * @throws EntityNotFoundException
	 */
	public function fetchBy(array $criteria, ?array $orderBy = null): object
	{
		$entity = $this->findOneBy($criteria, $orderBy);

		if ($entity === null) {
			throw EntityNotFoundException::createForCriteria($this->getClassName(), $criteria);
		}

		return $entity;
	}

	/**
	 * Fetches all records like $key => $value pairs
	 *
	 * @param array<string,mixed> $criteria
	 * @param array<string,string> $orderBy
	 * @return array<scalar, scalar|TEntityClass>
	 */
	public function findPairs(?string $key, string $value, array $criteria = [], array $orderBy = []): array
	{
		if ($key === null) {
			$key = $this->getClassMetadata()->getSingleIdentifierFieldName();
		}

		$qb = $this->createQueryBuilder('e')
			->select(['e.' . $value, 'e.' . $key])
			->resetDQLPart('from')
			->from($this->getEntityName(), 'e', 'e.' . $key);

		foreach ($criteria as $v) {
			if (is_array($v)) {
				$qb->andWhere(sprintf('e.%s IN(:%s)', $key, $key))->setParameter($key, array_values($v));
			} else {
				$qb->andWhere(sprintf('e.%s = :%s', $key, $key))->setParameter($key, $v);
			}
		}

		foreach ($orderBy as $column => $order) {
			$qb->addOrderBy($column, $order);
		}

		/** @var array<TEntityClass> $result */
		$result = $qb->getQuery()->getArrayResult();

		return array_map(fn ($row) => reset($row), $result);
	}

}
