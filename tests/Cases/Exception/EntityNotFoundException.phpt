<?php declare(strict_types = 1);

namespace Tests\Cases\Exception;

use Nettrine\Extra\Exception\Runtime\EntityNotFoundException;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$idException = EntityNotFoundException::createForId('App\\Model\\User', 'user-1');

Assert::same('Entity "user" was not found by id "user-1"', $idException->getMessage());
Assert::null($idException->criteria);

$criteria = ['email' => 'john@example.com'];
$criteriaException = EntityNotFoundException::createForCriteria('App\\Model\\User', $criteria);

Assert::same('Entity "user" was not found by criteria', $criteriaException->getMessage());
Assert::same($criteria, $criteriaException->criteria);
