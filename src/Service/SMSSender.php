<?php

namespace App\Service;

use App\Model\Message;

class SMSSender implements SenderInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(Message $message)
    {
        return $message->type == Message::TYPE_EMAIL;
    }

    /**
     * {@inheritDoc}
     *
     * @param Message $message
     */
    public function send(Message $message)
    {
        print "SMS";
    }
}
