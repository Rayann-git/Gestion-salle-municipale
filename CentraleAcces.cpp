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
//   Serveur WEB       : 192.168.10.100 (Candidat 3 - à confirmer)
//
// Câblage relais (pin 5) :
//   COM → 12V externe
//   NO  → LED verte + Ventouse  (relais activé  = porte OUVERTE)
//   NC  → LED rouge             (relais inactif = porte FERMEE)
// ============================================================

#include "CentraleAcces.h"

#define SS_PIN      10
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
const char* SERVER_IP   = "192.168.20.20";  // Serveur Intranet Mairie (Candidat 4)
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

  // Initialisation Ethernet avec IP statique + gateway
  Ethernet.begin(mac, ip, gateway, subnet);
  delay(1000);

  Serial.print("[RESEAU] IP Arduino     : ");
  Serial.println(Ethernet.localIP());
  Serial.print("[RESEAU] Passerelle     : 192.168.10.1");
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
  bool autorise = api.verifierBadge(badge);
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
