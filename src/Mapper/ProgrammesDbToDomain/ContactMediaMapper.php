<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\ContactMediaMap;
use BBC\ProgrammesPagesService\Domain\ValueObject\ContactMedia;
use InvalidArgumentException;

class ContactMediaMapper extends AbstractMapper
{
    public function getDomainModel(array $contactsMedia): ContactMediaMap
    {
        $contactMediaMap = new ContactMediaMap();

        foreach ($contactsMedia as $contactMedia) {
            if (!$this->isValidDataToMap($contactMedia)) {
                throw new InvalidArgumentException('Not possible to map contact media');
            }

            $contactMediaMap->addContactMedia(new ContactMedia(
                $contactMedia['detail_type'],
                $contactMedia['detail_value'],
                $contactMedia['detail_freetext']
            ));
        }

        return $contactMediaMap;
    }

    private function isValidDataToMap(array $contact): bool
    {
        return (
            isset($contact['detail_type']) &&
            isset($contact['detail_value']) &&
            isset($contact['detail_freetext'])
        );
    }
}
