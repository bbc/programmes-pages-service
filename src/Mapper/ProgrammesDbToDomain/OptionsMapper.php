<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;

class OptionsMapper extends AbstractMapper
{
    public function getDomainModel(array $entity, array ...$parentEntities)
    {
        // $parentEntities must start from the bottom of the hierarchy

        $options = [];
        // first generate the base options
        // where we don't care about the cascade
        foreach ($entity as $key => $data) {
            $options[$key] = $data['value'];
        }

        // now loop through parents and apply the data
        // ONLY if the key is allowed to cascade AND
        // the key doesn't already exist lower down
        foreach ($parentEntities as $entity) {
            foreach ($entity as $key => $data) {
                if (!isset($options[$key]) &&
                    $data['cascades']
                ) {
                    $options[$key] = $data['value'];
                }
            }
        }

        return new Options($options);
    }
}
