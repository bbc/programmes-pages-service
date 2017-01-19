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
     * @var CoreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $optionsForCoreEntity;

    /**
     * @var MasterBrand
     *
     * @ORM\ManyToOne(targetEntity="MasterBrand")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $optionsForMasterBrand;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $adminOptions;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $localOptions;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $projectSpace;

    /**
     * RefOptions constructor.
     * @param CoreEntity|MasterBrand $optionsFor
     */
    public function __construct($optionsFor)
    {
        $this->setOptionsFor($optionsFor);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return CoreEntity|MasterBrand
     */
    public function getOptionsFor()
    {
        return $this->optionsForCoreEntity ?? $this->optionsForMasterBrand;
    }

    public function getOptionsForCoreEntity(): ?CoreEntity
    {
        return $this->optionsForCoreEntity;
    }

    public function getOptionsForMasterBrand(): ?MasterBrand
    {
        return $this->optionsForMasterBrand;
    }

    public function getAdminOptions(): ?array
    {
        return $this->adminOptions;
    }

    public function setAdminOptions(?array $options)
    {
        $this->adminOptions = $options;
    }

    public function getLocalOptions(): ?array
    {
        return $this->localOptions;
    }

    public function setLocalOptions(?array $options)
    {
        $this->localOptions = $options;
    }

    public function getProjectSpace(): ?string
    {
        return $this->projectSpace;
    }

    public function setProjectSpace(?string $projectSpace)
    {
        $this->projectSpace = $projectSpace;
    }

    /**
     * @param CoreEntity|MasterBrand $item
     */
    public function setOptionsFor($item)
    {
        if ($item instanceof CoreEntity) {
            $this->setOptionsForBatch($item, null);
        } elseif ($item instanceof MasterBrand) {
            $this->setOptionsForBatch(null, $item);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected setOptionsFor() to be called with an an instance of "%s" or "%s". Found instance of "%s"',
                CoreEntity::CLASS,
                MasterBrand::CLASS,
                (is_object($item) ? get_class($item) : gettype($item))
            ));
        }
    }

    private function setOptionsForBatch(
        ?CoreEntity $optionsForCoreEntity,
        ?MasterBrand $optionsForMasterBrand
    ): void {
        $this->optionsForCoreEntity = $optionsForCoreEntity;
        $this->optionsForMasterBrand = $optionsForMasterBrand;
    }
}
