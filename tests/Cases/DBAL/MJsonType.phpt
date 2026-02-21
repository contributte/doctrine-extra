<?php declare(strict_types = 1);

namespace Tests\Cases\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use JsonException;
use Mockery;
use Nettrine\Extra\DBAL\MJsonType;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$type = new MJsonType();

Assert::same(MJsonType::MJSON_TYPE, $type->getName());

$platform = Mockery::mock(AbstractPlatform::class);

Assert::same(
	'{"price":10.0,"name":"Žluťoučký"}',
	$type->convertToDatabaseValue([
		'price' => 10.0,
		'name' => 'Žluťoučký',
	], $platform),
);

Assert::null($type->convertToDatabaseValue(null, $platform));

$invalid = ['number' => INF];

try {
	$type->convertToDatabaseValue($invalid, $platform);
	Assert::fail('Expected conversion exception was not thrown.');
} catch (ConversionException $e) {
	Assert::type(JsonException::class, $e->getPrevious());
	Assert::true(str_contains($e->getMessage(), 'Could not convert PHP type'));
}

Mockery::close();
