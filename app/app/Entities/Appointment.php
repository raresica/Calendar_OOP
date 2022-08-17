<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('appointment')]
class Appointment extends BaseEntity
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'reservation', type: Types::DATE_MUTABLE, nullable: false)]
    protected \DateTime $reservation;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'appointments')]
    #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: false)]
    protected Location $location;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'appointments')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $user;
}


