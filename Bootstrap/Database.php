<?php

namespace PrepayMatch\Bootstrap;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use PrepayMatch\Models\MatchedTransaction;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Database
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->schemaTool = new SchemaTool($entityManager);
    }

    /**
     * Installs all registered ORM classes
     */
    public function install()
    {
        $this->schemaTool->updateSchema(
            $this->getClassesMetaData(),
            true
        );
    }

    /**
     * Drop all registered ORM classes
     */
    public function uninstall()
    {
        $this->schemaTool->dropSchema(
            $this->getClassesMetaData()
        );
    }

    /**
     * @return array
     */
    public function getClassesMetaData()
    {
        return [
            $this->entityManager->getClassMetadata(MatchedTransaction::class),
        ];
    }
}