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
            'id' => '1',
            'type' => 'genre',
            'pipId' => 'C00126',
            'title' => 'Title',
            'urlKey' => 'url_key',
        ];

        $expectedEntity = new Genre(
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
            'id' => '1',
            'type' => 'genre',
            'pipId' => 'C00126',
            'title' => 'Title',
            'urlKey' => 'url_key',
            'parent' => [
                'id' => '2',
                'type' => 'genre',
                'pipId' => 'C00127',
                'title' => 'Parent Title',
                'urlKey' => 'parent_url_key',
            ],
        ];

        $expectedEntity = new Genre(
            'C00126',
            'Title',
            'url_key',
            new Genre(
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
