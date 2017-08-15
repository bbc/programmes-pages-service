<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Table(indexes={
 *   @ORM\Index(name="promotion_context_idx", columns={"context_id", "is_active", "start_date", "end_date"}),
 *  })
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository")
 */
class Promotion
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;
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
     * One of promotionOfCoreEntity or promotionOfImage must be set. So even though
     * this is nullable, we do want deleting a coreEntity to cascade to delete
     * the promotions attached to the coreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $promotionOfCoreEntity;

    /**
    * One of promotionOfCoreEntity or promotionOfImage must be set. So even though
     * this is nullable, we do want deleting a coreEntity to cascade to delete
     * the promotions attached to the image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $promotionOfImage;

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
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $context;

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
     * @param CoreEntity|Image $promotionOf
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $weighting
     */
    public function __construct(
        string $pid,
        $promotionOf,
        DateTime $startDate,
        DateTime $endDate,
        int $weighting
    ) {
        $this->pid = $pid;
        $this->setPromotionOf($promotionOf);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->weighting = $weighting;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * @return CoreEntity|Image
     */
    public function getPromotionOf()
    {
        return $this->promotionOfCoreEntity ?? $this->promotionOfImage;
    }

    public function getPromotionOfCoreEntity(): ?CoreEntity
    {
        return $this->promotionOfCoreEntity;
    }

    public function getPromotionOfImage(): ?Image
    {
        return $this->promotionOfImage;
    }

    /**
     * @param CoreEntity|Image $item
     */
    public function setPromotionOf($item)
    {
        if ($item instanceof CoreEntity) {
            $this->setPromotionOfBatch($item, null);
        } elseif ($item instanceof Image) {
            $this->setPromotionOfBatch(null, $item);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected setPromotionOf() to be called with an an instance of "%s" or "%s". Found instance of "%s"',
                CoreEntity::class,
                Image::class,
                (is_object($item) ? get_class($item) : gettype($item))
            ));
        }
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getWeighting(): int
    {
        return $this->weighting;
    }

    public function setWeighting(int $weighting): void
    {
        $this->weighting = $weighting;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getContext(): ?CoreEntity
    {
        return $this->context;
    }

    public function setContext(?CoreEntity $context): void
    {
        $this->context = $context;
    }

    public function getPromotedFor(): ?string
    {
        return $this->promotedFor;
    }

    public function setPromotedFor(?string $promotedFor): void
    {
        $this->promotedFor = $promotedFor;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }


    public function getCascadesToDescendants(): bool
    {
        return $this->cascadesToDescendants;
    }

    public function setCascadesToDescendants(bool $cascadesToDescendants): void
    {
        $this->cascadesToDescendants = $cascadesToDescendants;
    }

    private function setPromotionOfBatch(?CoreEntity $promotionOfCoreEntity, ?Image $promotionOfImage): void
    {
        $this->promotionOfCoreEntity = $promotionOfCoreEntity;
        $this->promotionOfImage = $promotionOfImage;
    }
}
