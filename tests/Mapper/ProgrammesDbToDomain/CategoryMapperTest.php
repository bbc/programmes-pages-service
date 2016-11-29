<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use PHPUnit_Framework_TestCase;

class CategoryMapperTest extends PHPUnit_Framework_TestCase
{
    public function testGetDomainModelWithFormat()
    {
        $dbEntityArray = [
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

        $mapper = new CategoryMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
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
        ];

        $expectedEntity = new Genre(
            [1],
            'C00126',
            'Title',
            'url_key'
        );

        $mapper = new CategoryMapper();
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
                'parent_url_key'
            )
        );

        $mapper = new CategoryMapper();
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

        $mapper = new CategoryMapper();
        $mapper->getDomainModel($dbEntityArray);
    }
}
