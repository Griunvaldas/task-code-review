<?php

namespace App\Dto\Customer;

use Symfony\Component\Routing\Annotation\Route;

class NotifyCustomerDto
{
    /*
     *
     */
    private string $body;
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