#ifndef BADGE_H
#define BADGE_H

#include <Arduino.h>

// ============================================================
// FICHIER : Badge.h
// ROLE    : Définit la structure d'un badge RFID
//
// Un badge RFID possède un identifiant unique appelé UID
// Exemple d'UID : "08EFBA85" (8 caractères hexadécimaux)
// C'est ce numéro qu'on envoie au serveur pour vérification
// ============================================================

class Badge {

  public:

    // --------------------------------------------------------
    // ATTRIBUT : id
    // C'est le numéro unique du badge (comme un numéro de carte)
    // Type String = chaîne de caractères (ex: "08EFBA85")
    // --------------------------------------------------------
    String id;

    // --------------------------------------------------------
    // CONSTRUCTEUR PAR DÉFAUT
    // Un constructeur = une fonction spéciale qui s'exécute
    // automatiquement quand on crée un objet Badge
    // Ce constructeur crée un badge vide (sans UID)
    // Exemple d'utilisation : Badge monBadge;
    // --------------------------------------------------------
    Badge() : id("") {}

    // --------------------------------------------------------
    // CONSTRUCTEUR AVEC PARAMÈTRE
    // Ce constructeur crée un badge avec un UID déjà connu
    // Le ":" suivi de id(uid) s'appelle liste d'initialisation
    // Cela veut dire : "mets la valeur uid dans l'attribut id"
    // Exemple d'utilisation : Badge monBadge("08EFBA85");
    // --------------------------------------------------------
    Badge(String uid) : id(uid) {}

    // --------------------------------------------------------
    // FONCTION : estValide()
    // RETOURNE : true si le badge a un UID, false si vide
    // POURQUOI : evite de traiter un badge non lu correctement
    // Exemple : si la lecture a raté, id sera vide ("")
    //           length() == 0 → le badge n'est pas valide
    // --------------------------------------------------------
    bool estValide() {
      return id.length() > 0;
    }
};

#endif
