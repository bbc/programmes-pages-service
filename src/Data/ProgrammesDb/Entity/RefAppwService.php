<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 */
class RefAppwService
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
     * @ORM\Column(type="string", length=220, nullable=false, unique=true)
     */
    private $sid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $mediaAssetProfile;

    public function __construct(string $sid, ?string $mediaAssetProfile)
    {
        $this->sid = $sid;
        $this->mediaAssetProfile = $mediaAssetProfile;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSid(): string
    {
        return $this->sid;
    }

    public function setSid(string $sid): void
    {
        $this->sid = $sid;
    }

    public function getMediaAssetProfile(): string
    {
        return $this->mediaAssetProfile;
    }

    public function setMediaAssetProfile(string $mediaAssetProfile): void
    {
        $this->mediaAssetProfile = $mediaAssetProfile;
    }
}
