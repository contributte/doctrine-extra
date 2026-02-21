<?php declare(strict_types = 1);

namespace Tests\Cases\Utils;

use Nettrine\Extra\Utils\Helpers;
use Stringable;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Assert::same('123', Helpers::value(123));
Assert::same('1', Helpers::value(true));

$stringable = new class implements Stringable {

	public function __toString(): string
	{
		return 'stringable-value';
	}

};

Assert::same('stringable-value', Helpers::value($stringable));

$object = new class {

};

$objectValue = Helpers::value($object);

Assert::true(str_starts_with($objectValue, $object::class . '('));
Assert::true(str_ends_with($objectValue, ')'));

Assert::same('array', Helpers::value(['a']));
