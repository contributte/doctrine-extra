<?php declare(strict_types = 1);

namespace Nettrine\Extra\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TGeneratedId
{

	#[ORM\Column(type: 'integer', nullable: false)]
	#[ORM\Id]
	#[ORM\GeneratedValue]
	protected int $id;

	public function getId(): int
	{
		return $this->id;
	}

	public function __clone()
	{
		unset($this->id);
	}

}
