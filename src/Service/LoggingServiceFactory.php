<?php

namespace BBC\ProgrammesPagesService\Service;

use Doctrine\ORM\EntityManager;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;

/**
 * A ServiceFactory that provides Proxies to Services that wrap the Service
 * to provide logging and timing information whenever a Service method is called.
 *
 * However be aware that this proxy isn't properly type-hinted (and can't be
 * without some heinous eval and reflection magic) so while it can be used in
 * place of any Service it does not have proper type hinting and shall fail any
 * type checks (which shall also lead to autocomplete breaking in IDEs).
 */
class LoggingServiceFactory
{
    private $instances = [];

    private $serviceFactory;

    private $logger;

    private $stopwatch;

    public function __construct(
        EntityManager $entityManager,
        MapperFactory $mapperFactory,
        LoggerInterface $logger,
        Stopwatch $stopwatch
    ) {
        $this->serviceFactory = new ServiceFactory($entityManager, $mapperFactory);
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    public function __call(string $methodName, array $arguments)
    {
        if (!isset($this->instances[$methodName])) {
            $this->instances[$methodName] = $this->proxyClass(
                $this->serviceFactory->{$methodName}(...$arguments),
                $this->logger,
                $this->stopwatch
            );
        }

        return $this->instances[$methodName];
    }

    private function proxyClass($service, $logger, $stopwatch)
    {
        // PHPCS gets confused about anonymous classes
        // @codingStandardsIgnoreStart
        return new class($service, $logger, $stopwatch) {
            private $service;
            private $logger;
            private $stopwatch;

            public function __construct(
                AbstractService $service,
                LoggerInterface $logger,
                Stopwatch $stopwatch
            ) {
                $this->service = $service;
                $this->logger = $logger;
                $this->stopwatch = $stopwatch;
            }

            public function __call(string $methodName, array $arguments)
            {
                $eventName = get_class($this->service) . '::' . $methodName;

                $this->stopwatch->start($eventName, 'pps');

                $result = $this->service->{$methodName}(...$arguments);

                $this->stopwatch->stop($eventName);

                // Log only the duration of the last period, otherwise methods
                // with that are called multiple times on a single page shall be
                // misrepresented.
                $periods = $this->stopwatch->getEvent($eventName)->getPeriods();
                $this->logger->info(sprintf(
                    'ProgrammesPagesService: Called %s (Time Taken: %sms)',
                    $eventName,
                    end($periods)->getDuration()
                ));

                return $result;
            }
        };
        // @codingStandardsIgnoreEnd
    }
}
