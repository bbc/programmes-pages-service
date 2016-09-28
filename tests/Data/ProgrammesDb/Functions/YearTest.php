<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class YearTest extends AbstractDatabaseTest
{
    public function testGeneratedSqlForMySql()
    {
        $mySQLEntityManager = \Doctrine\ORM\EntityManager::create(
            [
                'driver' => 'pdo_mysql',
                'platform' => new \Doctrine\DBAL\Platforms\MySqlPlatform(),
            ],
            $this->getEntityManager()->getConfiguration(),
            $this->getEntityManager()->getEventManager()
        );

        $qText = 'SELECT YEAR(b.startAt) FROM ProgrammesPagesService:Broadcast b';
        $sql = $mySQLEntityManager->createQuery($qText)->getSql();
        $this->assertEquals('YEAR(b0_.start_at)', $this->extractFirstClause($sql));
    }

    public function testGeneratedSqlForSqlite()
    {
        $qText = 'SELECT YEAR(b.startAt) FROM ProgrammesPagesService:Broadcast b';
        $sql = $this->getEntityManager()->createQuery($qText)->getSql();
        $this->assertEquals("CAST(strftime('%Y', b0_.start_at) AS INTEGER)", $this->extractFirstClause($sql));
    }

    /**
     * @expectedException Doctrine\DBAL\DBALException
     */
    public function testGeneratedSqlForUnsupportedPlatform()
    {
        $mySQLEntityManager = \Doctrine\ORM\EntityManager::create(
            [
                'driver' => 'pdo_pgsql',
                'platform' => new \Doctrine\DBAL\Platforms\PostgreSqlPlatform(),
            ],
            $this->getEntityManager()->getConfiguration(),
            $this->getEntityManager()->getEventManager()
        );

        $qText = 'SELECT YEAR(b.startAt) FROM ProgrammesPagesService:Broadcast b';
        $sql = $mySQLEntityManager->createQuery($qText)->getSql();
    }

    private function extractFirstClause(string $sql)
    {
        $matches = [];
        preg_match('/(?<=SELECT ).*(?= AS sclr_0)/', $sql, $matches);
        return array_key_exists(0, $matches) ? $matches[0] : null;
    }
}
