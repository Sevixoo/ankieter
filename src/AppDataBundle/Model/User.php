<?php

namespace AppDataBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Created by PhpStorm.
 * User: Seweryn
 * Date: 2016-04-03
 * Time: 22:52
 */
class User extends \AppDataBundle\Entity\Users implements UserInterface, \Serializable
{


    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function eraseCredentials()
    {
    }
    //-------------------------------------------------------------------------
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->login,
            $this->password,
            $this->isadmin
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->login,
            $this->password,
            $this->isadmin
            ) = unserialize($serialized);

    }
    //-------------------------------------------------------------------------
}