<?php

namespace Tests\BBC\ProgrammesPagesService;

use BBC\ProgrammesPagesService\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;

abstract class AbstractServiceTest extends TestCase
{
    const ENTITY_NS = 'BBC\ProgrammesPagesService\Domain\Entity\\';
    const REPOSITORY_NS = 'BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\\';
    const MAPPER_NS = 'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\';

    protected $mockRepository;

    protected $mockMapper;

    protected $mockCache;

    protected function setUpRepo($repositoryName)
    {
        $this->mockRepository = $this->getRepo($repositoryName);
    }

    protected function getRepo($repositoryName)
    {
        return $this->createMock($this::REPOSITORY_NS . $repositoryName);
    }

    protected function setUpCache()
    {
        $this->mockCache = $this->getMockBuilder(Cache::class)
            ->setConstructorArgs([new NullAdapter(), ''])
            ->setMethods(null)
            ->getMock();
    }

    protected function setUpMapper($mapperName, $callback)
    {
        $this->mockMapper = $this->createMock($this::MAPPER_NS . $mapperName);

        // TODO once we always pass in a callable to this function then add a typehint and remove this check
        if (!is_callable($callback)) {
            $callback = function ($entity) use ($callback) {
                return $this->$callback($entity);
            };
        }

        $this->mockMapper->method('getDomainModel')->will($this->returnCallback($callback));
    }
}
