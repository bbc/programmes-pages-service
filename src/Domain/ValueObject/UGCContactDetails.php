<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

class UGCContactDetails extends ContactDetails
{
    /**
     * @var bool
     */
    protected $topNav;

    /**
     * @var string
     */
    protected $title;

    public function __construct($data)
    {
        parent::__construct($data['type'], $data['value'], $data['freetext']);
        $this->topNav = ($data['top_nav'] ?? false) === true;
        $this->title = $data['title'] ?? '';
    }

    public function isInTopNav() : bool
    {
        return $this->topNav;
    }

    public function getTitle() : string
    {
        return $this->title;
    }
}
