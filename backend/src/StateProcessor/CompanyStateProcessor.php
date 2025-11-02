<?php

namespace App\StateProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * State processor that automatically sets the company on entities that have a company relationship.
 */
class CompanyStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Only process entities that have a company property
        if (!property_exists($data, 'company')) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        // Get the current user
        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            // If creating and no user is authenticated, we might want to allow it (depends on your security)
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $company = $user->getCompany();
        
        if (!$company) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        // Only set company on create operations if not already set
        if ($operation instanceof Post) {
            if ($data->getCompany() === null) {
                $data->setCompany($company);
            }
        }

        // For update operations, ensure the company matches
        if ($operation instanceof Put || $operation instanceof Patch) {
            if ($data->getCompany() && $data->getCompany()->getId() !== $company->getId()) {
                throw new \RuntimeException('Cannot update resource from different company');
            }
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}

