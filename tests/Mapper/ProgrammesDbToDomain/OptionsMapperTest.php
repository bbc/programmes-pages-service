<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\OptionsMapper;

class OptionsMapperTest extends BaseMapperTestCase
{
    public function testBasicOptionsNoHierarchy()
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
            'second_option' => null,
        ];

        $this->assertEquals(
            new Options($expectedOptions),
            $this->getMapper()->getDomainModel($options)
        );
    }

    public function testInheritanceOfOptions()
    {
        // Create three levels of options
        // and check the ones that are allowed to cascade, do so.
        // Once in the domain model, we don't care about the cascades property

        $childOptions = [
            'language' => [
                'value' => 'languageInChild',
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
                'value' => 'languageInParent',
                'cascades' => true,
            ],
            'second_option' => [
                'value' => 'secondInParent',
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
                'value' => 'languageInGrandParent',
                'cascades' => true,
            ],
            'second_option' => [
                'value' => 'secondInGrandParent',
                'cascades' => true,
            ],
            'third_option' => [
                'value' => 'thirdInGrandParent',
                'cascades' => true,
            ],
            'fourth_option' => [
                'value' => 'fourthInGrandParent',
                'cascades' => false,
            ],
        ];

        $expectedOptions = [
            'language' => 'languageInChild',
            'second_option' => 'secondInParent',
            'third_option' => 'thirdInGrandParent',
            'fourth_option' => null,
        ];

        $this->assertEquals(
            new Options($expectedOptions),
            $this->getMapper()->getDomainModel($childOptions, $parentOptions, $grandparentOptions)
        );
    }

    private function getMapper(): OptionsMapper
    {
        return new OptionsMapper($this->getMapperFactory());
    }
}
