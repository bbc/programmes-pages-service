<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="ref_options_entity_id_idx", columns={"entity_id"})})
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
     * @var CoreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $coreEntity;

    /**
     * @var Network
     *
     * @ORM\ManyToOne(targetEntity="Network")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $network;

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

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $modifiedDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $creationDate;

    public function __construct(
        string $guid,
        string $projectId,
        $entity,
        string $type,
        DateTime $createdAt,
        DateTime $modifiedAt,
        array $options = []
    ) {
        $this->guid = $guid;
        $this->projectId = $projectId;
        $this->setEntity($entity);
        $this->setType($type);
        $this->modifiedAt = $modifiedAt;
        $this->createdAt = $createdAt;
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

    public function setEntity($entity)
    {
        if ($entity instanceof Network) {
            $this->network = $entity;
            $this->coreEntity = null;
        } elseif ($entity instanceof CoreEntity) {
            $this->coreEntity = $entity;
            $this->network = null;
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected an instance of "%s" or "%s". Found instance of "%s"',
                CoreEntity::CLASS,
                Network::CLASS,
                (is_object($entity) ? get_class($entity) : gettype($entity))
            ));
        }
    }

    /** @returns Network|CoreEntity|null */
    public function getEntity()
    {
        return $this->network ?? $this->coreEntity ?? null;
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

    public function getModifiedDate(): DateTime
    {
        return $this->modifiedDate;
    }

    public function setModifiedDate(DateTime $modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function setCreationDate(DateTime $creationDate)
    {
        $this->creationDate = $creationDate;
    }
}
