<?php

declare(strict_types=1);

namespace App\Dto\Customer;

use Symfony\Component\Validator\Constraints as Assert;

class NotifyCustomerDto
{
    /**
     * @Assert\NotBlank(message="body cannot be empty", payload="101")
     */
    private string $body;

    /**
     * @Assert\NotBlank(message="type cannot be empty", payload="101")
     */
    private string $type;

    public function __construct(string $body, string $type)
    {
        $this->body = $body;
        $this->type = $type;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
