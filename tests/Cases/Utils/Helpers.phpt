<?php declare(strict_types = 1);

namespace Tests\Cases\Utils;

use Nettrine\Extra\Utils\Helpers;
use Tester\Assert;
use Tests\Mocks\Utils\ObjectMock;
use Tests\Mocks\Utils\StringableMock;

require_once __DIR__ . '/../../bootstrap.php';

Assert::same('123', Helpers::value(123));
Assert::same('1', Helpers::value(true));

$stringable = new StringableMock();
Assert::same('stringable-value', Helpers::value($stringable));

$object = new ObjectMock();

$objectValue = Helpers::value($object);

Assert::true(str_starts_with($objectValue, $object::class . '('));
Assert::true(str_ends_with($objectValue, ')'));

Assert::same('array', Helpers::value(['a']));
