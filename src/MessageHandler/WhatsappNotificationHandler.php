<?php

namespace App\MessageHandler;

use App\Message\WhatsappNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WhatsappNotificationHandler
{
    public function __invoke(WhatsappNotification $message): void
    {
        // do something with your message
    }
}
