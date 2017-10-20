<?php

namespace BBC\ProgrammesPagesService\Domain\Map;

use BBC\ProgrammesPagesService\Domain\Enumeration\ContactMediaEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\ContactMedia;
use InvalidArgumentException;

class ContactMediaMap
{
    /**
     * Example:
     *  [
     *      'email'    => [<ContactMedia>, <ContactMedia>, <ContactMedia>],
     *      'facebook' => [<ContactMedia>],
     *  ]
     *
     * @var mixed[]
     */
    private $contactsByMediaMap = [];

    public function getNamesOfUsedMedia()
    {
        return array_keys($this->contactsByMediaMap);
    }

    public function getContactsByMedia(string $contactMediaType): array
    {
        if (!in_array($contactMediaType, ContactMediaEnum::validValues())) {
            throw new InvalidArgumentException('Trying to get a contact detail with unknown type');
        }

        return $this->contactsByMediaMap[$contactMediaType] ?? [];
    }

    public function addContactMedia(ContactMedia $contactMedia): self
    {
        // validate type of contact
        if (!in_array($contactMedia->getType(), ContactMediaEnum::validValues())) {
            throw new InvalidArgumentException('Trying to create an invalid type of contact media');
        }

        $this->contactsByMediaMap[$contactMedia->getType()][] = $contactMedia;

        return $this;
    }
}
