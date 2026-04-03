// ============================================================
// FICHIER : C_Ventouse.cpp
// ROLE    : Commande le relais connecté sur la pin 5
//
// RAPPEL DU CÂBLAGE :
//   Arduino pin 5 → IN du relais
//   COM du relais → 12V externe
//   NO  du relais → LED verte + Ventouse magnétique
//   NC  du relais → LED rouge
//
// LOGIQUE DU RELAIS :
//   LOW  (0V sur pin 5) → Relais ACTIF   → NO fermé → porte OUVERTE
//   HIGH (5V sur pin 5) → Relais INACTIF → NC fermé → porte FERMEE
//   Attention : c'est inversé ! LOW = actif car relais actif bas
// ============================================================

#include "C_Ventouse.h"

// ============================================================
// CONSTRUCTEUR : C_Ventouse(byte pin)
//
// PARAMÈTRE : pin → numéro de la pin Arduino du relais (5)
// RÔLE      : Mémorise le numéro de pin dans l'attribut pinRelais
//             Ne configure pas encore la pin (c'est begin())
// ============================================================
C_Ventouse::C_Ventouse(byte pin) : pinRelais(pin) {}


// ============================================================
// FONCTION : begin()
//
// pinMode(pinRelais, OUTPUT) → configure la pin 5 en SORTIE
//   OUTPUT = l'Arduino peut envoyer du courant sur cette pin
//   Sans cette ligne, la pin ne peut pas commander le relais
//
// fermer() → appelle la fonction fermer() immédiatement
//   Par sécurité : au démarrage, la porte doit être fermée
//   Evite une ouverture accidentelle au reboot de l'Arduino
// ============================================================
void C_Ventouse::begin() {
  pinMode(pinRelais, OUTPUT);
  fermer();  // Sécurité : porte fermée au démarrage
  Serial.println("[VENTOUSE] Initialisee - porte fermee");
}


// ============================================================
// FONCTION : ouvrir()
//
// digitalWrite(pinRelais, LOW) → envoie 0V sur la pin 5
//   LOW = 0 volts = signal bas
//   Ce relais est dit "actif à l'état bas" (active low)
//   Quand on envoie LOW → le relais s'active
//   → NO se ferme → 12V passe → ventouse alimentée
//   → LED verte s'allume via NO
//   → Porte déverrouillée
// ============================================================
void C_Ventouse::ouvrir() {
  digitalWrite(pinRelais, LOW);   // LOW = relais actif = porte ouverte
  Serial.println("[VENTOUSE] Porte OUVERTE - relais active (LOW)");
}


// ============================================================
// FONCTION : fermer()
//
// digitalWrite(pinRelais, HIGH) → envoie 5V sur la pin 5
//   HIGH = 5 volts = signal haut
//   Quand on envoie HIGH → le relais se désactive
//   → NC se ferme → 12V passe par NC → LED rouge allumée
//   → Ventouse non alimentée → ressort magnétique bloque
//   → Porte verrouillée
// ============================================================
void C_Ventouse::fermer() {
  digitalWrite(pinRelais, HIGH);  // HIGH = relais inactif = porte fermée
  Serial.println("[VENTOUSE] Porte FERMEE - relais inactif (HIGH)");
}
