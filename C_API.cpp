// ============================================================
// FICHIER : C_API.cpp
// ROLE    : Envoie l'UID du badge au serveur WEB
//           et reçoit la réponse JSON (autorisé ou refusé)
//
// PROTOCOLE UTILISÉ : HTTP (HyperText Transfer Protocol)
// C'est le même protocole que les navigateurs web utilisent
// FORMAT DE DONNÉES : JSON (JavaScript Object Notation)
// Exemple de réponse : {"uid":"08EFBA85","autorisation":true}
// ============================================================

#include "C_API.h"

// ============================================================
// CONSTRUCTEUR : C_API(const char* ip, int port)
//
// const char* ip → pointeur vers une chaîne de caractères
//                  contenant l'IP du serveur (ex: "192.168.20.20")
// int port       → numéro de port HTTP (80 par défaut)
//
// serverIP(ip)   → mémorise l'adresse IP du serveur
// serverPort(port) → mémorise le port de connexion
// ============================================================
C_API::C_API(const char* ip, int port)
  : serverIP(ip), serverPort(port) {}


// ============================================================
// FONCTION : verifierBadge(Badge badge)
//
// PARAMÈTRE : badge → l'objet Badge contenant l'UID à vérifier
// RETOURNE  : true  → badge autorisé (ouvrir la porte)
//             false → badge refusé OU erreur réseau
//
// ÉTAPES :
//   1. Connexion TCP au serveur
//   2. Envoi requête HTTP GET avec l'UID dans l'URL
//   3. Attente de la réponse (timeout 5 secondes)
//   4. Lecture et parsing du JSON reçu
//   5. Retourne le champ "autorisation"
// ============================================================
bool C_API::verifierBadge(Badge badge) {

  Serial.print("[API] Connexion au serveur ");
  Serial.println(serverIP);

  // ----------------------------------------------------------
  // ÉTAPE 1 : Tentative de connexion TCP au serveur
  // client.connect() ouvre une connexion réseau vers le serveur
  // Retourne true si la connexion réussit, false sinon
  // Si connexion impossible → on retourne false par sécurité
  // (principe : en cas de doute, on refuse l'accès)
  // ----------------------------------------------------------
  if (!client.connect(serverIP, serverPort)) {
    Serial.println("[API] ERREUR - Serveur injoignable");
    return false;
  }

  // ----------------------------------------------------------
  // ÉTAPE 2 : Construction et envoi de la requête HTTP GET
  //
  // Format de la requête envoyée au serveur :
  // GET /api/badge?uid=08EFBA85 HTTP/1.1
  // Host: 192.168.20.20
  // Connection: close
  // (ligne vide obligatoire pour terminer l'en-tête HTTP)
  //
  // ?uid= est un paramètre dans l'URL
  // Le serveur lit ce paramètre pour chercher le badge en BDD
  // ----------------------------------------------------------
  client.print("GET /api/badge?uid=");
  client.print(badge.id);
  client.println(" HTTP/1.1");
  client.print("Host: ");
  client.println(serverIP);
  client.println("Connection: close");
  client.println();  // Ligne vide = fin de l'en-tête HTTP

  Serial.print("[API] Requete envoyee pour UID : ");
  Serial.println(badge.id);

  // ----------------------------------------------------------
  // ÉTAPE 3 : Attente de la réponse du serveur
  //
  // millis() → retourne le nombre de millisecondes depuis
  //            le démarrage de l'Arduino
  // On attend maximum 5000 ms (5 secondes)
  // Si pas de réponse après 5s → timeout → on refuse l'accès
  // ----------------------------------------------------------
  unsigned long debut = millis();
  while (!client.available()) {
    if (millis() - debut > 5000) {
      Serial.println("[API] TIMEOUT - Pas de reponse du serveur");
      client.stop();  // Ferme la connexion
      return false;
    }
  }

  // ----------------------------------------------------------
  // ÉTAPE 4 : Lecture de la réponse HTTP
  //
  // Une réponse HTTP se compose de :
  //   - En-tête (headers) : informations sur la réponse
  //   - Ligne vide (\r\n)  : séparateur
  //   - Corps (body)       : le JSON qu'on cherche
  //
  // On lit ligne par ligne jusqu'à trouver la ligne vide
  // Après la ligne vide, tout ce qui suit = le corps JSON
  // ----------------------------------------------------------
  String reponse = "";
  bool corpsDebut = false;

  while (client.available()) {
    String ligne = client.readStringUntil('\n');

    // La ligne "\r" signifie qu'on a atteint la ligne vide
    // = séparateur entre en-tête et corps de la réponse HTTP
    if (ligne == "\r") {
      corpsDebut = true;
    } else if (corpsDebut) {
      reponse += ligne;  // On accumule le corps JSON
    }
  }

  // Ferme proprement la connexion TCP avec le serveur
  client.stop();

  Serial.print("[API] Reponse JSON recue : ");
  Serial.println(reponse);

  // ----------------------------------------------------------
  // ÉTAPE 5 : Parsing (décodage) du JSON reçu
  //
  // StaticJsonDocument<128> → crée un espace mémoire de 128
  //   octets pour stocker le JSON décodé
  //   128 octets suffisent pour {"uid":"XXXX","autorisation":true}
  //
  // deserializeJson() → lit la chaine JSON et la décode
  //   erreur.operator bool() retourne true si échec du décodage
  //
  // doc["autorisation"] → accède au champ "autorisation"
  //   dans le JSON décodé (comme un tableau associatif)
  //   Retourne true ou false selon la réponse du serveur
  // ----------------------------------------------------------
  StaticJsonDocument<128> doc;
  DeserializationError erreur = deserializeJson(doc, reponse);

  // Si le JSON est invalide ou mal formé → on refuse par sécurité
  if (erreur) {
    Serial.println("[API] ERREUR - JSON invalide ou mal forme");
    return false;
  }

  // Récupère la valeur true/false du champ "autorisation"
  bool autorisation = doc["autorisation"];

  if (autorisation) {
    Serial.println("[API] Badge AUTORISE par le serveur");
  } else {
    Serial.println("[API] Badge REFUSE par le serveur");
  }

  return autorisation;
}
