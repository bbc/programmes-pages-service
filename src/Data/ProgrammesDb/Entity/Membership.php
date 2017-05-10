<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class Membership
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;

    /**
     * @var int
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
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $group;

    /**
     * One of memberCoreEntity or memberImage must be set. So even though this
     * is nullable, we do want deleting a coreEntity to cascade to delete the
     * memberships that the coreEntity belonged to
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $memberCoreEntity;

    /**
     * One of memberCoreEntity or memberImage must be set. So even though this
     * is nullable, we do want deleting an image to cascade to delete the
     * memberships that the image belonged to
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $memberImage;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @param string $pid
     * @param Group $group
     * @param CoreEntity|Image $member
     */
    public function __construct(
        string $pid,
        Group $group,
        $member
    ) {
        $this->pid = $pid;
        $this->group = $group;
        $this->setMember($member);
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

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    /**
     * @return CoreEntity|Image
     */
    public function getMember()
    {
        return $this->memberCoreEntity ?? $this->memberImage;
    }

    public function getMemberCoreEntity(): ?CoreEntity
    {
        return $this->memberCoreEntity;
    }

    public function getMemberImage(): ?Image
    {
        return $this->memberImage;
    }

    /**
     * @param CoreEntity|Image $member
     */
    public function setMember($member)
    {
        if ($member instanceof CoreEntity) {
            $this->setMemberBatch($member, null);
        } elseif ($member instanceof Image) {
            $this->setMemberBatch(null, $member);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected setMember() to be called with an an instance of "%s" or "%s". Found instance of "%s"',
                CoreEntity::CLASS,
                Image::CLASS,
                (is_object($member) ? get_class($member) : gettype($member))
            ));
        }
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    private function setMemberBatch(?CoreEntity $memberCoreEntity, ?Image $memberImage): void
    {
        $this->memberCoreEntity = $memberCoreEntity;
        $this->memberImage = $memberImage;
    }
}
