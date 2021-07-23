<?php

namespace App\Service;

use App\Model\Message;

interface SenderInterface
{
    public function supports(Message $message);
    public function send(Message $message);
}
