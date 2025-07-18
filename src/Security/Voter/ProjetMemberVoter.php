<?php

namespace App\Security\Voter;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\Employe;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjetMemberVoter extends Voter
{
    public const IS_MEMBER = 'projet.is_member';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::IS_MEMBER && ($subject instanceof Projet || $subject instanceof Tache);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$this->supports($attribute, $subject)) {
            return false;
        }

        /** @var Employe $user */
        $user = $token->getUser();

        if (!$user instanceof Employe) {
            return false;
        }

        if ($subject instanceof Projet) {
            $projet = $subject;
        } elseif ($subject instanceof Tache) {
            $projet = $subject->getProjet();
        } else {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return $projet && $projet->getEmployes()->contains($user);
    }
}
