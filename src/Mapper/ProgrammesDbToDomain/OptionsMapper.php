<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;

class OptionsMapper extends AbstractMapper
{
    public function getDomainModel(array $entity, array ...$parentEntities)
    {
        // $parentEntities must start from the bottom of the hierarchy

        // first generate the base options
        // where we don't care about the cascade
        // array_map will maintain the keys
        $options = array_map(function ($data) {
            return $data['value'];
        }, $entity);

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
