<?php declare(strict_types = 1);

namespace Nettrine\Extra\Query;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

/**
 * @template T
 */
interface Queryable
{

	public function doQuery(EntityManagerInterface $em): Query;

}
