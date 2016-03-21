<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ImageRepository extends EntityRepository
{

    public function findAllImages($pid, $type = 'standard')
    {
        $qText = <<<QUERY
SELECT image FROM ProgrammesPagesService:Image image
JOIN ProgrammesPagesService:RefRelationship relationship WITH relationship.objectPid=image.pid 
JOIN relationship.relationshipType relationship_type
JOIN ProgrammesPagesService:CoreEntity core_entity WITH core_entity.pid = relationship.subjectPid
WHERE core_entity.pid=:entityPid AND relationship_type.name='is_image_for' AND image.type=:type
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setParameter('entityPid', $pid)
            ->setParameter('type', $type);

        return $q->getResult();
    }

}
