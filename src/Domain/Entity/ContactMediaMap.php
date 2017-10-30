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
     * @var ContactMedia[][]
     */
    private $contactsMapByMedia = [];

    /**
     * Example: ['reddit', 'email']
     *
     * @return string[]
     */
    public function getNamesOfUsedMedia(): array
    {
        return array_keys($this->contactsMapByMedia);
    }

    /**
     * @return ContactMedia[]
     */
    public function getContactsByMedia(string $contactMediaType): array
    {
        $validsMedia = ContactMediaEnum::VALID_MEDIA;

        if (!isset($validsMedia[$contactMediaType])) {
            throw new InvalidArgumentException('Trying to get a contact detail with unknown type');
        }

        return $this->contactsMapByMedia[$contactMediaType] ?? [];
    }

    public function addContactMedia(ContactMedia $contactMedia): self
    {
        $validsMedia = ContactMediaEnum::VALID_MEDIA;

        // we don't want to throw any error when trying to add invalid contact types.
        // so for invalid types we just dont add them to the map
        if (isset($validsMedia[$contactMedia->getType()])) {
            $this->contactsMapByMedia[$contactMedia->getType()][] = $contactMedia;
        }

        return $this;
    }
}
