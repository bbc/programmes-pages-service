<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;

class CategoryMapperTest extends BaseMapperTestCase
{
    public function testGetDomainModelWithFormat()
    {
        $dbEntityArray = [
            'ancestry' => '1,',
            'id' => '1',
            'type' => 'format',
            'pipId' => 'PT001',
            'title' => 'Title',
            'urlKey' => 'url_key',
        ];

        $expectedEntity = new Format(
            [1],
            'PT001',
            'Title',
            'url_key'
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelWithGenre()
    {
        $dbEntityArray = [
            'ancestry' => '1,',
            'id' => '1',
            'type' => 'genre',
            'pipId' => 'C00126',
            'title' => 'Title',
            'urlKey' => 'url_key',
            'parent' => null,
        ];

        $expectedEntity = new Genre(
            [1],
            'C00126',
            'Title',
            'url_key',
            null
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithGenreWithParent()
    {
        $dbEntityArray = [
            'ancestry' => '1,2,',
            'id' => '2',
            'type' => 'genre',
            'pipId' => 'C00126',
            'title' => 'Title',
            'urlKey' => 'url_key',
            'parent' => [
                'ancestry' => '1,',
                'id' => '1',
                'type' => 'genre',
                'pipId' => 'C00127',
                'title' => 'Parent Title',
                'urlKey' => 'parent_url_key',
                'parent' => null,
            ],
        ];

        $expectedEntity = new Genre(
            [1, 2],
            'C00126',
            'Title',
            'url_key',
            new Genre(
                [1],
                'C00127',
                'Parent Title',
                'parent_url_key',
                null
            )
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithGenreWithoutFetchingParent()
    {
        $dbEntityArray = [
            'ancestry' => '1,',
            'id' => '1',
            'type' => 'genre',
            'pipId' => 'C00126',
            'title' => 'Title',
            'urlKey' => 'url_key',
        ];

        $expectedEntity = new Genre(
            [1],
            'C00126',
            'Title',
            'url_key',
            new UnfetchedGenre()
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not build domain model for unknown category type "wrongwrongwrong"
     */
    public function testGetDomainModelWithInvalidTypeThrowsExeption()
    {
        $dbEntityArray = [
            'ancestry' => '1,',
            'id' => '1',
            'type' => 'wrongwrongwrong',
            'pipId' => 'C00126',
            'title' => 'Title',
            'urlKey' => 'url_key',
        ];

        $mapper = $this->getMapper();
        $mapper->getDomainModel($dbEntityArray);
    }

    private function getMapper(): CategoryMapper
    {
        return new CategoryMapper($this->getMapperFactory());
    }
}
