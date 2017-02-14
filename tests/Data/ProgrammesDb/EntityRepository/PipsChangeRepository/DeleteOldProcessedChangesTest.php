<?php
namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PipsChangeRepository;

use DateTime;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class DeleteOldProcessedChangesTest extends AbstractDatabaseTest
{
    public function testSomething()
    {
        /*
        Fixture/processed_times:
            2 days ago
            10 months ago
            4 months ago
            1970-01-01 00:00:00
            null
        */
        $this->loadFixtures(['PipsChange']);

        // delete old changes processed
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:PipsChange');
        $repo->deleteOldProcessedChanges();

        // assert
        $processedChangesEventsInDb = $repo->findAll();
        $this->assertCount(3, $processedChangesEventsInDb);
        $processedDatesInDb = array_map(function($o) { return $o->getProcessedTime(); }, $processedChangesEventsInDb);

        $today = new DateTime();
        $diffTime = $today->diff($processedDatesInDb[0]);
        $this->assertTrue($diffTime->format('m') < 3);
        $this->assertEquals('1970-01-01 00:00:00', $processedDatesInDb[1]->format('Y-m-d H:i:s'));
        $this->assertEquals(null, $processedDatesInDb[2]);
    }
}
