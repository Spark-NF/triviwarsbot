<?php
namespace TriviWars\DB;

use Doctrine\ORM\EntityManager;

class TriviDB
{
    /** @var EntityManager */
    private static $em = null;

    /**
     * @return EntityManager
     */
    public static function getEntityManager()
    {
        return self::$em;
    }

    /**
     * @param EntityManager $entityManager
     */
    public static function setEntityManager($entityManager)
    {
        self::$em = $entityManager;
    }
}