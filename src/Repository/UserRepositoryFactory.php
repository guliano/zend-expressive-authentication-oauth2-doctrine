<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Zend\Expressive\Authentication\OAuth2\Doctrine\Exception;

class UserRepositoryFactory
{
    public function __invoke(ContainerInterface $container): UserRepository
    {
        if (! $container->has(EntityManagerInterface::class)) {
            throw new Exception\ServiceNotFoundException(
                'Doctrine entity manager was not found in the container'
            );
        }

        try {
            /** @var EntityManagerInterface $em */
            $em = $container->get(EntityManagerInterface::class);
        } catch (ContainerExceptionInterface $exception) {
            throw new Exception\InvalidServiceException(
                'Could not create Doctrine entity manager service from container'
            );
        }

        $config = $container->has('config') ? $container->get('config') : [];
        $config = $config['authentication'] ?? null;

        if (
            null === $config ||
            (
                ! isset($config['doctrine']) ||
                ! isset($config['doctrine']['user_class'])
            )
        ) {
            throw new Exception\InvalidConfigException(
                'The OAuth2 Doctrine configuration is missing'
            );
        }

        if (
            null === $config ||
            (
                ! isset($config['doctrine']) ||
                ! isset($config['doctrine']['customer_account_class'])
            )
        ) {
            throw new Exception\InvalidConfigException(
                'The OAuth2 Doctrine configuration is missing'
            );
        }

        $repository = new UserRepository(
            $em,
            $em->getRepository($config['doctrine']['user_class']),
            $config['doctrine']['user_class']
        );
        $repository->setUsernameField($config['doctrine']['user_field'] ?? 'username');
        $repository->setCustomerAccountClass($config['doctrine']['customer_account_class']);
        $repository->setCustomerAccountFields($config['doctrine']['customer_account_fields'] ?? ['username']);

        return $repository;
    }
}
