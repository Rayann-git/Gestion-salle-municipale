# Gestion d'accès — Salle Municipale d'Aubers

**BTS CIEL IR — Session 2026**
**Candidat 1 : Rayann Ben Romdhane**
**LTP Saint Joseph — Hazebrouck**

---

## Description du projet

La commune d'Aubers a inauguré sa nouvelle salle municipale **"Espace Culturel des Étangs"** le 28 janvier 2023.

Ce projet a pour objectif de moderniser la gestion d'accès de cette salle en remplaçant les clés physiques par un système de badges RFID et d'application smartphone, et de gérer les réservations via un site web.

---

## Mon périmètre — Candidat 1

Je suis responsable de la **centrale de gestion d'accès Arduino** :

- Lecture des badges RFID (module MFRC522)
- Vérification de l'autorisation via service WEB (HTTP/JSON)
- Commande de la ventouse magnétique (relais pin 5)
- Signalisation LED verte/rouge (câblée sur NO/NC du relais)
- Gestion des erreurs réseau (timeout, serveur injoignable)

---

## Architecture du code (POO C++)

```
AccesSalle/
├── AccesSalle.ino        → Programme principal (setup/loop)
├── Badge.h               → Classe de données UID badge
├── CentraleAcces.h/.cpp  → Classe principale (chef d'orchestre)
├── C_LecteurRFID.h/.cpp  → Lecture badge RFID via MFRC522
├── C_API.h/.cpp          → Requête HTTP + parsing JSON
└── C_Ventouse.h/.cpp     → Commande relais (pin 5)
```

---

## Câblage

| Composant | Pin Arduino |
|---|---|
| RFID SS (SPI) | Pin 10 |
| RFID RST | Pin 9 |
| Relais (ventouse + LEDs) | Pin 5 |

**Logique du relais :**
- `LOW` sur pin 5 → Relais actif → NO fermé → Ventouse alimentée + LED verte → **Porte OUVERTE**
- `HIGH` sur pin 5 → Relais inactif → NC fermé → LED rouge → **Porte FERMEE**

---

## Plan d'adressage réseau

| Équipement | Adresse IP |
|---|---|
| Arduino RFID | 192.168.10.20 |
| Firewall Salle (passerelle) | 192.168.10.1 |
| Serveur Intranet Mairie | 192.168.20.20 |

Communication via **tunnel IPsec** entre le réseau Salle et le réseau Mairie.

---

## Librairies utilisées

| Librairie | Auteur | Rôle |
|---|---|---|
| MFRC522 | Miguel Balboa | Communication SPI avec le lecteur RFID |
| ArduinoJson | Benoit Blanchon | Décodage de la réponse JSON du serveur |
| Ethernet | Arduino | Communication réseau TCP/IP |

---

## Flux de fonctionnement

```
Badge détecté
      ↓
Lecture UID (ex: "08EFBA85")
      ↓
Requête HTTP GET → 192.168.20.20/api/badge?uid=08EFBA85
      ↓
Réponse JSON : {"autorisation": true}
      ↓
Relais pin 5 LOW → Porte ouverte 5 secondes
      ↓
Relais pin 5 HIGH → Porte refermée
```

---

## Équipe projet

| Candidat | Périmètre |
|---|---|
| Candidat 1 — Rayann | Arduino RFID + Relais + Communication HTTP |
| Candidat 2 | Réseau + Firewall + Application Android |
| Candidat 3 | Serveur WEB public + Services WEB |
| Candidat 4 | Serveur Intranet + Base de données |
