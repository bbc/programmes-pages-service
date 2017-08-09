<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class Image implements PromotableInterface
{
    /** @var Pid */
    private $pid;

    /** @var string */
    private $title;

    /** @var string */
    private $shortSynopsis;

    /** @var string */
    protected $longestSynopsis;

    /** @var string|null */
    private $type;

    /** @var string */
    private $filename;

    public function __construct(
        Pid $pid,
        string $title,
        string $shortSynopsis,
        string $longestSynopsis,
        string $type,
        string $extension
    ) {
        $this->pid = $pid;
        $this->title = $title;
        $this->shortSynopsis = $shortSynopsis;
        $this->longestSynopsis = $longestSynopsis;
        $this->type = $type;
        $this->filename = (string) $pid . '.' . $extension;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function getLongestSynopsis(): string
    {
        return $this->longestSynopsis;
    }

    public function getUrl($width, $height = 'n'): string
    {
        $recipe = $width . 'x' . $height;
        return 'https://ichef.bbci.co.uk/images/ic/' . $recipe . '/' . $this->filename;
    }

    public function isLetterBox(): bool
    {
        return ($this->type == 'letterbox');
    }

    public function getType(): string
    {
        return $this->type;
    }
}
