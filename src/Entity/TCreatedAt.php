<?php declare(strict_types = 1);

namespace Nettrine\Extra\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait TCreatedAt
{

	#[ORM\Column(type: 'datetime', nullable: false)]
	protected DateTime $createdAt;

	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}

	/**
	 * @internal
	 */
	#[ORM\PrePersist]
	public function setCreatedAt(): void
	{
		$this->createdAt = new DateTime();
	}

}
