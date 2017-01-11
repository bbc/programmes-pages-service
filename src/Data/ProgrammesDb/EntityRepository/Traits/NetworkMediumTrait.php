<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use InvalidArgumentException;

trait NetworkMediumTrait
{
    private function assertNetworkMedium(?string $medium): void
    {
        if (!in_array($medium, [NetworkMediumEnum::TV, NetworkMediumEnum::RADIO])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Network medium must be %s or %s, instead got %s',
                    NetworkMediumEnum::TV,
                    NetworkMediumEnum::RADIO,
                    $medium
                )
            );
        }
    }
}
