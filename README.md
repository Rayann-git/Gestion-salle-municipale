# 🔐 Gestion d'accès — Salle Municipale d'Aubers

![BTS CIEL](https://img.shields.io/badge/BTS-CIEL%20IR-blue?style=flat-square)
![Session](https://img.shields.io/badge/Session-2026-green?style=flat-square)
![Arduino](https://img.shields.io/badge/Plateforme-Arduino%20Uno-teal?style=flat-square&logo=arduino)
![Langage](https://img.shields.io/badge/Langage-C%2B%2B%20(POO)-orange?style=flat-square)
![Statut](https://img.shields.io/badge/Statut-Fonctionnel-brightgreen?style=flat-square)

**Candidat 1 — Rayann **

---

## 📌 Présentation du projet

La commune d'Aubers a inauguré sa salle municipale **"Espace Culturel des Étangs"** le 28 janvier 2023.

L'objectif est de **moderniser la gestion d'accès** en remplaçant les clés physiques par :
- Des **badges RFID** pour les utilisateurs
- Une **application Android** pour l'ouverture à distance
- Un **site web** pour la gestion des réservations

Ce dépôt contient **mon périmètre : la centrale Arduino de contrôle d'accès physique**.

---

## 🏗️ Architecture globale du projet (4 candidats)

```
┌─────────────────────────────────────────────────────────────┐
│                    Réseau Salle (192.168.10.0/24)           │
│                                                             │
│   [Candidat 1]          [Candidat 2]                        │
│   Arduino Uno           Switch + Firewall                   │
│   192.168.10.20    ──── Stormshield SN-XS-170               │
│   RFID + Ventouse       192.168.10.1 (GW)                   │
│                              │                              │
└──────────────────────────────┼──────────────────────────────┘
                               │ Tunnel IPsec
┌──────────────────────────────┼──────────────────────────────┐
│                    Réseau Mairie (192.168.20.0/24)          │
│                              │                              │
│   [Candidat 3]          [Candidat 4]                        │
│   Serveur WEB public    Serveur Intranet                    │
│   192.168.20.100        Base de données MySQL               │
│   Linux/PHP/JSON        Site gestionnaire                   │
└─────────────────────────────────────────────────────────────┘
```

| Candidat | Rôle |
|---|---|
| **Candidat 1 — Rayann** | **Arduino + RFID + Ventouse + Communication HTTP** |
| Candidat 2 | Réseau + Firewall + Application Android |
| Candidat 3 | Serveur WEB public + Services WEB JSON |
| Candidat 4 | Serveur Intranet + Base de données MySQL |

---

## ⚙️ Mon périmètre — Candidat 1

Je suis responsable de la **centrale de gestion d'accès physique Arduino** :

| Fonction | Détail |
|---|---|
| 📡 Lecture badge | Module RFID MFRC522 via SPI |
| 🌐 Vérification autorisation | Requête HTTP GET → serveur 192.168.20.100 |
| 🔒 Commande porte | Ventouse magnétique 12V via relais |
| 🟢🔴 Signalisation | LED verte (accès OK) / LED rouge (refus) |
| ⚠️ Gestion erreurs | Timeout réseau, serveur injoignable |

---

## 🔌 Matériel utilisé

| Composant | Référence | Détail |
|---|---|---|
| Microcontrôleur | Arduino Uno (ATmega328P) | 16 MHz, 32 KB Flash, 2 KB SRAM |
| Lecteur RFID | MFRC522 | Interface SPI, fréquence 13.56 MHz |
| Shield réseau | W5100 Ethernet Shield | TCP/IP hardware, MAC intégrée |
| Ventouse | 12V DC, Imax 300 mA | Normalement fermée (fail-secure) |
| Signalisation | LED rouge + LED verte | Visibles à 1 m minimum |

---

## 🗂️ Architecture du code (POO C++)

```
AccesSalle/
├── AccesSalle.ino          → Point d'entrée : setup() + loop()
├── Badge.h                 → Structure données badge (UID)
├── CentraleAcces.h/.cpp    → Classe principale (orchestration)
├── C_LecteurRFID.h/.cpp    → Lecture badge via MFRC522 (SPI)
├── C_API.h/.cpp            → Requête HTTP GET + parsing JSON
└── C_Ventouse.h/.cpp       → Commande relais (ouvrir/fermer)
```

**Diagramme de classes :**

```
         ┌─────────────────────────────┐
         │       CentraleAcces         │
         │─────────────────────────────│
         │ +rfid : C_LecteurRFID       │
         │ +api  : C_API               │
         │ +ventouse : C_Ventouse      │
         │ +DUREE_OUVERTURE : ulong    │
         │─────────────────────────────│
         │ +begin() : void             │
         │ +verifierAutorisation()     │
         │ +ouvrirPorte() : void       │
         │ +refuserAcces() : void      │
         └──────┬──────────┬───────────┘
                │          │          │
    ┌───────────┘   ┌──────┘   ┌──────┘
    ▼               ▼          ▼
┌──────────┐  ┌──────────┐  ┌──────────┐
│C_Lecteur │  │  C_API   │  │C_Ventouse│
│   RFID   │  │          │  │          │
│ ssPin:8  │  │serverIP  │  │pinRelais │
│ rstPin:9 │  │serverPort│  │          │
│ begin()  │  │verifier  │  │ ouvrir() │
│isPresent │  │Badge()   │  │ fermer() │
│lireBadge │  │          │  │          │
└────┬─────┘  └──────────┘  └──────────┘
     │ «crée»
     ▼
┌──────────┐
│  Badge   │
│  +id     │
│ estValide│
└──────────┘
```

---

## 🔧 Câblage

| Composant | Pin Arduino | Remarque |
|---|---|---|
| RFID SS (SDA/NSS) | **Pin 8** | ⚠️ Déplacé de 10 → 8 (conflit SPI) |
| RFID RST | Pin 9 | |
| Shield Ethernet SS | Pin 10 | Réservée W5100 |
| Relais (ventouse + LEDs) | Pin 5 | LOW = ouvert, HIGH = fermé |

**Logique du relais :**
```
Pin 5 LOW  → Relais actif → Contact NO fermé → Ventouse 12V + LED verte → PORTE OUVERTE
Pin 5 HIGH → Relais inactif → Contact NC fermé → LED rouge → PORTE FERMÉE
```

---

## 🌐 Plan d'adressage réseau

| Équipement | Adresse IP | Rôle |
|---|---|---|
| Arduino | 192.168.10.20 | Centrale d'accès |
| Passerelle (Firewall) | 192.168.10.1 | Routage inter-réseaux |
| Serveur WEB | 192.168.20.100 | API d'autorisation (réseau Mairie, via tunnel IPSec) |
| Masque | 255.255.255.0 | /24 |

---

## 🔄 Flux de fonctionnement

```
┌─────────────────────────────────────────────────┐
│                  BOUCLE PRINCIPALE               │
│                                                  │
│  Badge détecté par MFRC522                       │
│         │                                        │
│         ▼                                        │
│  Lecture UID (ex: "F9A7A6E2")                    │
│         │                                        │
│         ▼                                        │
│  RST_PIN LOW (désactiver RFID)                   │
│  GET /api/badge?uid=F9A7A6E2 → 192.168.20.100   │
│         │                                        │
│         ├──── {"autorisation": true}  ────────►  │
│         │     Relais LOW → Porte ouverte 5s      │
│         │     LED verte allumée                  │
│         │     Relais HIGH → Porte fermée         │
│         │                                        │
│         └──── {"autorisation": false} ────────►  │
│               LED rouge (3 clignotements)        │
│               Porte reste fermée                 │
│                                                  │
│  RST_PIN HIGH (réactiver RFID)                   │
│  rfid.begin() → retour attente badge             │
└─────────────────────────────────────────────────┘
```

---

## 📦 Librairies utilisées

| Librairie | Auteur | Version | Rôle |
|---|---|---|---|
| `MFRC522` | Miguel Balboa | 1.4.x | Communication SPI avec le lecteur RFID |
| `ArduinoJson` | Benoit Blanchon | 6.x | Décodage de la réponse JSON du serveur |
| `Ethernet` | Arduino | Built-in | Communication réseau TCP/IP (W5100) |

---

## 🐛 Problèmes rencontrés et solutions

### Bug 1 — Mauvaise adresse IP du serveur
```cpp
// ❌ AVANT : adresse erronée
const char* SERVER_IP = "192.168.20.20";

// ✅ APRÈS : serveur intranet Mairie, accessible via le tunnel IPSec
const char* SERVER_IP = "192.168.20.100";
```

### Bug 2 — Shield Ethernet non initialisé (IP = 0.0.0.0)
```cpp
// ❌ AVANT : délai insuffisant pour l'initialisation
delay(1000);

// ✅ APRÈS : délai augmenté pour laisser le W5100 démarrer
delay(2000);
```

### Bug 3 — Conflit SPI (Shield Ethernet + MFRC522)
**Problème** : les deux modules utilisaient la pin 10 (SS) → collision sur le bus SPI → Arduino freeze.

**Solution matérielle** : déplacer le fil SDA/NSS du MFRC522 de la pin 10 → **pin 8**.

**Solution logicielle** :
```cpp
// Au démarrage : désactiver RFID avant init Ethernet
pinMode(SS_PIN, OUTPUT);
digitalWrite(SS_PIN, HIGH);   // RFID désactivé
Ethernet.begin(mac, ip, gateway, subnet);
delay(2000);

// Avant chaque requête réseau : désactiver RFID
digitalWrite(RST_PIN, LOW);
bool autorise = api.verifierBadge(badge);
digitalWrite(RST_PIN, HIGH);
rfid.begin();  // Réinitialiser le RFID après la requête
```

### Bug 4 — Serial.print sans retour à la ligne
```cpp
// ❌ AVANT
Serial.print("[RESEAU] Passerelle : 192.168.10.1");

// ✅ APRÈS
Serial.println("[RESEAU] Passerelle : 192.168.10.1");
```

---

## ✅ Tests réalisés (Cahier de recette)

| Test | Description | Résultat |
|---|---|---|
| TU-01 | Initialisation Arduino + Shield Ethernet | ✅ RÉUSSI |
| TU-02 | Lecture UID badge RFID | ✅ RÉUSSI |
| TU-03 | Requête HTTP vers serveur | ✅ RÉUSSI |
| TU-04 | Parsing réponse JSON | ✅ RÉUSSI |
| TU-05 | Ouverture ventouse (badge autorisé) | ✅ RÉUSSI |
| TU-06 | Refus accès (badge non autorisé) | ✅ RÉUSSI |
| TU-07 | Gestion timeout réseau | ✅ RÉUSSI |
| TU-08 | Signalisation LED | ✅ RÉUSSI |
| TU-09 | Réinitialisation après erreur | ✅ RÉUSSI |
| TU-10 | Scénario complet d'intégration | ✅ RÉUSSI |

---

## 🚀 Installation et déploiement

### Prérequis
- Arduino IDE 2.x
- Librairies : `MFRC522`, `ArduinoJson`, `Ethernet` (gestionnaire de librairies Arduino IDE)

### Téléversement
1. Cloner ce dépôt
2. Ouvrir `AccesSalle.ino` dans Arduino IDE
3. Sélectionner **Arduino Uno** comme carte
4. Vérifier les pins dans `CentraleAcces.cpp` :
   ```cpp
   #define SS_PIN  8   // SS MFRC522
   #define RST_PIN 9   // RST MFRC522
   ```
5. Téléverser sur la carte

### Serveur de test (développement)
Un serveur Python minimal est fourni pour tester sans infrastructure complète :
```bash
py -3.12 serveur_test.py   # Répond {"autorisation": true} à toutes les requêtes
```

---

## 📋 Cahier des charges — Conformité

| Exigence | Statut |
|---|---|
| Lecture badge RFID 13.56 MHz | ✅ |
| Communication HTTP avec API JSON | ✅ |
| Ouverture porte pendant 5 secondes | ✅ |
| Signalisation LED visible à 1 m | ✅ |
| Gestion erreur réseau sans blocage | ✅ |
| Code versionné et commenté | ✅ |

---

*BTS CIEL IR — Session 2026 — LTP Saint Joseph Hazebrouck*
