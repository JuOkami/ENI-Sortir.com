<?php

namespace App\Services;

use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;

class InscriptionSortieService
{

    public function validationInscription(Sortie $sortie, Participant $utilisateur){
        $personnesInscrites = $sortie->getInscriptions();
        $datedujour = new DateTime();

        if ($personnesInscrites->contains($utilisateur)){
            return ['inscriptionPossible' => false, 'utilisateurInscrit' => true, 'motif' => 'Vous êtes inscrit à cet evenement !'];
        }
        if (!$utilisateur->isActif()){
            return ['inscriptionPossible' => false, 'utilisateurInscrit' => false, 'motif' => 'Votre compte a été désactivé. Pour le réactiver, veuillez contacter un administrateur'];
        }
        if ($datedujour > $sortie->getDateLimiteInscription()){
            return ['inscriptionPossible' => false, 'utilisateurInscrit' => false, 'motif' => "La date limite d'inscription est dépassée !"];
        }
        if ($personnesInscrites->count() >= $sortie->getNbInscriptionsMax()){
            return ['inscriptionPossible' => false, 'utilisateurInscrit' => false, 'motif' => 'Cet evenement est complet ! Revenez plus tard en cas de désistement'];
        }

        return ['inscriptionPossible' => true, 'utilisateurInscrit' => false, 'motif' => 'Pour vous inscrire à cet evenement : '];

    }

}