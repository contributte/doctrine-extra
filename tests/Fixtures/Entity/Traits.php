<?php declare(strict_types = 1);

namespace Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nettrine\Extra\Entity\TCreatedAt;
use Nettrine\Extra\Entity\TGeneratedId;
use Nettrine\Extra\Entity\TUpdatedAt;

#[ORM\Entity]
class Traits
{

	use TUpdatedAt;
	use TCreatedAt;
	use TGeneratedId;

}
