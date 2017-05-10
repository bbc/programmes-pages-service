<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class MatchAgainstTest extends AbstractDatabaseTest
{
    /**
     * @dataProvider matchAgainstDataProvider
     */
    public function testGeneratedSql($dql, $expectedSql)
    {
        $qText = 'SELECT ' . $dql . ' FROM ProgrammesPagesService:VersionType vt';
        $sql = $this->getEntityManager()->createQuery($qText)->getSql();
        $this->assertEquals($expectedSql, $this->extractFirstClause($sql));
    }

    public function matchAgainstDataProvider()
    {
        return [
            // Single item
            ["MATCH_AGAINST(vt.name, 'someValue')", "MATCH(v0_.name) AGAINST ('someValue')"],
            // Multiple items
            ["MATCH_AGAINST(vt.id, vt.name, 'someValue')", "MATCH(v0_.id, v0_.name) AGAINST ('someValue')"],
            // Custom mode, single item
            ["MATCH_AGAINST(vt.name, 'someValue' 'IN BOOLEAN MODE')", "MATCH(v0_.name) AGAINST ('someValue' IN BOOLEAN MODE)"],
            // Custom mode, multiple items
            ["MATCH_AGAINST(vt.id, vt.name, 'someValue' 'IN BOOLEAN MODE')", "MATCH(v0_.id, v0_.name) AGAINST ('someValue' IN BOOLEAN MODE)"],
        ];
    }

    private function extractFirstClause(string $sql)
    {
        $matches = [];
        preg_match('/(?<=SELECT ).*(?= AS sclr_0)/', $sql, $matches);
        return $matches[0] ?? null;
    }
}
