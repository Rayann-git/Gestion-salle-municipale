#ifndef CENTRALE_ACCES_H
#define CENTRALE_ACCES_H

#include <Arduino.h>
#include <Ethernet.h>
#include "C_LecteurRFID.h"
#include "C_API.h"
#include "C_Ventouse.h"
#include "Badge.h"

// ============================================================
// Classe CentraleAcces - Classe principale
// Orchestre : RFID → API → Relais (ventouse + LEDs 12V)
// Les LEDs sont câblées directement sur NO/NC du relais
// Candidat : Rayann Ben Romdhane - BTS CIEL IR 2026
// ============================================================

class CentraleAcces {
  public:
    C_LecteurRFID rfid;      // Lecture badge RFID
    C_API         api;       // Vérification autorisation serveur WEB
    C_Ventouse    ventouse;  // Commande relais (pin 5)

    static const unsigned long DUREE_OUVERTURE = 5000; // 5 secondes

    CentraleAcces();
    void begin();
    void verifierAutorisation(Badge badge);
    void ouvrirPorte();
    void refuserAcces();
};

#endif
