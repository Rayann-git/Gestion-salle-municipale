#ifndef C_LECTEUR_RFID_H
#define C_LECTEUR_RFID_H

#include <Arduino.h>
#include <SPI.h>
#include <MFRC522.h>
#include "Badge.h"

// ============================================================
// FICHIER : C_LecteurRFID.h
// ROLE    : Déclare la classe qui gère le lecteur de badge
//
// Le lecteur RFID utilisé est le MFRC522
// Il communique avec l'Arduino via le protocole SPI
// SPI utilise 4 fils : MOSI, MISO, SCK, et SS (pin 10)
// Portée de lecture : 0,5 à 2 cm maximum
// ============================================================

class C_LecteurRFID {

  private:
    // --------------------------------------------------------
    // ATTRIBUT PRIVÉ : mfrc522
    // C'est l'objet qui représente physiquement le lecteur RFID
    // La librairie MFRC522 fournit toutes les fonctions
    // pour communiquer avec le module (init, lecture, arrêt)
    // --------------------------------------------------------
    MFRC522 mfrc522;

    // --------------------------------------------------------
    // ATTRIBUT PRIVÉ : ssPin
    // Numéro de la pin Arduino connectée à SS du MFRC522
    // SS = Slave Select, sert à activer/désactiver le module SPI
    // On utilise la pin 10 sur Arduino Uno
    // --------------------------------------------------------
    byte ssPin;

    // --------------------------------------------------------
    // ATTRIBUT PRIVÉ : rstPin
    // Numéro de la pin Arduino connectée à RST du MFRC522
    // RST = Reset, sert à redémarrer le module si besoin
    // On utilise la pin 9 sur Arduino Uno
    // --------------------------------------------------------
    byte rstPin;

  public:

    // --------------------------------------------------------
    // CONSTRUCTEUR : C_LecteurRFID(byte ss, byte rst)
    // PARAMÈTRES   : ss  = numéro de pin SS  (ex: 10)
    //                rst = numéro de pin RST (ex: 9)
    // RÔLE : Crée l'objet lecteur RFID en mémorisant les pins
    //        Ne démarre PAS encore le module (c'est begin())
    // Exemple : C_LecteurRFID rfid(10, 9);
    // --------------------------------------------------------
    C_LecteurRFID(byte ss, byte rst);

    // --------------------------------------------------------
    // FONCTION : begin()
    // RÔLE     : Démarre le bus SPI et initialise le MFRC522
    //            A appeler UNE SEULE FOIS dans setup()
    //            Sans cette fonction, le lecteur ne marche pas
    // --------------------------------------------------------
    void begin();

    // --------------------------------------------------------
    // FONCTION : isCardPresent()
    // RETOURNE : true  → un badge est devant le lecteur
    //            false → aucun badge détecté
    // RÔLE     : Vérifie en permanence si un badge approche
    //            On l'appelle dans la boucle loop()
    // --------------------------------------------------------
    bool isCardPresent();

    // --------------------------------------------------------
    // FONCTION : lireBadge()
    // RETOURNE : Un objet Badge contenant l'UID lu
    // RÔLE     : Lit les octets de l'UID du badge détecté
    //            Convertit ces octets en texte hexadécimal
    //            Exemple : octets [08,EF,BA,85] → "08EFBA85"
    //            Arrête la communication avec le badge après
    // A APPELER : uniquement si isCardPresent() == true
    // --------------------------------------------------------
    Badge lireBadge();
};

#endif
