<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.03.16
 * Time: 22:49
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\User;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ImmortalchessnetService
{
    use ContainerAwareTrait;
    
    /**
     * @param string $login
     * @param string $password
     * @return User
     */
    public function getUser(string $login, string $password) : User
    {
        $user = new User();
        
        $userData = $this->getUserData($login, $password);
        
        if (empty($userData)) {
            return $user;
        }

        $user->setLogin($userData[0]['username'])
             ->setEmail($userData[0]['email']);
        
        return $user;
    }

    /**
     * @param string $login
     * @param string $password
     * @return array
     */
    private function getUserData(string $login, string $password) : array
    {
        return $this->getConnection()->fetchAll(
            "SELECT * 
                FROM `immortalchess`.`user` 
                WHERE username = '$login' AND usergroupid IN (5,6,7)
                AND PASSWORD = md5( concat( md5( '$password' ) , salt ) )"
        );
    }

    /**
     * @return Connection
     */
    private function getConnection() : Connection
    {
        return $this->container->get('doctrine')->getConnection('immortalchess');
    }
}