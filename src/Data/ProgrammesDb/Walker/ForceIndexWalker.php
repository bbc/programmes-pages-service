<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker;

use Doctrine\ORM\Query\SqlWalker;

/**
 * Class ForceIndexWalker
 *
 * This is some extremely nasty dark magic to force doctrine to implement a FORCE INDEX
 * clause for MySQL. I'm using this for the backfill. I wouldn't really recommend using
 * it in full production
 *
 * @package BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker
 */
class ForceIndexWalker extends SqlWalker
{
    const HINT_USE_INDEX = 'ForceIndexWalker.ForceIndex';

    public function walkFromClause($fromClause)
    {
        $result = parent::walkFromClause($fromClause);
        if ($index = $this->getQuery()->getHint(self::HINT_USE_INDEX)) {
            $result = preg_replace('#(\bFROM\s*\w+\s*\w+)#', '\1 FORCE INDEX (' . $index . ')', $result);
        }
        return $result;
    }
}
