<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping AS ORM;

#[ORM\Entity]
#[ORM\Table('location')]
class Location extends BaseEntity
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'address', type: Types::STRING, length: 255, nullable: false)]
    protected string $address;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 255, nullable: false)]
    protected string $city;

    #[ORM\Column(name: 'capacity', type: Types::INTEGER, nullable: true)]
    protected string $capacity;

}
