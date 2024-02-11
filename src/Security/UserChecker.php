<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    /**
     * @param User $user
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (null === $user->getBannedUntil()) {
            return;
        }

        $now = new DateTime();

        if ($now < $user->getBannedUntil()) {
            throw new AccessDeniedHttpException('The user is banned');
        } else {
            $user->setBannedUntil(null);

            $this->em->persist($user);
            $this->em->flush();
        }
    }

    /**
     * @param User $user
     */
    public function checkPostAuth(UserInterface $user): void
    {
    }
}
