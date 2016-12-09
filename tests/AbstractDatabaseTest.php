<?php

namespace Tests\BBC\ProgrammesPagesService;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Logging\DebugStack;
use PHPUnit_Framework_TestCase;

abstract class AbstractDatabaseTest extends PHPUnit_Framework_TestCase
{
    const FIXTURES_PATH = 'Tests\BBC\ProgrammesPagesService\DataFixtures\ORM\\';
    const EMBARGOED_FILTER = 'embargoed_filter';

    private $ormExecutor;
    static private $entityManager;

    protected function loadFixtures(array $fixtureNames)
    {
        $loader = new Loader();

        foreach ($fixtureNames as $fixtureName) {
            $fixtureClassName = self::FIXTURES_PATH . $fixtureName;
            $loader->addFixture(new $fixtureClassName());
        }

        $this->getOrmExecutor()->execute($loader->getFixtures());

        // Reset the SQL Logger so we only care about Queries made after the
        // fixtures have loaded
        $this->resetDbQueryLogger();
    }

    protected function getRepository($name)
    {
        return $this->getEntityManager()->getRepository($name);
    }

    protected function getDbQueries()
    {
        return $this->getEntityManager()->getConfiguration()->getSQLLogger()->queries;
    }

    protected function resetDbQueryLogger()
    {
        $this->getEntityManager()->getConfiguration()->setSQLLogger(new DebugStack());
    }

    protected function getOrmExecutor()
    {
        if (is_null($this->ormExecutor)) {
            $this->ormExecutor = new ORMExecutor($this->getEntityManager(), new ORMPurger());
        }

        return $this->ormExecutor;
    }

    protected function getEntityManager()
    {
        if (is_null(self::$entityManager)) {
            self::$entityManager = require __DIR__ . '/doctrine-bootstrap.php';
        }

        return self::$entityManager;
    }

    protected function enableEmbargoedFilter(): bool
    {
        $filters = $this->getEntityManager()->getFilters();
        $previouslyEnabled = $filters->isEnabled(self::EMBARGOED_FILTER);

        if (!$previouslyEnabled) {
            $filters->enable(self::EMBARGOED_FILTER);
        }

        return $previouslyEnabled;
    }

    protected function disableEmbargoedFilter()
    {
        $filters = $this->getEntityManager()->getFilters();
        $previouslyEnabled = $filters->isEnabled(self::EMBARGOED_FILTER);

        if ($previouslyEnabled) {
            $filters->disable(self::EMBARGOED_FILTER);
        }

        return $previouslyEnabled;
    }

    /**
     * In order to do some tests sometimes you need to know exactly
     * what the database ID of the fixtures are. This can usually be
     * controlled with fixtures, but new fixtures may cause updates
     * to be needed. However, finding the ID from the database would
     * result in an additional SQL query, adding to the total query count.
     * The tests count queries to ensure they are kept under control.
     * This method therefore excludes the ID fetch in the test itself
     * from the overall query count. This method will also bypass the
     * embargo filter, so will always fetch what was requested.
     *
*@param       $identifier
     * @param $entityType
     * @param $usePipId
     *
*@return int
     */
    protected function getDbIdFromPersistentIdentifier(string $identifier, string $entityType, bool $usePipId = false): int
    {
        return $this->getColumnValueFromPid($identifier, $entityType, 'Id', $usePipId);
    }

    /**
     * Shortcut method for getDbIdFromPid (as many tests based on CoreEntity)
     * @param string $pid
     * @return int
     */
    protected function getCoreEntityDbId(string $pid): int
    {
        return $this->getColumnValueFromPid($pid, 'CoreEntity', 'Id');
    }

    protected function getAncestryFromPersistentIdentifier(
        string $identifier,
        string $entityType,
        bool $usePipId = false
    ): array {
        $ancestryString = $this->getColumnValueFromPid($identifier, $entityType, 'Ancestry', $usePipId);

        // $ancestryString contains a string of all IDs including the current
        // one with a trailing comma at the end (which is an empty item when exploding).
        // Thus we want an array of all but the final item (which is null)
        $ancestry = explode(',', $ancestryString, -1) ?? [];
        return array_map(function ($a) {
            return (int) $a;
        }, $ancestry);
    }

    private function getColumnValueFromPid(string $pid, string $entityType, string $columnName, bool $usePipId = false)
    {
        // Disable the logger for this call as we don't want to count it
        $this->getEntityManager()->getConfiguration()->getSQLLogger()->enabled = false;

        // Disable the embargo filter for this call
        $embargoFilterWasEnabled = $this->disableEmbargoedFilter();

        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:' . $entityType);

        // findOneByX is a magic bit of Doctrine to access property X.
        // Therefore all Entities with a 'pid' or 'pipId' property will have the
        // findOneByPid method.
        if($usePipId) {
            $id = $repo->findOneByPipId($pid)->{'get' . $columnName}();
        }
        else {
            $id = $repo->findOneByPid($pid)->{'get' . $columnName}();
        }

        // Return the embargo filter to it's prior state
        if ($embargoFilterWasEnabled) {
            $this->enableEmbargoedFilter();
        }

        // Re-enable the SQL logger
        $this->getEntityManager()->getConfiguration()->getSQLLogger()->enabled = true;

        return $id;
    }
}
