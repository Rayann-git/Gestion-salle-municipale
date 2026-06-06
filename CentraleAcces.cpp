// ============================================================
// CentraleAcces.cpp
// Candidat : Rayann Ben Romdhane - BTS CIEL IR 2026
//
// Plan d'adressage réseau LAN Salle :
//   Firewall Salle    : 192.168.10.1  (passerelle)
//   Switch Salle      : 192.168.10.2
//   PC-Salle          : 192.168.10.10
//   Arduino RFID      : 192.168.10.20 (cette carte)
//   WiFi MR46         : 192.168.10.30
//   Serveur WEB       : 192.168.20.100 (Candidat 3 - réseau Mairie, via tunnel IPSec)
//
// Câblage relais (pin 5) :
//   COM → 12V externe
//   NO  → LED verte + Ventouse  (relais activé  = porte OUVERTE)
//   NC  → LED rouge             (relais inactif = porte FERMEE)
// ============================================================

#include "CentraleAcces.h"

#define SS_PIN       8
#define RST_PIN      9
#define PIN_RELAIS   5    // Relais sur pin 5

// ── CONFIGURATION RÉSEAU ─────────────────────────────────────
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };

// IP statique de l'Arduino RFID
byte ip[]      = { 192, 168, 10, 20  };

// Passerelle = Firewall Salle
byte gateway[] = { 192, 168, 10,  1  };

// Masque de sous-réseau
byte subnet[]  = { 255, 255, 255,  0  };

// IP du serveur WEB (Candidat 3) - à confirmer avec l'équipe
const char* SERVER_IP   = "192.168.20.100"; // Serveur WEB (Candidat 3) - réseau Mairie, via tunnel IPSec
const int   SERVER_PORT = 80;

// ─────────────────────────────────────────────────────────────

CentraleAcces::CentraleAcces()
  : rfid(SS_PIN, RST_PIN),
    api(SERVER_IP, SERVER_PORT),
    ventouse(PIN_RELAIS)
{}

void CentraleAcces::begin() {
  Serial.begin(9600);
  Serial.println("=== Centrale d'acces - Demarrage ===");

  // Désactive le RFID (SS pin 8 = HIGH) avant d'init Ethernet
  // Évite le conflit SPI entre shield Ethernet et MFRC522
  pinMode(SS_PIN, OUTPUT);
  digitalWrite(SS_PIN, HIGH);

  // Initialisation Ethernet avec IP statique + gateway
  Ethernet.begin(mac, ip, gateway, subnet);
  delay(2000);  // Laisse le temps au shield de s'initialiser

  Serial.print("[RESEAU] IP Arduino     : ");
  Serial.println(Ethernet.localIP());
  Serial.println("[RESEAU] Passerelle     : 192.168.10.1");
  Serial.print("[RESEAU] Serveur WEB    : ");
  Serial.println(SERVER_IP);

  rfid.begin();
  ventouse.begin();

  // Relais inactif par défaut → NC fermé → LED rouge allumée
  Serial.println("[LED] Rouge allumee - porte fermee");
  Serial.println("=== Systeme pret - En attente badge ===");
}

void CentraleAcces::verifierAutorisation(Badge badge) {
  Serial.println("--- Verification en cours ---");

  // Désactive le RFID (RST_PIN LOW) avant d'utiliser Ethernet
  // Évite le conflit SPI entre shield Ethernet et MFRC522
  digitalWrite(RST_PIN, LOW);

  bool autorise = api.verifierBadge(badge);

  // Réactive le RFID après la requête réseau
  digitalWrite(RST_PIN, HIGH);
  rfid.begin();

  if (autorise) {
    ouvrirPorte();
  } else {
    refuserAcces();
  }
}

void CentraleAcces::ouvrirPorte() {
  Serial.println("[ACCES] AUTORISE");
  ventouse.ouvrir();        // Relais activé → NO fermé → LED verte + ventouse
  Serial.println("[LED] Verte allumee via NO relais");
  delay(DUREE_OUVERTURE);   // Porte ouverte 5 secondes
  ventouse.fermer();        // Relais inactif → NC fermé → LED rouge
  Serial.println("[LED] Rouge allumee via NC relais - porte refermee");
}

void CentraleAcces::refuserAcces() {
  Serial.println("[ACCES] REFUSE - LED rouge (relais inactif NC)");
  // Relais reste inactif, LED rouge déjà allumée via NC
}
