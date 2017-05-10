<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;
use DateTimeImmutable;
use BBC\ProgrammesPagesService\Cache\CacheInterface;

class BroadcastsService extends AbstractService
{
    public function __construct(
        BroadcastRepository $repository,
        BroadcastMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByVersion(
        Version $version,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByVersion(
            [$version->getDbId()],
            'Broadcast',
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findByServiceAndDateRange(
        Sid $serviceId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findAllByServiceAndDateRange(
            $serviceId,
            $startDate,
            $endDate,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function groupBroadcastsByPeriodOfDay(array $broadcasts, DateTimeImmutable $selectedDate)
    {
        $intervalsDay = [
            'early' => [],
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
            'late' => []
        ];

        $prior_broadcast = null;
        foreach($broadcasts as $broadcast) {
            // // If the end of the prior is earlier than the start of this broadcast
            // // then inject a broadcast gap object.
            // if ($prior_broadcast && $prior_broadcast->end->compare($broadcast->start) == -1) {
            //     $period = $this->_getBroadcastPeriod($prior_broadcast->end, $day, $use_timezones);
            //     $periods_of_day[$period][] = $this->_broadcastGap($prior_broadcast->end, $broadcast->start);
            // }

            $period = $this->_getBroadcastPeriod($broadcast, $selectedDate);
            $intervalsDay[$period][] = $broadcast;
        }

        return array_filter($intervalsDay);
    }

    private function _getBroadcastPeriod(Broadcast $broadcast, DateTimeImmutable $selectedDate)
    {
        $dayStart = $selectedDate->setTime(0,0,0);
        $dayEnd = $selectedDate->setTime(23,59,59);

        $startBroadcast = $broadcast->getStartAt();
        $startBroadcastHour = $startBroadcast->format('H');

        switch ($startBroadcastHour)  {
            case ($startBroadcast > $dayEnd && $startBroadcastHour < 6):
                return 'late';
            case ($startBroadcastHour < 6):
                return 'early';
            case ($startBroadcastHour < 12):
                return 'morning';
            case ($startBroadcastHour < 18):
                return 'afternoon';
            case ($startBroadcastHour <= 23 && $startBroadcast > $dayStart):
                return 'evening';
        }
    }

    /**
     * @param array $broadcasts
     * @return Broadcast|null
     */
    public function getBroadcastedNowFromSchedule(array $broadcasts)
    {
        $now = new DateTimeImmutable();

        foreach ($broadcasts as $broadcast)
        {
            if ($broadcast->getStartAt() <= $now && $broadcast->getEndAt() < $now) {
                return $broadcast;
            }

        }

        return null;
    }
}
