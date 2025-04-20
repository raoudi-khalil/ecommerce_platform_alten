<?php
// src/Service/UserManager.php

namespace App\Service;

use App\Entity\User;
use App\Model\RegisterUserModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {}

    public function registerFromModel(RegisterUserModel $model): User
    {
        $user = new User();
        $user->setEmail($model->email);
        $user->setUsername($model->username);
        $user->setFirstname($model->firstname);
        $user->setPassword(
            $this->hasher->hashPassword($user, $model->password)
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}