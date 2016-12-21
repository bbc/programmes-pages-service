<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait PartnerPidTrait
{
    /**
     * @var string
     *
     * In Pips an Entity may be attached to a Partner and we may filter results
     * based upon this partner, however given that the Partner contains only a
     * pid, name and description, and the only value we shall ever use is pid
     * there is no value in creating  a Partner entity. Instead we shall attach
     * a PartnerPid directly onto the entities.
     * In PIPs a PartnerPid may be null (as the concept was introduced after
     * PIPs' creation) but this is treated the same as if the entity has the
     * Partner Pid of 's0000001' - The BBC Partner Pid. In order to avoid two
     * states that do the same thing we shall set PartnerPid to be NOT NULL and
     * we shall set a default value of 's0000001'.
     *
     * @ORM\Column(type="string", length=15, nullable=false, options={"default" = "s0000001"})
     */
    private $partnerPid = 's0000001';

    public function getPartnerPid(): string
    {
        return $this->partnerPid;
    }

    public function setPartnerPid(string $partnerPid)
    {
        $this->partnerPid = $partnerPid;
    }
}
