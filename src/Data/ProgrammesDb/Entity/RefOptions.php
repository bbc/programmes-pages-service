<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class RefOptions
{
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
     * @var array
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $originalId;

    /**
     * @var CoreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $optionsForCoreEntity;

    /**
     * @var Network
     *
     * @ORM\ManyToOne(targetEntity="Network")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $optionsForNetwork;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $adminOptions;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $localOptions;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $projectSpace;

    /**
     * RefOptions constructor.
     * @param string $originalId
     * @param CoreEntity|Network $optionsFor
     */
    public function __construct(string $originalId, $optionsFor)
    {
        $this->setOriginalId($originalId);
        $this->setOptionsFor($optionsFor);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalId(): string
    {
        return $this->originalId;
    }

    public function setOriginalId(string $id): void
    {
        $this->originalId = $id;
    }

    /**
     * @return CoreEntity|Network
     */
    public function getOptionsFor()
    {
        return $this->optionsForCoreEntity ?? $this->optionsForNetwork;
    }

    public function getOptionsForCoreEntity(): ?CoreEntity
    {
        return $this->optionsForCoreEntity;
    }

    public function getOptionsForNetwork(): ?Network
    {
        return $this->optionsForNetwork;
    }

    public function getAdminOptions(): ?array
    {
        return $this->adminOptions;
    }

    public function setAdminOptions(?array $options): void
    {
        $this->adminOptions = $options;
    }

    public function getLocalOptions(): ?array
    {
        return $this->localOptions;
    }

    public function setLocalOptions(?array $options): void
    {
        $this->localOptions = $options;
    }

    public function getProjectSpace(): ?string
    {
        return $this->projectSpace;
    }

    public function setProjectSpace(?string $projectSpace): void
    {
        $this->projectSpace = $projectSpace;
    }

    /**
     * @param CoreEntity|Network $item
     */
    public function setOptionsFor($item): void
    {
        if ($item instanceof CoreEntity) {
            $this->setOptionsForBatch($item, null);
        } elseif ($item instanceof Network) {
            $this->setOptionsForBatch(null, $item);
        } else {
            throw new InvalidArgumentException(sprintf(
                                                   'Expected setOptionsFor() to be called with an an instance of "%s" or "%s". Found instance of "%s"',
                                                   CoreEntity::CLASS,
                                                   Network::CLASS,
                                                   (is_object($item) ? get_class($item) : gettype($item))
                                               ));
        }
    }

    private function setOptionsForBatch(
        ?CoreEntity $optionsForCoreEntity,
        ?Network $optionsForNetwork
    ): void {
        $this->optionsForCoreEntity = $optionsForCoreEntity;
        $this->optionsForNetwork = $optionsForNetwork;
    }
}
