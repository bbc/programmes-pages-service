<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

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
     * @var string|null
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
     * @ORM\Column(type="string", length=255, nullable=true)
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

    public function __construct(string $pid, string $type)
    {
        $this->pid = $pid;
        $this->type = $type;
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

    /**
     * @return string|null
     */
    public function getMusicBrainzId()
    {
        return $this->musicBrainzId;
    }

    public function setMusicBrainzId(string $musicBrainzId = null)
    {
        $this->musicBrainzId = $musicBrainzId;
    }

    /**
     * @return string|null
     */
    public function getPresentationName()
    {
        return $this->presentationName;
    }

    public function setPresentationName(string $presentationName = null)
    {
        $this->presentationName = $presentationName;
    }

    /**
     * @return string|null
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName = null)
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string|null
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName = null)
    {
        $this->familyName = $familyName;
    }

    /**
     * @return string|null
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    public function setSortName(string $sortName = null)
    {
        $this->sortName = $sortName;
    }

    /**
     * @return string|null
     */
    public function getNameLanguage()
    {
        return $this->nameLanguage;
    }

    public function setNameLanguage(string $nameLanguage = null)
    {
        $this->nameLanguage = $nameLanguage;
    }

    /**
     * @return string|null
     */
    public function getDisambiguation()
    {
        return $this->disambiguation;
    }

    public function setDisambiguation(string $disambiguation = null)
    {
        $this->disambiguation = $disambiguation;
    }

    /**
     * @return string|null
     */
    public function getGender()
    {
        return $this->gender;
    }

    public function setGender(string $gender = null)
    {
        $this->gender = $gender;
    }
}
