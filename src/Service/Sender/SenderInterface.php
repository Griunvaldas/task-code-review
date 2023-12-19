<?php

namespace App\Service\Sender;

use App\Model\Message;

interface SenderInterface
{
    public function supports(Message $message): bool;
    public function send(Message $message): void;
    public function isSent(): bool;
}
