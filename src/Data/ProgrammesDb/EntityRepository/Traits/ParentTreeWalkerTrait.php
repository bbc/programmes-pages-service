<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

trait ParentTreeWalkerTrait
{
    /**
     * Takes an array of entity array representations that have a id, parent and
     * ancestry fields and finds all the ancestors for all the entities and
     * attaches the parents onto the entities.
     * It is also possible to support entities where the 'ancestry' field is several
     * levels deep ($entity['version']['programmeItem']['ancestry']). A 'parent'
     * field will be populated at the same level as that field. To do this set
     * the $keyPath to point to the 'ancestry' field:
     * $keyPath = [''version', 'programmeItem', 'ancestry']testConstructorRequiredArgs
     *
     * @param  array $entities
     * @param callable $ancestryGetter A callable that accepts an array of ids and returns a
     *                  list of entities with those ids
     * @param array $keyPath an array of keys to follow to reach the ancestry source field
     * @return array
     */
    protected function abstractResolveAncestry(
        array $entities,
        callable $ancestryGetter,
        array $keyPath = ['ancestry']
    ) {
        // Build a list of all unique parentIds found in all of the entities
        $listOfAllParentIds = [];
        foreach ($entities as $entity) {

            $ancestry = $this->getFieldFromDepth($entity, $keyPath);

            foreach ($this->getParentIdsFromAncestry($ancestry) as $parentId) {
                $listOfAllParentIds[$parentId] = true;
            }
        }
        $listOfAllParentIds = array_keys($listOfAllParentIds);

        // No parents so do nothing
        if (empty($listOfAllParentIds)) {
            return $entities;
        }
        // Get all entities in the ancestry from the DB.
        $parentEntities = $ancestryGetter($listOfAllParentIds);

        // Update the entities so that their ancestry is hydrated
        foreach ($entities as $i => $entity) {
            $entities[$i] = $this->combineAncestry($entity, $parentEntities, $keyPath);
        }
        return $entities;
    }

    /**
     * Takes an array of entity array representations that contain a field that
     * has contains an array of items that have a id, parent and ancestry fields
     * and finds all the ancestors for all the sub-entities and attaches the
     * parents onto them.
     *
     * For instance a Programme has Categories, which then has an ancestry
     * abstractResolveNestedAncestry($programmes, 'category', $callable)
     * shall populate the category ancestry for all categories that belong to
     * all programmes
     *
     * @param  array $entities
     * @param string $key
     * @param callable $ancestryGetter A callable that accepts an array of ids and returns a
     *                  list of entities with those ids
     * @return array
     */
    protected function abstractResolveNestedAncestry(array $entities, string $key, callable $ancestryGetter)
    {
        // Build a list of all unique parentIds found in all of the entities
        $listOfAllParentIds = [];
        foreach ($entities as $entity) {
            foreach ($entity[$key] ?? [] as $itemWithAncestry) {
                foreach ($this->getParentIdsFromAncestry($itemWithAncestry['ancestry']) as $parentId) {
                    $listOfAllParentIds[$parentId] = true;
                }
            }
        }
        $listOfAllParentIds = array_keys($listOfAllParentIds);

        // No parents so do nothing
        if (empty($listOfAllParentIds)) {
            return $entities;
        }
        // Get all entities in the ancestry from the DB.
        $parentEntities = $ancestryGetter($listOfAllParentIds);

        // Process the entities into an array with their ancestry combined
        foreach ($entities as $i => $entity) {
            // For each category in that programme
            foreach ($entity[$key] ?? [] as $j => $itemWithAncestry) {
                // For each ancestor in the categories list
                $entities[$i][$key][$j] = $this->combineAncestry($itemWithAncestry, $parentEntities);
            }
        }

        return $entities;
    }

    /**
     * Using the potential ancestors as a source, recursively set the parents into place
     *
     * @param $entity
     * @param array $potentialAncestors
     * @param array $keyPath
     * @return array|null
     */
    private function combineAncestry($entity, array $potentialAncestors = [], $keyPath = ['ancestry'])
    {
        // an embargoed ancestor will come through as null
        if (is_null($entity)) {
            return null;
        }

        // The keyPath is the pointer to find the 'ancestry' field.
        // In order to set the results of the ancestry, we'll be setting
        // the 'parent' field, so here we update the pointer to be a 'parent'
        // field at the same level.
        $setterPath = array_slice($keyPath, 0, -1);
        $setterPath[] = 'parent';

        $parentIds = $this->getParentIdsFromAncestry($this->getFieldFromDepth($entity, $keyPath));
        if ($parentIds) {
            $resolvedParent = $this->searchSetForEntityWithId($potentialAncestors, end($parentIds));

            // overwrite the entity with the new one where the parent has
            // been set
            $entity = $this->setDeepKey(
                $entity,
                $this->combineAncestry($resolvedParent, $potentialAncestors),
                $setterPath
            );
        }
        return $entity;
    }

    private function searchSetForEntityWithId(array $resultSet, int $id)
    {
        // TODO we should come up with a way of storing all programmes using
        // their ID as a key, so we can lookup a programme from it's ID in
        // O(1) time, instead of O(n)
        foreach ($resultSet as $programme) {
            if ($programme['id'] == $id) {
                return $programme;
            }
        }
        return null;
    }

    /**
     * Gets the values of a key deep in a nested array. This allows
     * the key to be fetched where the depth is dynamic. Equivalent to
     * $value = $array['level1']['level2']
     * but where we can't hard code how many levels to travel
     * $value = $this->getFieldFromDepth($array, ['level1','level2']);
     *
     * @param $entity
     * @param array $keyPath
     * @return mixed
     */
    private function getFieldFromDepth($entity, array $keyPath)
    {
        $key = array_shift($keyPath);
        if (empty($keyPath)) {
            return $entity[$key];
        }
        return $this->getFieldFromDepth($entity[$key], $keyPath);
    }

    /**
     * Sets the value of a key deep in a nested array. This allows
     * the key to be set where the depth is dynamic, so we can't code
     * the setter. Using
     * $array = $this->setDeepKey($array, 'hello', ['level1', 'level2']);
     * is equivalent to using:
     * $array['level1']['level2] = 'hello';
     *
     * @param $entity
     * @param $valueToSet
     * @param array $keyPath
     * @return mixed
     */
    private function setDeepKey($entity, $valueToSet, array $keyPath = [])
    {
        if (empty($keyPath)) {
            return $valueToSet;
        }
        $key = array_shift($keyPath);
        if (!isset($entity[$key])) {
            $entity[$key] = null;
        }
        $entity[$key] = $this->setDeepKey($entity[$key], $valueToSet, $keyPath);
        return $entity;
    }

    private function getParentIdsFromAncestry(string $ancestry): array
    {
        // $ancestry contains a string of all IDs including the current one with
        // a trailing comma at the end (which makes it an empty item when exploding)
        // Thus for parent ids we want an array of all but the two items (which
        // is the current id and null)
        $ancestors = explode(',', $ancestry, -2);
        return $ancestors ?? [];
    }
}
