<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Enrich the JWT authentication success response with user and company data.
 */
class JWTAuthenticationSuccessListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            // Triggered when json_login succeeds and Lexik returns the token
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        // In some cases, $user can be a string (e.g., when anonymous), guard against it
        if (!$user instanceof User) {
            return;
        }

        $company = $user->getCompany();

        $data = $event->getData(); // Contains the 'token' key by default

        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'company' => $company ? [
                'id' => $company->getId(),
                'name' => $company->getName(),
            ] : null,
        ];

        $event->setData($data);
    }
}
