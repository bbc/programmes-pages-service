<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

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
    private $contactsMapByMedia = [];

    /**
     * @return string[]     Example: ['reddit', 'email']
     */
    public function getNamesOfUsedMedia(): array
    {
        return array_keys($this->contactsMapByMedia);
    }

    /**
     * @return ContactMedia[] | []
     */
    public function getContactsByMedia(string $contactMediaType): array
    {
        if (!in_array($contactMediaType, ContactMediaEnum::validMedia())) {
            throw new InvalidArgumentException('Trying to get a contact detail with unknown type');
        }

        return $this->contactsMapByMedia[$contactMediaType] ?? [];
    }

    public function addContactMedia(ContactMedia $contactMedia): self
    {
        if (!in_array($contactMedia->getType(), ContactMediaEnum::validMedia())) {
            throw new InvalidArgumentException('Trying to create an invalid type of contact media');
        }

        $this->contactsMapByMedia[$contactMedia->getType()][] = $contactMedia;

        return $this;
    }
}
