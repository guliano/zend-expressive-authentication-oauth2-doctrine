<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2-doctrine for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use Auth\Entity\CustomerAccount;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Zend\Expressive\Authentication\OAuth2\Doctrine\Entity\UserEntity;

class UserRepository extends AbstractRepository
    implements UserRepositoryInterface
{
    /**
     * @var string
     */
    private $usernameField;

    /**
     * @var string
     */
    private $customerAccountClass;

    /**
     * @var array
     */
    private $customerAccountFields;

    public function setUsernameField(string $usernameField) : void
    {
        $this->usernameField = $usernameField;
    }

    public function setCustomerAccountClass(string $className) : void
    {
        $this->customerAccountClass = $className;
    }

    public function setCustomerAccountFields(array $fields) : void
    {
        $this->customerAccountFields = $fields;
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        if ($clientEntity->hasCustomerArea()) {
            $repository = $this->objectManager->getRepository($this->customerAccountClass);
            foreach ($this->customerAccountFields as $field) {
                $user = $repository->findOneBy([$field => $username]);
                if ($user) {
                    break;
                }
            }

        } else {
            /** @var UserEntity $user */
            $user = $this->objectRepository->findOneBy([$this->usernameField => $username]);
        }

        if (!$user) {
            return null;
        }
//        $user->setIdentifier($user->getUsername());
        if (!$user->getIsActive()) {
            return null;
        }

        return password_verify($password, $user->getPassword()) ? $user : null;
    }
}
