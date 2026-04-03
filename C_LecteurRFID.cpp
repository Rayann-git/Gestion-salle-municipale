// ============================================================
// FICHIER : C_LecteurRFID.cpp
// ROLE    : Contient le code de chaque fonction déclarée
//           dans C_LecteurRFID.h
// ============================================================

#include "C_LecteurRFID.h"

// ============================================================
// CONSTRUCTEUR : C_LecteurRFID(byte ss, byte rst)
//
// Le ":" après les parenthèses s'appelle liste d'initialisation
// C'est la façon C++ d'initialiser les attributs d'une classe
//
// mfrc522(ss, rst) → dit à la librairie MFRC522 quelles pins
//                    utiliser pour SS et RST
// ssPin(ss)        → mémorise la valeur de ss dans ssPin
// rstPin(rst)      → mémorise la valeur de rst dans rstPin
// ============================================================
C_LecteurRFID::C_LecteurRFID(byte ss, byte rst)
  : mfrc522(ss, rst), ssPin(ss), rstPin(rst) {}


// ============================================================
// FONCTION : begin()
//
// SPI.begin() → démarre le bus SPI de l'Arduino
//               SPI est le protocole de communication entre
//               l'Arduino et le MFRC522 (4 fils)
//
// mfrc522.PCD_Init() → initialise physiquement le lecteur
//                      PCD = Proximity Coupling Device
//                      (nom technique du lecteur RFID)
//
// Serial.println() → affiche un message dans le moniteur
//                    série d'Arduino IDE (pour déboguer)
// ============================================================
void C_LecteurRFID::begin() {
  SPI.begin();
  mfrc522.PCD_Init();
  Serial.println("[RFID] Lecteur initialise - en attente badge...");
}


// ============================================================
// FONCTION : isCardPresent()
//
// PICC_IsNewCardPresent() → vérifie si un badge émet un signal
//                           PICC = carte RFID (le badge)
//                           Retourne true si signal détecté
//
// PICC_ReadCardSerial()   → lit les données de base du badge
//                           Nécessaire avant de lire l'UID
//                           Retourne true si lecture réussie
//
// Le && signifie "ET" : les deux conditions doivent être vraies
// Si l'une échoue, la fonction retourne false
// ============================================================
bool C_LecteurRFID::isCardPresent() {
  return mfrc522.PICC_IsNewCardPresent() &&
         mfrc522.PICC_ReadCardSerial();
}


// ============================================================
// FONCTION : lireBadge()
//
// mfrc522.uid.size       → nombre d'octets dans l'UID
//                          (généralement 4 octets)
//
// mfrc522.uid.uidByte[i] → valeur de l'octet numéro i
//                          Exemple : octet 0 = 0x08
//
// String(valeur, HEX)    → convertit un octet en texte hexa
//                          Exemple : 0x08 → "8", 0xEF → "ef"
//
// if (octet < 0x10)      → si l'octet est < 16 en décimal
//   uid += "0"           → on ajoute un "0" devant
//                          car on veut "08" et pas juste "8"
//
// toUpperCase()          → met en majuscules ("ef" → "EF")
//
// PICC_HaltA()           → dit au badge "communication terminée"
// PCD_StopCrypto1()      → arrête le chiffrement SPI
//                          Important : évite les conflits
//
// Badge(uid)             → crée et retourne un objet Badge
//                          avec l'UID qu'on vient de lire
// ============================================================
Badge C_LecteurRFID::lireBadge() {
  String uid = "";

  // Parcourt chaque octet de l'UID du badge
  for (byte i = 0; i < mfrc522.uid.size; i++) {

    // Ajoute "0" si l'octet est inférieur à 16
    // Pour avoir toujours 2 caractères par octet
    if (mfrc522.uid.uidByte[i] < 0x10) {
      uid += "0";
    }

    // Convertit l'octet en texte hexadécimal et l'ajoute
    uid += String(mfrc522.uid.uidByte[i], HEX);
  }

  // Met tout en majuscules : "08efba85" → "08EFBA85"
  uid.toUpperCase();

  // Termine la communication avec le badge
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();

  Serial.print("[RFID] Badge detecte - UID : ");
  Serial.println(uid);

  // Crée un objet Badge avec l'UID lu et le retourne
  return Badge(uid);
}
