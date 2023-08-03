<?php declare(strict_types = 1);

namespace Nettrine\Extra\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait TUpdatedAt
{

	#[ORM\Column(type: 'datetime', nullable: false)]
	protected ?DateTime $updatedAt = null;

	public function getUpdatedAt(): ?DateTime
	{
		return $this->updatedAt;
	}

	/**
	 * @internal
	 */
	#[ORM\PreUpdate]
	public function setUpdatedAt(): void
	{
		$this->updatedAt = new DateTime();
	}

}
