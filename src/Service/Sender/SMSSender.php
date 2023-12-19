<?php

namespace App\Service\Sender;

use App\Model\Message;

class SMSSender implements SenderInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(Message $message): bool
    {
        return $message->type == Message::TYPE_SMS;
    }

    /**
     * {@inheritDoc}
     *
     * @param Message $message
     */
    public function send(Message $message)
    {
        print "SMS sent" . PHP_EOL;
    }
}
