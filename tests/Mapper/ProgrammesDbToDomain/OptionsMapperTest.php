<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\OptionsMapper;

class OptionsMapperTest extends BaseMapperTestCase
{
    public function testGetDomainModelSimple()
    {
        // just one set of options. no complicated inheritance
        $options = [
            'language' => [
                'value' => 'cy',
                'cascades' => true,
            ],
            'second_option' => [
                'value' => null,
                'cascades' => true,
            ],
        ];

        $expectedOptions = [
            'language' => 'cy',
            'second_option' => null
        ];
        $expectedEntity = new Options($expectedOptions);
        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($options));

    }

    public function testGetDomainModelWithInheritance()
    {
        // create three levels of options
        // and check the ones that are allowed to cascade, do so
        // once in the domain model, we don't care about that property

        $options = [
            'language' => [
                'value' => 'cy',
                'cascades' => true,
            ],
            'second_option' => [
                'value' => null,
                'cascades' => true,
            ],
            'third_option' => [
                'value' => null,
                'cascades' => true,
            ],
            'fourth_option' => [
                'value' => null,
                'cascades' => false,
            ],
        ];
        $parentOptions = [
            'language' => [
                'value' => 'en',
                'cascades' => true,
            ],
            'second_option' => [
                'value' => 'red',
                'cascades' => true,
            ],
            'third_option' => [
                'value' => null,
                'cascades' => true,
            ],
            'fourth_option' => [
                'value' => 'mother',
                'cascades' => false,
            ],
        ];
        $grandparentOptions = [
            'language' => [
                'value' => 'gd',
                'cascades' => true,
            ],
            'second_option' => [
                'value' => 'yellow',
                'cascades' => true,
            ],
            'third_option' => [
                'value' => 'monday',
                'cascades' => true,
            ],
            'fourth_option' => [
                'value' => 'grandmother',
                'cascades' => false,
            ],
        ];

        $expectedOptions = [
            'language' => 'cy',
            'second_option' => 'red',
            'third_option' => 'monday',
            'fourth_option' => null
        ];

        $expectedEntity = new Options($expectedOptions);

        $mapper = $this->getMapper();


        $arrayOfAncestors = [$parentOptions, $grandparentOptions];

        $this->assertEquals(
            $expectedEntity,
            $mapper->getDomainModel($options, ...$arrayOfAncestors)
        );
    }

    private function getMapper(): OptionsMapper
    {
        return new OptionsMapper($this->getMapperFactory());
    }
}
