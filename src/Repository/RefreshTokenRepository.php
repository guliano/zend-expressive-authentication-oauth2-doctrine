<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use Doctrine\ORM\ORMException;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Zend\Expressive\Authentication\OAuth2\Doctrine\Entity\RefreshTokenEntity;

class RefreshTokenRepository extends AbstractRepository
    implements RefreshTokenRepositoryInterface
{
    /**
     * @return RefreshTokenEntityInterface|RefreshTokenEntity
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        try {
            $this->objectManager->persist($refreshTokenEntity);
            $this->objectManager->flush();
        } catch (ORMException $exception) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        /** @var RefreshTokenEntity $accessToken */
        $accessToken = $this->objectRepository->find($tokenId)->setRevoked(true);
        $this->objectManager->persist($accessToken);
        $this->objectManager->flush();
    }

    /**
     * @param string $tokenId
     * @return bool
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        /** @var RefreshTokenEntity $accessToken */
        $accessToken = $this->objectRepository->find($tokenId);
        if (! $accessToken instanceof RefreshTokenEntity) {
            return false;
        }
        return $accessToken->isRevoked();
    }
}
