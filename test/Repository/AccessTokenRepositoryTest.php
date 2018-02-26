<?php

namespace ZendTest\Expressive\Authentication\OAuth2\Doctrine\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Expressive\Authentication\OAuth2\Doctrine\Repository\AccessTokenRepository;
use Zend\Expressive\Authentication\OAuth2\Entity\AccessTokenEntity;

class AccessTokenRepositoryTest extends TestCase
{
    /**
     * @var AccessTokenRepository
     */
    private $repository;

    private $objectManager;

    private $objectRepository;

    protected function setUp()
    {
        $this->objectManager = $this->prophesize(EntityManagerInterface::class);
        $this->objectRepository = $this->prophesize(ObjectRepository::class);
        $this->repository = new AccessTokenRepository(
            $this->objectManager->reveal(),
            $this->objectRepository->reveal(),
            AccessTokenEntity::class
        );
    }

    public function testGetNewTokenReturnsAccessTokenEntityWithScopes()
    {
        $client = $this->prophesize(ClientEntityInterface::class);
        $client->getIdentifier()->willReturn('client_id');
        $user = 'foo';
        $scope = $this->prophesize(ScopeEntityInterface::class);
        $scope->getIdentifier()->willReturn('bar');

        $token = $this->repository->getNewToken($client->reveal(), [$scope->reveal()], $user);
        $this->assertInstanceOf(AccessTokenEntity::class, $token);
    }

    public function testPersistNewTokenRaisesExceptionIfIdentifierExists()
    {
        $accessToken = $this->prophesize(AccessTokenEntityInterface::class);
        $accessToken->getIdentifier()->willReturn('token_id');
        $this->objectRepository->find($accessToken->reveal()->getIdentifier())->willReturn(new AccessTokenEntity());
        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->repository->persistNewAccessToken($accessToken->reveal());
    }

    public function testIsAccessTokenRevokedWillReturnFalseIfEntityNotFound()
    {
        $tokenId = 'token_id';
        $this->objectRepository->find($tokenId)->willReturn(null);
        $this->assertFalse($this->repository->isAccessTokenRevoked($tokenId));
    }
}
