<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class Contribution
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
     * @var string
     *
     * @ORM\Column(type="string", length=15, nullable=false, unique=true)
     */
    private $pid;

    /**
     * @ORM\ManyToOne(targetEntity="Contributor")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $contributor;

    /**
     * @ORM\ManyToOne(targetEntity="CreditRole")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $creditRole;

    /**
     * One of Programme, Segment or Version must be set. So even though, this is
     * nullable, we do want deleting a programme to cascade to delete the
     * contributions made to the programme
     *
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $programme;

    /**
     * One of Programme, Segment or Version must be set. So even though, this is
     * nullable, we do want deleting a segment to cascade to delete the
     * contributions made to the segment
     *
     * @ORM\ManyToOne(targetEntity="Segment")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $segment;

    /**
     * One of Programme, Segment or Version must be set. So even though, this is
     * nullable, we do want deleting a version to cascade to delete the
     * contributions made to the version
     *
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $version;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $position;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $characterName;

    /**
     * @param string $pid
     * @param Contributor $contributor
     * @param CreditRole $creditRole
     * @param Programme|Segment|Version $contributionTo
     */
    public function __construct(
        string $pid,
        Contributor $contributor,
        CreditRole $creditRole,
        $contributionTo
    ) {
        $this->pid = $pid;
        $this->contributor = $contributor;
        $this->creditRole = $creditRole;
        $this->setContributionTo($contributionTo);
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

    public function getContributor(): Contributor
    {
        return $this->contributor;
    }

    public function setContributor(Contributor $contributor)
    {
        $this->contributor = $contributor;
    }

    public function getCreditRole(): CreditRole
    {
        return $this->creditRole;
    }

    public function setCreditRole(CreditRole $creditRole)
    {
        $this->creditRole = $creditRole;
    }

    /**
     * @return Programme|Segment|Version
     */
    public function getContributionTo()
    {
        return $this->programme ?? $this->segment ?? $this->version;
    }

    /**
     * @return Programme|null
     */
    public function getProgramme()
    {
        return $this->programme;
    }

    /**
     * @return Segment|null
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @return Version|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param Programme|Segment|Version $item
     */
    public function setContributionTo($item)
    {
        if ($item instanceof Programme) {
            $this->setContributionToBatch($item, null, null);
        } elseif ($item instanceof Segment) {
            $this->setContributionToBatch(null, $item, null);
        } elseif ($item instanceof Version) {
            $this->setContributionToBatch(null, null, $item);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected setContributionTo() to be called with an an instance of "%s", "%s" or "%s". Found instance of "%s"',
                Programme::CLASS,
                Segment::CLASS,
                Version::CLASS,
                (is_object($item) ? get_class($item) : gettype($item))
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

    /**
     * @return string|null
     */
    public function getCharacterName()
    {
        return $this->characterName;
    }

    public function setCharacterName(string $characterName = null)
    {
        $this->characterName = $characterName;
    }

    private function setContributionToBatch(
        Programme $programme = null,
        Segment $segment = null,
        Version $version = null
    ) {
        $this->programme = $programme;
        $this->segment = $segment;
        $this->version = $version;
    }
}
