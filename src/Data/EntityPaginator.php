<?php declare(strict_types = 1);

namespace Nettrine\Extra\Data;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @template T
 */
final class EntityPaginator
{

	private QueryBuilder $qb;

	/** @var Paginator<T>|null */
	private ?Paginator $paginator = null;

	private function __construct(QueryBuilder $qb)
	{
		$this->qb = $qb;
	}

	/**
	 * @return self<T>
	 */
	public static function of(QueryBuilder $qb): self
	{
		return new self($qb); // @phpstan-ignore-line
	}

	/**
	 * @return self<T>
	 */
	public function applyFilter(QueryFilter $filter): self
	{
		$alias = $this->qb->getRootAliases()[0];

		foreach ($filter->getCriteria() as $key => $value) {
			$this->qb->andWhere(sprintf('%s.%s = :%s', $alias, $key, $key))->setParameter($key, $value);
		}

		foreach ($filter->getOrders() as $sort => $order) {
			$this->qb->orderBy($alias . '.' . $sort, $order);
		}

		if ($filter->getLimit() !== null) {
			$this->qb->setMaxResults($filter->getLimit());

			if ($filter->getPage() !== null) {
				$this->qb->setFirstResult(($filter->getPage() - 1) * $filter->getLimit());
			}
		}

		return $this;
	}

	public function getCount(): int
	{
		return $this->getPaginator()->count();
	}

	/**
	 * @return iterable<T>
	 */
	public function getEntities(): iterable
	{
		/** @var iterable<T> $entities */
		$entities = $this->getPaginator()->getIterator();

		return $entities;
	}

	public function getLimit(): ?int
	{
		return $this->getPaginator()->getQuery()->getMaxResults();
	}

	public function getPage(): int
	{
		return (int) ($this->getPaginator()->getQuery()->getFirstResult() / ($this->getPaginator()->getQuery()->getMaxResults() ?? 1) + 1);
	}

	/**
	 * @return Paginator<T>
	 */
	private function getPaginator(): Paginator
	{
		if ($this->paginator === null) {
			/** @var Paginator<T> $paginator */
			$paginator = new Paginator($this->qb->getQuery());
			$this->paginator = $paginator;
		}

		return $this->paginator;
	}

}
