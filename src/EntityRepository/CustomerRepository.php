<?php

declare(strict_types=1);

namespace App\EntityRepository;

use App\Entity\Customer;
use Doctrine\ORM\EntityRepository;

class CustomerRepository extends EntityRepository
{
    public function getEntityClass(): string
    {
        return Customer::class;
    }
}
