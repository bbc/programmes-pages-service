<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="contributor_music_brainz_id_idx", columns={"music_brainz_id"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository")
 */
class Contributor
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;

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
     * @var string
     *
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=511, nullable=false)
     */
    private $name = '';

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $musicBrainzId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $presentationName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $givenName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $familyName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=511, nullable=true)
     */
    private $sortName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $nameLanguage;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $disambiguation = '';

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * Used for joins. Cannot be queried, so there is no getter/setter.
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="contributor")
     */
    private $contributions;

    /**
     * @var Thing|null
     *
     * @ORM\ManyToOne(targetEntity="Thing")
     * @JoinColumn(name="thing_id", referencedColumnName="id")
     */
    private $thing;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $wikidataItemId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $imdbUri;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $wikipediaUri;

    public function __construct(string $pid, string $type)
    {
        $this->pid = $pid;
        $this->type = $type;
        $this->contributions = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getMusicBrainzId(): ?string
    {
        return $this->musicBrainzId;
    }

    public function setMusicBrainzId(?string $musicBrainzId)
    {
        $this->musicBrainzId = $musicBrainzId;
    }

    public function getPresentationName(): ?string
    {
        return $this->presentationName;
    }

    public function setPresentationName(?string $presentationName)
    {
        $this->presentationName = $presentationName;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName)
    {
        $this->givenName = $givenName;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName)
    {
        $this->familyName = $familyName;
    }

    public function getSortName(): ?string
    {
        return $this->sortName;
    }

    public function setSortName(?string $sortName)
    {
        $this->sortName = $sortName;
    }

    public function getNameLanguage(): ?string
    {
        return $this->nameLanguage;
    }

    public function setNameLanguage(?string $nameLanguage)
    {
        $this->nameLanguage = $nameLanguage;
    }

    public function getDisambiguation(): ?string
    {
        return $this->disambiguation;
    }

    public function setDisambiguation(?string $disambiguation)
    {
        $this->disambiguation = $disambiguation;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender)
    {
        $this->gender = $gender;
    }

    public function getThing(): ?Thing
    {
        return $this->thing;
    }

    public function setThing(?Thing $thing)
    {
        $this->thing = $thing;
    }

    public function getWikidataItemId(): ?string
    {
        return $this->wikidataItemId;
    }

    public function setWikidataItemId(?string $wikidataItemId)
    {
        $this->wikidataItemId = $wikidataItemId;
    }

    public function getImdbUri(): ?string
    {
        return $this->imdbUri;
    }

    public function setImdbUri(?string $imdbUri)
    {
        $this->imdbUri = $imdbUri;
    }

    public function getWikipediaUri(): ?string
    {
        return $this->wikipediaUri;
    }

    public function setWikipediaUri(?string $wikipediaUri)
    {
        $this->wikipediaUri = $wikipediaUri;
    }
}
