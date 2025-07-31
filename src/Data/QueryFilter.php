<?php declare(strict_types = 1);

namespace Nettrine\Extra\Data;

class QueryFilter
{

	/** @var array<string, array<scalar>|scalar> */
	private array $criteria = [];

	private ?string $query = null;

	private ?int $page = null;

	private ?int $limit = null;

	/** @var array<string, 'desc'|'asc'> */
	private array $orders = [];

	/**
	 * @phpstan-param array{
	 *     q: array<string, array<scalar>|scalar>|null,
	 *     qs: string|null,
	 *     p: int|null,
	 *     l: int|null,
	 *     o: array<string, 'desc'|'asc'>|null
	 * } $filter
	 */
	public static function from(array $filter): self
	{
		$self = new self();

		if ($filter['q'] !== null) {
			$self->setCriteria($filter['q']);
		}

		if ($filter['qs'] !== null) {
			$self->setQuery($filter['qs']);
		}

		if ($filter['p'] !== null) {
			$self->setPage($filter['p']);
		}

		if ($filter['l'] !== null) {
			$self->setLimit($filter['l']);
		}

		if ($filter['o'] !== null) {
			$self->setOrders($filter['o']);
		}

		return $self;
	}

	/**
	 * @return array<string, array<scalar>|scalar>
	 */
	public function getCriteria(): array
	{
		return $this->criteria;
	}

	public function hasCriterion(string $name): bool
	{
		return array_key_exists($name, $this->criteria);
	}

	/**
	 * @return scalar
	 */
	public function pullCriterion(string $name): mixed
	{
		/** @var scalar $value */
		$value = $this->criteria[$name];
		unset($this->criteria[$name]);

		return $value;
	}

	/**
	 * @return array<scalar>
	 */
	public function pullCriterionAsArray(string $name): mixed
	{
		/** @var array<scalar> $value */
		$value = $this->criteria[$name];
		unset($this->criteria[$name]);

		return $value;
	}

	/**
	 * @param array<string, array<scalar>|scalar|null> $criteria
	 */
	public function setCriteria(array $criteria): void
	{
		foreach ($criteria as $key => $value) {
			// Skip invalid criteria
			if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
				continue;
			}

			$this->addCriterion($key, $value);
		}
	}

	/**
	 * @param array<scalar>|scalar $value
	 */
	public function addCriterion(string $name, array | string | int | float | bool $value): void
	{
		$this->criteria[$name] = $value;
	}

	public function setQuery(?string $query): void
	{
		$this->query = $query;
	}

	public function getQuery(): ?string
	{
		return $this->query;
	}

	public function getPage(): ?int
	{
		return $this->page;
	}

	public function setPage(int $page): void
	{
		$this->page = $page;
	}

	public function getLimit(): ?int
	{
		return $this->limit;
	}

	public function getOffset(): int
	{
		return (($this->getPage() ?? 0) - 1) * ($this->getLimit() ?? 0);
	}

	public function setLimit(?int $limit): void
	{
		$this->limit = $limit;
	}

	/**
	 * @return array<string, 'desc'|'asc'>
	 */
	public function getOrders(): array
	{
		return $this->orders;
	}

	/**
	 * @param array<string, scalar|null> $orders
	 */
	public function setOrders(array $orders): void
	{
		foreach ($orders as $sort => $order) {
			// Skip invalid orders
			if ($order !== 'asc' && $order !== 'desc') {
				continue;
			}

			$this->addOrder($sort, $order);
		}
	}

	/**
	 * @param 'desc'|'asc' $order
	 */
	public function addOrder(string $sort, string $order): void
	{
		$this->orders[$sort] = $order;
	}

	public function hasOrder(string $name): bool
	{
		return array_key_exists($name, $this->orders);
	}

	/**
	 * @return scalar
	 */
	public function pullOrder(string $name): mixed
	{
		$value = $this->orders[$name];
		unset($this->orders[$name]);

		return $value;
	}

}
