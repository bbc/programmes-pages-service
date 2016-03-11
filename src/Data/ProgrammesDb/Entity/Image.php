<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Image
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $shortSynopsis = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $mediumSynopsis = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $longSynopsis = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $longestSynopsis = '';


    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */

    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $extension = 'jpg';


    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    public function setPid(string $pid)
    {
        $this->pid = $pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function setShortSynopsis(string $shortSynopsis)
    {
        $this->shortSynopsis = $shortSynopsis;
    }

    public function getMediumSynopsis(): string
    {
        return $this->mediumSynopsis;
    }

    public function setMediumSynopsis(string $mediumSynopsis)
    {
        $this->mediumSynopsis = $mediumSynopsis;
    }

    public function getLongSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function setLongSynopsis(string $longSynopsis)
    {
        $this->longSynopsis = $longSynopsis;
    }

    public function getLongestSynopsis(): string
    {
        return $this->longestSynopsis;
    }

    public function setLongestSynopsis(string $longestSynopsis)
    {
        $this->longestSynopsis = $longestSynopsis;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type = null)
    {
        $this->type = $type;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension)
    {
        $this->extension = $extension;
    }
}
