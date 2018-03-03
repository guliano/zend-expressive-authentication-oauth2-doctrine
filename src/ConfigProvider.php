<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2-doctrine for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Zend\Expressive\Authentication\OAuth2\Entity as PdoEntity;
use Zend\Expressive\Authentication\OAuth2\Doctrine\Entity as DoctrineEntity;

class ConfigProvider
{
    /**
     * Return the configuration array.
     */
    public function __invoke() : array
    {
        return [
            'dependencies'   => $this->getDependencies(),
            'doctrine' => include __DIR__ . '/../config/doctrine.php',
            'oauth2_repository_mapping' => $this->getRepositoryMapping(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'aliases' => [
                AccessTokenRepositoryInterface::class => Repository\AccessTokenRepository::class,
                AuthCodeRepositoryInterface::class => Repository\AuthCodeRepository::class,
                ClientRepositoryInterface::class => Repository\ClientRepository::class,
                RefreshTokenRepositoryInterface::class => Repository\RefreshTokenRepository::class,
                ScopeRepositoryInterface::class => Repository\ScopeRepository::class,
                UserRepositoryInterface::class => Repository\UserRepository::class,
            ],
            'factories' => [
                Repository\AccessTokenRepository::class => Repository\RepositoryFactory::class,
                Repository\AuthCodeRepository::class => Repository\RepositoryFactory::class,
                Repository\ClientRepository::class => Repository\RepositoryFactory::class,
                Repository\RefreshTokenRepository::class => Repository\RepositoryFactory::class,
                Repository\ScopeRepository::class => Repository\RepositoryFactory::class,
                Repository\UserRepository::class => Repository\UserRepositoryFactory::class,
            ],
        ];
    }

    public function getRepositoryMapping(): array
    {
        return [
            Repository\AccessTokenRepository::class => PdoEntity\AccessTokenEntity::class,
            Repository\AuthCodeRepository::class => PdoEntity\AuthCodeEntity::class,
            Repository\ClientRepository::class => PdoEntity\ClientEntity::class,
            Repository\RefreshTokenRepository::class => PdoEntity\RefreshTokenEntity::class,
            Repository\ScopeRepository::class => PdoEntity\ScopeEntity::class,
            Repository\UserRepository::class => DoctrineEntity\UserEntity::class,
        ];
    }
}
