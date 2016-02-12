<?php

namespace Tests\BBC\ProgrammesPagesService;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

abstract class AbstractDatabaseTest extends PHPUnit_Framework_TestCase
{
    const FIXTURES_PATH = 'Tests\BBC\ProgrammesPagesService\DataFixtures\ORM\\';

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
}
