<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

namespace Zend\Expressive\Authentication\OAuth2\Doctrine\Repository;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

abstract class AbstractRepository
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * AbstractRepository constructor.
     *
     * @param ObjectManager $objectManager
     * @param ObjectRepository $objectRepository
     * @param string $entityClass
     */
    public function __construct(
        ObjectManager $objectManager,
        ObjectRepository $objectRepository,
        string $entityClass)
    {
        $this->objectManager = $objectManager;
        $this->objectRepository = $objectRepository;
        $this->entityClass = $entityClass;
    }

    /**
     * Return a string of scopes, separated by space
     * from ScopeEntityInterface[]
     *
     * @param ScopeEntityInterface[] $scopes
     * @return string
     */
    protected function scopesToString(array $scopes) : string
    {
        return trim(array_reduce($scopes, function ($result, $item) {
            return $result . ' ' . $item->getIdentifier();
        }));
    }
}
