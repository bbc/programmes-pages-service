<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\ContactMediaEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\ContactMedia;
use BBC\ProgrammesPagesService\Domain\Map\ContactMediaMap;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    /** @var ContactMediaMap */
    private $contactEntity;

    public function setUp()
    {
        $this->contactEntity = new ContactMediaMap();
    }

    public function testCanAddOneEmailContact()
    {
        $this->contactEntity->addContactMedia(
            new ContactMedia(
                ContactMediaEnum::EMAIL,
                'myemail@something.com',
                'any text'
        ));

        $this->assertEquals(
            [new ContactMedia('email', 'myemail@something.com', 'any text')],
            $this->contactEntity->getContactsByMedia(ContactMediaEnum::EMAIL)
        );
    }

    public function testCanAddMultipleEmailContacts()
    {

        $this->contactEntity->addContactMedia(
            new ContactMedia(
                ContactMediaEnum::EMAIL,
                'myemailAAAA@something.com',
                'any text'
        ));

        $this->contactEntity->addContactMedia(
            new ContactMedia(
                ContactMediaEnum::EMAIL,
                'myemailBBBB@something.com',
                'any text'
        ));

        $this->assertEquals([
                new ContactMedia('email', 'myemailAAAA@something.com', 'any text'),
                new ContactMedia('email', 'myemailBBBB@something.com', 'any text')
            ],
            $this->contactEntity->getContactsByMedia(ContactMediaEnum::EMAIL)
        );
    }

    public function testCanReceiveContactDetailsByType()
    {
        $this->assertEquals(
            [],
            $this->contactEntity->getContactsByMedia(ContactMediaEnum::GOOGLE)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @InvalidArgumentException Trying to get a contact detail with unknown type
     */
    public function testCannotReceiveNoneSupportedContactTypes()
    {
        $this->contactEntity->getContactsByMedia('wrong_type');
    }
}
