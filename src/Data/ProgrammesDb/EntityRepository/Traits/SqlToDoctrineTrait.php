<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

trait SqlToDoctrineTrait
{
    protected function sqlResultsToDoctrineResults($results)
    {
        foreach ($results as $i => $result) {
            $results[$i] = $this->sqlResultToDoctrineResult($result);
        }
        return $results;
    }

    protected function sqlResultToDoctrineResult($result)
    {
        foreach ($result as $key => $value) {
            $newKey = $this->sqlKeyToDoctrineKey($key);
            if ($key !== $newKey) {
                $result[$newKey] = $value;
                unset($result[$key]);
            }
        }
        return $result;
    }

    protected function sqlKeyToDoctrineKey($key)
    {
        return lcfirst(
            str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))
        );
    }
}