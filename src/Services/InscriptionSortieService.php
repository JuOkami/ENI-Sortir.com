<?php

namespace App\Services;

use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;

class InscriptionSortieService
{

    public function isInscrit(Sortie $sortie, Participant $utilisateur){
        return $sortie->getInscriptions()->contains($utilisateur);
    }

    public function validationInscription(Sortie $sortie, Participant $utilisateur){
        $personnesInscrites = $sortie->getInscriptions();
        $datedujour = new DateTime();

        if ($personnesInscrites->contains($utilisateur)){
            return ['inscriptionPossible' => false, 'motif' => 'Vous êtes inscrit à cet evenement !'];
        }
        if (!$utilisateur->isActif()){
            return ['inscriptionPossible' => false, 'motif' => 'Votre compte a été désactivé. Pour le réactiver, veuillez contacter un administrateur'];
        }
        if ($datedujour > $sortie->getDateLimiteInscription()){
            return ['inscriptionPossible' => false, 'motif' => "La date limite d'inscription est dépassée !"];
        }
        if ($personnesInscrites->count() >= $sortie->getNbInscriptionsMax()){
            return ['inscriptionPossible' => false, 'motif' => 'Cet evenement est complet ! Revenez plus tard en cas de désistement'];
        }

        return ['inscriptionPossible' => true, 'motif' => 'Pour vous inscrire à cet evenement : '];

    }

    public function validationDesistement (Sortie $sortie, Participant $utilisateur){
        $personnesInscrites = $sortie->getInscriptions();
        $datedujour = new DateTime();

    if (!$personnesInscrites->contains($utilisateur)){
        return ['desistementPossible' => false, 'motif' => "Vous n'êtes pas inscrit à cet evenement !"];
    }
    if ($datedujour > $sortie->getDateHeureDebut()){
        return ['desistementPossible' => false, 'motif' => "Vous ne pouvez pas vous désinscrire d'une sortie qui a deja commencé"];
    }
    if ($datedujour > $sortie->getDateLimiteInscription()){
        return ['desistementPossible' => true, 'motif' => "Vous pouvez vous désister de cette sortie, mais les inscriptions sont cloturées donc vous ne pourrez pas changer d'avis ! "];
    }
    return ['desistementPossible' => true, 'motif' => "Vous pouvez vous désister de cette sortie"];
    }

}