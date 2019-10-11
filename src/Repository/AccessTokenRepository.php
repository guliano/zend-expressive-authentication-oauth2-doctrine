<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Zend\Expressive\Authentication\OAuth2\Doctrine\Entity\AccessTokenEntity;

class AccessTokenRepository extends AbstractRepository
    implements AccessTokenRepositoryInterface
{
    /**
     * @param ClientEntityInterface $clientEntity
     * @param array $scopes
     * @param string|int|null $userIdentifier
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);
        return $accessToken;
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        if ( $this->objectRepository->find($accessTokenEntity->getIdentifier()) instanceof AccessTokenEntity) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->objectManager->persist($accessTokenEntity);
        $this->objectManager->flush();
    }

    /**
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        /** @var AccessTokenEntity $accessToken */
        $accessToken = $this->objectRepository->find($tokenId);
        $accessToken->setRevoked(true);
        $this->objectManager->persist($accessToken);
        $this->objectManager->flush();
    }

    /**
     * @param string $tokenId
     * @return bool
     */
    public function isAccessTokenRevoked($tokenId)
    {
        /** @var AccessTokenEntity $accessToken */
        $accessToken = $this->objectRepository->find($tokenId);
        if (! $accessToken instanceof AccessTokenEntity) {
            return false;
        }
        return $accessToken->isRevoked();
    }
}
