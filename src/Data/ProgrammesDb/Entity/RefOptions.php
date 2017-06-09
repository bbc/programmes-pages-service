<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="entity_id_idx", columns={"entity_id"})})
 */
class RefOptions
{
    const TYPE_LOCAL = 'local';
    const TYPE_ADMIN = 'admin';

    use TimestampableEntity;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=36, unique=true, nullable=false)
     */
    private $guid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=35, nullable=false)
     */
    private $projectId;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $entityId;

    /**
     * local|admin
     *
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=false)
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=false)
     */
    private $options;

    public function __construct(
        string $guid,
        string $projectId,
        string $entityId,
        string $type,
        array $options = []
    ) {
        $this->guid = $guid;
        $this->projectId = $projectId;
        $this->entityId = $entityId;
        $this->setType($type);
        $this->options = $options;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function setGuid(string $guid)
    {
        $this->guid = $guid;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function setProjectId(string $projectId)
    {
        $this->projectId = $projectId;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (!in_array($type, [self::TYPE_ADMIN, self::TYPE_LOCAL])) {
            throw new InvalidArgumentException('Type document for options not allowed');
        }

        $this->type = $type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }
}
