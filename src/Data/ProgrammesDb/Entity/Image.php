<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 */
class Image
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
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;

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
     * @ORM\Column(type="string", nullable=false)
     */

    private $type = 'standard';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $extension = 'jpg';


    public function __construct(string $pid, string $title)
    {
        $this->pid = $pid;
        $this->title = $title;
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
        return $this->longSynopsis;
    }

    public function setLongSynopsis(string $longSynopsis)
    {
        $this->longSynopsis = $longSynopsis;
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
