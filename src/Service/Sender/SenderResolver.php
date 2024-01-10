<?php

declare(strict_types=1);

namespace App\Service\Sender;

use App\Model\Message;

class SenderResolver
{
    public static function resolve(string $type): SenderInterface
    {
        switch ($type) {
            case Message::TYPE_EMAIL: {
                return new EmailSender();
            }
            case Message::TYPE_SMS: {
                return new SMSSender();
            }
            default:
                throw new \InvalidArgumentException('Message type not found');
        }
    }
}