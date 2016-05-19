<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DateTime;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class Promotion
{
    use TimestampableEntity;
    use Traits\SynopsesTrait;

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
     * @ORM\Column(type="string", length=15, nullable=false, unique=true)
     */
    private $pid;

    /**
     * One of promotedCoreEntity or promotedImage must be set. So even though
     * this is nullable, we do want deleting a coreEntity to cascade to delete
     * the promotions attached to the coreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $promotedCoreEntity;

    /**
    * One of promotedCoreEntity or promotedImage must be set. So even though
     * this is nullable, we do want deleting a coreEntity to cascade to delete
     * the promotions attached to the image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $promotedImage;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $endDate;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    private $weighting;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $promotedFor;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $uri = '';

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $cascadesToDescendants = false;

    /**
     * @param string $pid
     * @param CoreEntity|Image $promotedItem
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $weighting
     */
    public function __construct(
        string $pid,
        $promotedItem,
        DateTime $startDate,
        DateTime $endDate,
        int $weighting
    ) {
        $this->pid = $pid;
        $this->setPromotedItem($promotedItem);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->weighting = $weighting;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return CoreEntity|Image
     */
    public function getPromotedItem()
    {
        return $this->promotedCoreEntity ?? $this->promotedImage;
    }

    /**
     * @return CoreEntity|null
     */
    public function getPromotedCoreEntity()
    {
        return $this->promotedCoreEntity;
    }

    /**
     * @return Image|null
     */
    public function getPromotedImage()
    {
        return $this->promotedImage;
    }

    /**
     * @param CoreEntity|Image $item
     */
    public function setPromotedItem($item)
    {
        if ($item instanceof CoreEntity) {
            $this->setPromotedItemBatch($item, null);
        } elseif ($item instanceof Image) {
            $this->setPromotedItemBatch(null, $item);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected setPromotedItem() to be called with an an instance of "%s" or "%s". Found instance of "%s"',
                CoreEntity::CLASS,
                Image::CLASS,
                (is_object($item) ? get_class($item) : gettype($item))
            ));
        }
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    public function getWeighting(): int
    {
        return $this->weighting;
    }

    public function setWeighting(int $weighting)
    {
        $this->weighting = $weighting;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return string|null
     */
    public function getPromotedFor()
    {
        return $this->promotedFor;
    }

    public function setPromotedFor(string $promotedFor = null)
    {
        $this->promotedFor = $promotedFor;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }


    public function getCascadesToDescendants(): bool
    {
        return $this->cascadesToDescendants;
    }

    public function setCascadesToDescendants(bool $cascadesToDescendants)
    {
        $this->cascadesToDescendants = $cascadesToDescendants;
    }

    private function setPromotedItemBatch(
        CoreEntity $promotedCoreEntity = null,
        Image $promotedImage = null
    ) {
        $this->promotedCoreEntity = $promotedCoreEntity;
        $this->promotedImage = $promotedImage;
    }
}
