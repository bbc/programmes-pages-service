<?php
declare(strict_types = 1);

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *     @ORM\Index(name="ref_isite_dependent_file_id_idx", columns={"file_id"}),
 *     @ORM\Index(name="ref_isite_dependent_guid_idx", columns={"guid"}),
 * })
 */
class RefIsiteDependentBlock
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
     * @var RefIsiteOptions
     *
     * @ORM\ManyToOne(targetEntity="RefIsiteOptions")
     * @ORM\JoinColumn(nullable=false, name="ref_isite_options_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $refIsiteOptions;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $fileId;

    /**
     * @var null|string
     *
     * Guid is nullable because when an options form has an unpublished dependent block,
     * the only thing we get for it is the file id when we request it through isite2-content-reader.
     * As we still want to know the dependent blocks for a option, we have to use file id as the primary
     * identifier. This field is here as a 'nice-to-have', in case we need it in the future.
     *
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $guid;

    public function __construct(RefIsiteOptions $refIsiteOptions, string $fileId)
    {
        $this->refIsiteOptions = $refIsiteOptions;
        $this->fileId = $fileId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRefIsiteOptions(): RefIsiteOptions
    {
        return $this->refIsiteOptions;
    }

    public function setRefIsiteOptions(RefIsiteOptions $refIsiteOptions)
    {
        $this->refIsiteOptions = $refIsiteOptions;
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function setFileId(string $fileId)
    {
        $this->fileId = $fileId;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid)
    {
        $this->guid = $guid;
    }
}
