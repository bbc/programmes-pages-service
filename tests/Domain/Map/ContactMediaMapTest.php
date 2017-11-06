<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Map;

use BBC\ProgrammesPagesService\Domain\Entity\ContactMediaMap;
use BBC\ProgrammesPagesService\Domain\ValueObject\ContactDetails;
use PHPUnit\Framework\TestCase;

class ContactMediaMapTest extends TestCase
{
    /** @var  ContactMediaMap */
    private $contactMediaMap;

    public function setUp()
    {
        $this->contactMediaMap = new ContactMediaMap();
    }

    public function testCanAddOneContactMedia()
    {
        $contactMedia = new ContactDetails('email', 'myemail@something.com', 'a free text');

        $this->contactMediaMap->addContactMedia($contactMedia);

        $this->assertEquals([$contactMedia], $this->contactMediaMap->getContactsByMedia('email'));
    }

    public function testCanAddMultipleContactMedia()
    {
        $contactMedia1 = new ContactDetails('email', 'myemail@something.com', 'a free text');
        $contactMedia2 = new ContactDetails('email', 'myemail@something.com', 'a free text');
        $contactMedia3 = new ContactDetails('reddit', 'myredditUser', 'a free text');

        $this->contactMediaMap
            ->addContactMedia($contactMedia1)
            ->addContactMedia($contactMedia2)
            ->addContactMedia($contactMedia3);

        $this->assertEquals(
            [$contactMedia1, $contactMedia2],
            $this->contactMediaMap->getContactsByMedia('email')
        );

        $this->assertEquals(
            [$contactMedia3],
            $this->contactMediaMap->getContactsByMedia('reddit')
        );
    }

    public function testCanGetNamesOfUsedMedia()
    {
        $contactEmail = new ContactDetails('email', 'myemail@something.com', 'a free text');
        $contactReddit = new ContactDetails('reddit', 'myreddit', 'a free text');

        $this->contactMediaMap
            ->addContactMedia($contactEmail)
            ->addContactMedia($contactReddit);

        $this->assertEquals(
            ['email', 'reddit'],
            $this->contactMediaMap->getNamesOfUsedMedia()
        );
    }

    public function testReturnEmptyArrayWhenNoContactsExistForSpecifiedMedia()
    {
        $this->assertEquals(
            [],
            $this->contactMediaMap->getContactsByMedia('facebook')
        );
    }

    public function testInvalidContactMediaAreNotAddedButTheyDontThrowError()
    {
        $this->contactMediaMap->addContactMedia(new ContactDetails('invalid_type', 'myemail@something.com', 'a free text'));
        $this->assertEquals([], $this->contactMediaMap->getNamesOfUsedMedia());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Trying to get a contact detail with unknown type
     */
    public function testExceptionWhenGettingContactWithInvalidMediaType()
    {
        $this->contactMediaMap->getContactsByMedia('invalid_media');
    }
}
