<?php

namespace App\StateProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * State processor for Order entity that automatically sets user and handles client/truck relationships.
 */
class OrderStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private Security $security,
        private EntityManagerInterface $em
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Order) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        // Set user on create
        if ($operation instanceof Post) {
            if ($data->getUser() === null) {
                $data->setUser($user);
            }

            // If client is provided via IRI, we need to resolve it
            // API Platform will handle IRI resolution, but we need to ensure it's from the same company
            if ($data->getClient() && $data->getClient()->getCompany()->getId() !== $user->getCompany()->getId()) {
                throw new \RuntimeException('Client must belong to your company');
            }

            // Handle delivery truck if provided
            if ($data->getDeliveryTruck() && $data->getDeliveryTruck()->getCompany()->getId() !== $user->getCompany()->getId()) {
                throw new \RuntimeException('Delivery truck must belong to your company');
            }

            // Set deliveredAt when status is 'delivered'
            if ($data->getStatus() === 'delivered' && $data->getDeliveredAt() === null) {
                $data->setDeliveredAt(new \DateTimeImmutable());
            }
        }

        // Handle update operations
        if ($operation instanceof Put || $operation instanceof Patch) {
            // Ensure company matches
            if ($data->getCompany() && $data->getCompany()->getId() !== $user->getCompany()->getId()) {
                throw new \RuntimeException('Cannot update order from different company');
            }

            // Set deliveredAt when status changes to 'delivered'
            if ($data->getStatus() === 'delivered' && $data->getDeliveredAt() === null) {
                $data->setDeliveredAt(new \DateTimeImmutable());
            }

            // Update updatedAt timestamp
            $data->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}

