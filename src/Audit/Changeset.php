<?php declare(strict_types = 1);

namespace Nettrine\Extra\Audit;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JsonException;

#[ORM\Entity]
#[ORM\Table(name: 'AuditChangesets')]
class Changeset
{

	private const DATETIME_FORMAT = 'Y-m-d H:i:s.u';

	#[ORM\Id]
	#[ORM\Column(type: 'string', length: 26)]
	private string $datetime;

	#[ORM\Column(type: 'string', length: 32)]
	private string $entrypoint;

	#[ORM\Column(type: 'integer', nullable: true)]
	private ?int $user;

	#[ORM\Column(type: 'string', length: 255)]
	private string $entityTable;

	#[ORM\Column(type: 'string', length: 128)]
	private string $entityId;

	#[ORM\Column(type: 'text', nullable: true)]
	private ?string $oldData;

	#[ORM\Column(type: 'text', nullable: true)]
	private ?string $newData;

	/**
	 * @param array<string, mixed>|null $oldData
	 * @param array<string, mixed>|null $newData
	 */
	public function __construct(
		DateTimeImmutable $datetime,
		string $entrypoint,
		string $entityTable,
		string $entityId,
		?array $oldData,
		?array $newData,
		?int $user,
	)
	{
		$this->datetime = $datetime->format(self::DATETIME_FORMAT);
		$this->entrypoint = $entrypoint;
		$this->entityTable = $entityTable;
		$this->entityId = $entityId;
		$this->oldData = $this->encode($oldData);
		$this->newData = $this->encode($newData);
		$this->user = $user;
	}

	public function getDatetime(): string
	{
		return $this->datetime;
	}

	public function getEntrypoint(): string
	{
		return $this->entrypoint;
	}

	public function getUser(): ?int
	{
		return $this->user;
	}

	public function getEntityTable(): string
	{
		return $this->entityTable;
	}

	public function getEntityId(): string
	{
		return $this->entityId;
	}

	public function getOldData(): ?string
	{
		return $this->oldData;
	}

	public function getNewData(): ?string
	{
		return $this->newData;
	}

	/**
	 * @param array<string, mixed>|null $data
	 */
	private function encode(?array $data): ?string
	{
		if ($data === null) {
			return null;
		}

		try {
			return json_encode($data, JSON_THROW_ON_ERROR);
		} catch (JsonException $e) {
			throw new AuditException($e->getMessage(), 0, $e);
		}
	}

}
