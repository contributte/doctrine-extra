<?php declare(strict_types = 1);

namespace Tests\Cases\Query;

use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Doctrine\DBAL\Driver\SQLite3\Driver;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Proxy\ProxyFactory;
use Tester\Assert;
use Tests\Fixtures\Query\UserQuery;
use Tests\Toolkit\Tests;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$config = new Configuration();
	$config->setAutoGenerateProxyClasses(ProxyFactory::AUTOGENERATE_NEVER);
	$config->setProxyDir(Environment::getTestDir());
	$config->setProxyNamespace('Doctrine\Tests\Proxies');
	if (PHP_VERSION_ID >= 80400) {
		$config->enableNativeLazyObjects(true);
	}

	$config->setMetadataDriverImpl(new AttributeDriver([
		Tests::FIXTURES_PATH . '/Entity',
	]));

	$entityManager = new EntityManager(
		DriverManager::getConnection([
			'driverClass' => Driver::class,
			'memory' => true,
		], $config),
		$config
	);

	$q = (new UserQuery())->withName('Felix');
	$query = $q->doQuery($entityManager);

	Assert::equal('SELECT u0_.id AS id_0 FROM User u0_ WHERE u0_.name = ?', $query->getSQL());
});
