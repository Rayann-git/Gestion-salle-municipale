// ============================================================
// FICHIER : AccesSalle.ino
// ROLE    : Programme principal de l'Arduino
//
// Projet  : Gestion d'accès salle municipale d'Aubers
// Candidat: Rayann Ben Romdhane (Candidat 1)
// BTS CIEL IR — LTP Saint Joseph Hazebrouck — Session 2026
//
// FONCTIONNEMENT GÉNÉRAL :
//   1. setup() s'exécute UNE SEULE FOIS au démarrage
//   2. loop() s'exécute EN BOUCLE infinie ensuite
//   3. Dans loop() : on attend un badge, on lit son UID,
//      on demande au serveur si c'est autorisé,
//      et on ouvre ou refuse selon la réponse
//
// FICHIERS DU PROJET :
//   CentraleAcces.h/.cpp → classe principale (chef d'orchestre)
//   C_LecteurRFID.h/.cpp → lecture du badge RFID
//   C_API.h/.cpp         → communication HTTP avec le serveur
//   C_Ventouse.h/.cpp    → commande du relais (pin 5)
//   Badge.h              → structure de données d'un badge
// ============================================================

#include "CentraleAcces.h"

// ============================================================
// CRÉATION DE L'OBJET PRINCIPALE
//
// CentraleAcces centrale; → crée un objet de type CentraleAcces
// Cela appelle automatiquement le constructeur CentraleAcces()
// qui crée à son tour tous les sous-objets :
//   - rfid      (C_LecteurRFID avec pins 8 et 9)
//   - api       (C_API avec IP 192.168.20.100 port 80)
//   - ventouse  (C_Ventouse avec pin 5)
// ============================================================
CentraleAcces centrale;

// ============================================================
// FONCTION setup()
// RÔLE : Initialisation au démarrage (exécutée UNE SEULE FOIS)
//
// centrale.begin() → initialise dans l'ordre :
//   - Le port série (pour voir les messages sur PC)
//   - Le réseau Ethernet (IP 192.168.10.20)
//   - Le lecteur RFID (SPI + MFRC522)
//   - Le relais ventouse (pin 5 en sortie, LOW par défaut)
// ============================================================
void setup() {
  centrale.begin();
}

// ============================================================
// FONCTION loop()
// RÔLE : Boucle principale infinie (répétée sans arrêt)
//
// ÉTAPE 1 : centrale.rfid.isCardPresent()
//   → Vérifie si un badge est devant le lecteur
//   → Si non : on recommence la boucle (attente)
//   → Si oui : on passe à l'étape 2
//
// ÉTAPE 2 : centrale.rfid.lireBadge()
//   → Lit l'UID du badge (ex: "08EFBA85")
//   → Retourne un objet Badge avec cet UID
//
// ÉTAPE 3 : badge.estValide()
//   → Vérifie que la lecture s'est bien passée
//   → Si l'UID est vide → on ignore ce badge
//
// ÉTAPE 4 : centrale.verifierAutorisation(badge)
//   → Envoie l'UID au serveur via HTTP
//   → Si autorisé → ouvre la porte 5 secondes
//   → Si refusé   → LED rouge reste allumée
//
// ÉTAPE 5 : delay(1000)
//   → Attend 1 seconde avant la prochaine lecture
//   → Evite de lire le même badge plusieurs fois d'affilée
//   → C'est l'anti-rebond logiciel
// ============================================================
void loop() {

  // ÉTAPE 1 : Y a-t-il un badge devant le lecteur ?
  if (centrale.rfid.isCardPresent()) {

    // ÉTAPE 2 : Lire l'UID du badge détecté
    Badge badge = centrale.rfid.lireBadge();

    // ÉTAPE 3 : Vérifier que la lecture est valide
    if (badge.estValide()) {

      // ÉTAPE 4 : Demander au serveur si ce badge est autorisé
      centrale.verifierAutorisation(badge);
    }

    // ÉTAPE 5 : Anti-rebond - pause 1 seconde
    delay(1000);
  }

  // Si pas de badge → on revient au début de loop() automatiquement
}
