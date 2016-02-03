<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class EmbargoedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $embargoedField = 'isEmbargoed';

        if (!$targetEntity->hasField($embargoedField)) {
            return "";
        }

        return $targetTableAlias . '.' . $targetEntity->getColumnName($embargoedField) . '= 0';
    }
}
