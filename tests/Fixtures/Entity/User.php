<?php declare(strict_types = 1);

namespace Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', nullable: false)]
	private string $id;

	#[ORM\Column(type: 'string', length: 255, nullable: false, unique: false)]
	private string $name;

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

}
