<?php declare(strict_types = 1);

namespace Nettrine\Extra\Data;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @template T of object
 */
final class QueryPaginator
{

	/** @var DoctrinePaginator<T> */
	private DoctrinePaginator $paginator;

	private function __construct(Query $query)
	{
		$this->paginator = new DoctrinePaginator($query);
	}

	/**
	 * @return self<object>
	 */
	public static function of(Query $query, bool $outputWalkers = true): self
	{
		$self = new self($query);
		$self->paginator->setUseOutputWalkers($outputWalkers);

		return $self;
	}

	public function getCount(): int
	{
		return $this->paginator->count();
	}

	/**
	 * @return iterable<T>
	 */
	public function getEntities(): iterable
	{
	 /** @var iterable<T> $entities */
		$entities = $this->paginator->getIterator();

		return $entities;
	}

	public function getLimit(): ?int
	{
		return $this->paginator->getQuery()->getMaxResults();
	}

	public function getPage(): int
	{
		return (int) ($this->paginator->getQuery()->getFirstResult() / ($this->paginator->getQuery()->getMaxResults() ?? 1) + 1);
	}

}
