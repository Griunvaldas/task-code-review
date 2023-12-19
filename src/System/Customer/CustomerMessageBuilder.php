<?php

namespace App\System\Customer;

use App\Dto\Customer\NotifyCustomerDto;
use App\Entity\Customer;
use App\Model\Message;

class CustomerMessageBuilder
{
    public function build(NotifyCustomerDto $dto, Customer $customer): Message
    {
        return (new Message())
            ->setBody($dto->getBody())
            ->setType($dto->getType());
    }
}