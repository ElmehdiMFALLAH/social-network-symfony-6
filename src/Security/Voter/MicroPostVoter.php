<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\MicroPost;
use Symfony\Bundle\SecurityBundle\Security;

class MicroPostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const COMMENT = 'POST_COMMENT';

    public function __construct(
        private Security $security
    )
    {
        
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof MicroPost;
    }

    /**
     * @param Micropost $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        if ($isAdmin === true) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        $isAuth = $user instanceof UserInterface;

        switch ($attribute) {
            case self::EDIT:
                return $isAuth && (
                    $subject->getAuthor()->getId() == $user->getId() || $isAdmin === true);
                break;

            case self::VIEW:
                return true;
                break;

            case self::COMMENT:
                return $isAuth && (
                    $subject->getAuthor()->isVerified() === true);
                break;
        }

        return false;
    }
}
