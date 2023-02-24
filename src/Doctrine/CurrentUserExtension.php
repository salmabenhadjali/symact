<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class CurrentUserExtension implements QueryItemExtensionInterface, QueryCollectionExtensionInterface
{
    /** @var Security  */
    private Security $security;
    /** @var AuthorizationCheckerInterface  */
    private AuthorizationCheckerInterface $checker;

    public function __construct(Security $security, AuthorizationCheckerInterface $checker)
    {
        $this->security = $security;
        $this->checker = $checker;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) : void
    {
        $this->updateQuery($resourceClass, $queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ) : void
    {
        $this->updateQuery($resourceClass, $queryBuilder);
    }

/**
 * @param string $resourceClass
 * @param QueryBuilder $queryBuilder
 * @return void
 */public function updateQuery(string $resourceClass, QueryBuilder $queryBuilder): void
{
    $user = $this->security->getUser();

    if (
        in_array($resourceClass, [Customer::class, Invoice::class])
        &&
        !$this->checker->isGranted('ROLE_ADMIN')
        &&
        $user instanceof User
    ) {
        $aliasRoot = $queryBuilder->getAllAliases()[0];
        if ($resourceClass === Customer::class) {
            $queryBuilder->andWhere("$aliasRoot.user = :user");
        } else {
            $queryBuilder->join("$aliasRoot.customer", "c")
                ->andWhere("c.user = :user");
        }
        $queryBuilder->setParameter('user', $user);
    }
} 
}