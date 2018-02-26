<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2-doctrine for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Zend\Expressive\Authentication\OAuth2\Doctrine\Entity\UserEntity;

class UserRepository extends AbstractRepository
    implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    )
    {
        /** @var UserEntity $user */
        $user = $this->objectRepository->findOneBy(compact('username'));

        if (! $user) {
            return null;
        }

        return password_verify($password, $user->getPassword()) ? $user : null;
    }
}
