<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class Membership
{
    use TimestampableEntity;

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
     * One of coreEntityMember or imageMember must be set. So even though this
     * is nullable, we do want deleting a coreEntity to cascade to delete the
     * memberships that the coreEntity belonged to
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $coreEntityMember;

    /**
     * One of coreEntityMember or imageMember must be set. So even though this
     * is nullable, we do want deleting an image to cascade to delete the
     * memberships that the image belonged to
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $imageMember;

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

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return CoreEntity|Image
     */
    public function getMember()
    {
        return $this->coreEntityMember ?? $this->imageMember;
    }

    /**
     * @return CoreEntity|null
     */
    public function getCoreEntityMember()
    {
        return $this->coreEntityMember;
    }

    /**
     * @return Image|null
     */
    public function getImageMember()
    {
        return $this->imageMember;
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

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition(int $position = null)
    {
        $this->position = $position;
    }

    private function setMemberBatch(
        CoreEntity $coreEntityMember = null,
        Image $imageMember = null
    ) {
        $this->coreEntityMember = $coreEntityMember;
        $this->imageMember = $imageMember;
    }
}
