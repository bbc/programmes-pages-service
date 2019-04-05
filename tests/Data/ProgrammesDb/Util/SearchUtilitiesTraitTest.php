<?php
declare(strict_types = 1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Util;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Util\SearchUtilitiesTrait;
use PHPUnit\Framework\TestCase;

class SearchUtilitiesTraitTest extends TestCase
{
    /** @var SearchUtilitiesTrait */
    private $searchUtilityObject;

    public function setUp()
    {
        $this->searchUtilityObject = $this->getObjectForTrait(SearchUtilitiesTrait::class);
    }

    /**
     * @dataProvider stripPunctuationDataProvider
     */
    public function testStripPunctuation(string $string, string $expectedString): void
    {
        $actualString = $this->invokePrivateMethod($this->searchUtilityObject, 'stripPunctuation', [$string]);
        $this->assertEquals($expectedString, $actualString);
    }

    public function stripPunctuationDataProvider()
    {
        return [
            'Test simple string' => [
                'A Simple string without punctuation',
                'A Simple string without punctuation',
            ],
            'Test simple punctuation' => [
                'A string, with (some), shatner like, #pauses! It. Is. So. Fun. To - Write _($unit) Tests & Other; Things: Too',
                'A string with some shatner like pauses It Is So Fun To  Write unit Tests  Other Things Too',
            ],
            'Test weird unicode that will break your IDE' => [
                'A string †‡ of ⸁⸇ commonly used and sensible ⸠ symbols',
                'A string  of  commonly used and sensible  symbols',
            ],
        ];
    }

    /**
     * @dataProvider makeBooleanSearchQueryDataProvider
     */
    public function testMakeBooleanSearchQuery(string $string, ?string $expectedString): void
    {
        $actualString = $this->invokePrivateMethod($this->searchUtilityObject, 'makeBooleanSearchQuery', [$string]);
        $this->assertEquals($expectedString, $actualString);
    }

    public function makeBooleanSearchQueryDataProvider(): array
    {
        return [
            'A single Word' => [
                'SingleWord',
                '+SingleWord',
            ],
            'A simple search' => [
                'This simple search',
                '+This +simple +search',
            ],
            'An erratically spaced and punctuated sentence' => [
                'Doctor  ,+ Who&" The, search',
                '+Doctor +Who +The +search',
            ],
            'Test short stopwords are excluded' => [
                'A string that I put an & an stopwords in',
                '+string +that +put +stopwords',
            ],
            'An invalid search' => [
                'i i i i i an is',
                null,
            ],
        ];
    }

    private function invokePrivateMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
