<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

trait ParentTreeWalkerTrait
{
    /**
     * Takes an array of entity array representations that have a id, parent and
     * ancestry fields and finds all the ancestors for all the entities and
     * attaches the parents onto the entities.
     *
     * @param  array    $entities
     * @param  callable A callable that accepts an array of ids and returns a
     *                  list of entities with those ids
     * @return array
     */
    protected function abstractResolveAncestry(array $entities, callable $ancestryGetter)
    {
        // Build a list of all unique parentIds found in all of the entities
        $listOfAllParentIds = [];
        foreach ($entities as $entity) {
            foreach ($this->getParentIdsFromAncestry($entity['ancestry']) as $parentId) {
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
            $entities[$i] = $this->combineAncestry($entity, $parentEntities);
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
     * @param  array    $entities
     * @param  callable A callable that accepts an array of ids and returns a
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
     * @return array|null
     */
    private function combineAncestry($entity, array $potentialAncestors = [])
    {
        // an embargoed ancestor will come through as null
        if (is_null($entity)) {
            return null;
        }

        $parentIds = $this->getParentIdsFromAncestry($entity['ancestry']);
        if ($parentIds) {
            $resolvedParent = $this->searchSetForEntityWithId($potentialAncestors, end($parentIds));
            $entity['parent'] = $this->combineAncestry($resolvedParent, $potentialAncestors);
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
