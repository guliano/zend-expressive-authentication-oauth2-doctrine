<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use Doctrine\ORM\ORMException;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Zend\Expressive\Authentication\OAuth2\Entity\AuthCodeEntity;

class AuthCodeRepository extends AbstractRepository
    implements AuthCodeRepositoryInterface
{
    /**
     * @return AuthCodeEntityInterface
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    /**
     * @param AuthCodeEntityInterface $authCodeEntity
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        try {
            $this->objectManager->persist($authCodeEntity);
            $this->objectManager->flush();
        } catch (ORMException $exception) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }


    public function revokeAuthCode($codeId)
    {
        /** @var AuthCodeEntity $accessToken */
        $authCode = $this->objectRepository->find($codeId)->setRevoked(true);
        $this->objectManager->persist($authCode);
        $this->objectManager->flush();
    }

    public function isAuthCodeRevoked($codeId)
    {
        /** @var AuthCodeEntity $accessToken */
        $authCode = $this->objectRepository->find($codeId);
        if (! $authCode instanceof AuthCodeEntity) {
            return false;
        }
        return $authCode->isRevoked();
    }
}
