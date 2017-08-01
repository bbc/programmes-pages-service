<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\ORM\EntityManager;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class MonthTest extends AbstractDatabaseTest
{
    public function testGeneratedSqlForMySql()
    {
        $mySQLEntityManager = EntityManager::create(
            [
                'driver' => 'pdo_mysql',
                'platform' => new MySqlPlatform(),
            ],
            $this->getEntityManager()->getConfiguration(),
            $this->getEntityManager()->getEventManager()
        );

        $qText = 'SELECT MONTH(b.startAt) FROM ProgrammesPagesService:Broadcast b';
        $sql = $mySQLEntityManager->createQuery($qText)->getSQL();
        $this->assertEquals('MONTH(b0_.start_at)', $this->extractFirstClause($sql));
    }

    public function testGeneratedSqlForSqlite()
    {
        $qText = 'SELECT MONTH(b.startAt) FROM ProgrammesPagesService:Broadcast b';
        $sql = $this->getEntityManager()->createQuery($qText)->getSQL();
        $this->assertEquals("CAST(strftime('%m', b0_.start_at) AS INTEGER)", $this->extractFirstClause($sql));
    }

    /**
     * @expectedException Doctrine\DBAL\DBALException
     */
    public function testGeneratedSqlForUnsupportedPlatform()
    {
        $mySQLEntityManager = EntityManager::create(
            [
                'driver' => 'pdo_pgsql',
                'platform' => new PostgreSqlPlatform(),
            ],
            $this->getEntityManager()->getConfiguration(),
            $this->getEntityManager()->getEventManager()
        );

        $qText = 'SELECT MONTH(b.startAt) FROM ProgrammesPagesService:Broadcast b';
        $sql = $mySQLEntityManager->createQuery($qText)->getSQL();
    }

    private function extractFirstClause(string $sql)
    {
        $matches = [];
        preg_match('/(?<=SELECT ).*(?= AS sclr_0)/', $sql, $matches);
        return $matches[0] ?? null;
    }
}
