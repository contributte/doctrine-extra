# Contributte Doctrine Extra

> Opinionated extra functions to Doctrine ecosystem in [Nette Framework](https://nette.org).

## Content

- [Setup](#setup)
- [Usage](#usage)

## Setup

Install package

```bash
composer require nettrine/extra
```

## Usage

### Query objects

Query objects is a good way how to decouple repository classes.

You need to create child of `AbstractQuery` or implement `IQueryable`.

```php
<?php declare(strict_types = 1);

namespace App\Domain\User;

use Doctrine\ORM\QueryBuilder;
use Nettrine\Extra\Query\AbstractQuery;

class UserQuery extends AbstractQuery
{

	private function __construct()
	{
		$this->ons[] = static function (QueryBuilder $qb): QueryBuilder {
			$qb->from(User::class, 'u');
			$qb->select('u.id');

			return $qb;
		};
	}

	public static function create(): self
	{
	  return new self();
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
```

To execute this query object, you need to register QueryManager (manually in neon file).

```neon
services:
    - Nettrine\Extra\Query\QueryManager
```

After that, just execute it.

```php
class UserPresenter extends Presenter
{

    public fuction actionDefault(): void
    {
        $user = $this->queryManager->fetchOne(
            (new UserQuery())->withName('felix')
        );

        $users = $this->queryManager->fetchAll(
            (new UserQuery())->withRole('admin')
        );
    }

}
```

### Repository

We've prepared abstract repository class with few fetching methods.

```php
use Nettrine\Extra\Repository\AbstractRepository;

class UserRepository extends AbstractRepository
{
}
```

### Utils

We've prepared some utility classes.

- DataUtils
- OracleUtils
- QueryUtils

## Examples

We've made a few skeletons with preconfigured Nettrine nad Contributte packages.

- https://github.com/contributte/doctrine-skeleton
- https://github.com/contributte/webapp-skeleton
- https://github.com/contributte/apitte-skeleton
