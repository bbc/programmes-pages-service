<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="denorm_backfill_processed_time_idx", columns={"processed_time"}),
 *     @ORM\Index(name="denorm_backfill_locked_at_idx", columns={"locked_at"}),
 *     @ORM\Index(name="denorm_backfill_locking_idx", columns={"processed_time","locked","cid"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BackfillRepository")
 *
 * Note that the composite index here is useful to avoid locking. Don't ask. You don't want to know.
 */
class DenormBackfill extends BackfillBase
{
}
