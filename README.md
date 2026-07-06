![](https://heatbadger.now.sh/github/readme/contributte/doctrine-extra/)

<p align=center>
  <a href="https://github.com/contributte/doctrine-extra/actions"><img src="https://badgen.net/github/checks/contributte/doctrine-extra/master?cache=300"></a>
  <a href="https://codecov.io/gh/contributte/doctrine-extra"><img src="https://badgen.net/codecov/c/github/contributte/doctrine-extra"></a>
  <a href="https://packagist.org/packages/nettrine/extra"><img src="https://badgen.net/packagist/dm/nettrine/extra"></a>
  <a href="https://packagist.org/packages/nettrine/extra"><img src="https://badgen.net/packagist/v/nettrine/extra"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/nettrine/dbal"><img src="https://badgen.net/packagist/php/nettrine/extra"></a>
  <a href="https://github.com/contributte/doctrine-extra"><img src="https://badgen.net/github/license/contributte/doctrine-extra"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Opinionated extra functions to Doctrine ecosystem in [Nette Framework](https://nette.org).

## Versions

| State  | Version | Branch   | Nette  | PHP     |
|--------|---------|----------|--------|---------|
| dev    | `^0.3`  | `master` | `3.3+` | `>=8.2` |
| stable | `^0.2`  | `master` | `3.3+` | `>=8.2` |

## Installation

To install latest version of `nettrine/extra` use [Composer](https://getcomposer.org).

```bash
composer require nettrine/extra
```

## Usage

### Query Objects

Query objects are a good way how to decouple repository classes.

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

    public function actionDefault(): void
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

We've made a few skeletons with preconfigured Nettrine and Contributte packages.

- https://github.com/contributte/doctrine-skeleton
- https://github.com/contributte/webapp-skeleton
- https://github.com/contributte/apitte-skeleton

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.com/partners) **contributte** development team.
Also thank you for using this package.
