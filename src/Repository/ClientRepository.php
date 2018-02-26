<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Zend\Expressive\Authentication\OAuth2\Entity\ClientEntity;

class ClientRepository extends AbstractRepository
    implements ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true): ?ClientEntity
    {
        /** @var ClientEntity $client */
        $client = $this->objectRepository->find($clientIdentifier);

        if (! $client instanceof ClientEntity) {
            return null;
        }

        if (! $this->isGranted($client, $grantType)) {
            return null;
        }

        if ($mustValidateSecret && ! password_verify((string)$clientSecret, $client->getSecret())) {
            return null;
        }

        return $client;
    }

    /**
     * @param ClientEntity $clientEntity
     * @param string $grantType
     * @return bool
     */
    protected function isGranted(ClientEntity $clientEntity, string $grantType): bool
    {
        switch ($grantType) {
            case 'authorization_code':
                return ! ($clientEntity->hasPasswordClient() || $clientEntity->hasPersonalAccessClient());
            case 'personal_access':
                return $clientEntity->hasPersonalAccessClient();
            case 'password':
                return $clientEntity->hasPasswordClient();
            default:
                return true;
        }
    }
}
