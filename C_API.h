#ifndef C_API_H
#define C_API_H

#include <Arduino.h>
#include <Ethernet.h>
#include <ArduinoJson.h>
#include "Badge.h"

// ============================================================
// Classe C_API
// Envoie l'UID du badge au serveur WEB via requête HTTP GET
// Reçoit la réponse JSON : {"autorisation": true/false}
// Communication : Ethernet → Service WEB Candidat 3
// Auteur : Rayann Ben Romdhane - Candidat 1
// ============================================================

class C_API {
  private:
    EthernetClient client;  // Client TCP Ethernet
    const char* serverIP;   // IP du serveur WEB
    int serverPort;         // Port HTTP (80 par défaut)

  public:
    // Constructeur : IP et port du serveur
    C_API(const char* ip, int port);

    // Vérifie si le badge est autorisé auprès du serveur
    // Retourne true si autorisé, false si refusé ou erreur réseau
    bool verifierBadge(Badge badge);
};

#endif
