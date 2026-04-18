<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $payload['id'] = $event->getUser()->getId();
        $payload['email'] = $event->getUser()->getEmail();
        unset($payload['roles']);

        $event->setData($payload);
    }
}
