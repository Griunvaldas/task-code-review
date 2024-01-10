<?php

declare(strict_types=1);

namespace App\System\Customer;

use App\Dto\Customer\NotifyCustomerDto;
use App\Model\Message;

class CustomerMessageBuilder
{
    public function build(NotifyCustomerDto $dto): Message
    {
        return (new Message())
            ->setBody($dto->getBody())
            ->setType($dto->getType());
    }
}
