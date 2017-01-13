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
    private $optionsToCoreEntity;

    /**
     * @var Network
     *
     * @ORM\ManyToOne(targetEntity="Network")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $optionsToNetwork;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $adminOptions;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
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
     * @param CoreEntity|Network $optionsTo
     */
    public function __construct($optionsTo)
    {
        $this->setOptionsTo($optionsTo);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return CoreEntity|Network
     */
    public function getOptionsTo()
    {
        return $this->optionsToCoreEntity ?? $this->optionsToNetwork;
    }

    public function getOptionsToCoreEntity(): ?CoreEntity
    {
        return $this->optionsToCoreEntity;
    }

    public function getOptionsToNetwork(): ?Network
    {
        return $this->optionsToNetwork;
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
     * @param CoreEntity|Network $item
     */
    public function setOptionsTo($item)
    {
        if ($item instanceof CoreEntity) {
            $this->setOptionsToBatch($item, null);
        } elseif ($item instanceof Network) {
            $this->setOptionsToBatch(null, $item);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected setOptionsTo() to be called with an an instance of "%s", "%s" or "%s". Found instance of "%s"',
                CoreEntity::CLASS,
                Network::CLASS,
                (is_object($item) ? get_class($item) : gettype($item))
            ));
        }
    }

    private function setOptionsToBatch(
        ?CoreEntity $optionsToCoreEntity,
        ?Network $optionsToNetwork
    ): void {
        $this->optionsToCoreEntity = $optionsToCoreEntity;
        $this->optionsToNetwork = $optionsToNetwork;
    }
}
