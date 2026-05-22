<?php declare(strict_types = 1);

namespace Tests\Mocks\Utils;

use Stringable;

final class StringableMock implements Stringable
{

	public function __toString(): string
	{
		return 'stringable-value';
	}

}
