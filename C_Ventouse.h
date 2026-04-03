#ifndef C_VENTOUSE_H
#define C_VENTOUSE_H

#include <Arduino.h>

// ============================================================
// Classe C_Ventouse
// Commande le relais qui alimente la ventouse magnétique
// Ventouse : 12 VDC / 300 mA max
// Relais actif à l'état BAS (LOW = ventouse alimentée = porte ouverte)
// Auteur : Rayann Ben Romdhane - Candidat 1
// ============================================================

class C_Ventouse {
  private:
    byte pinRelais;  // Pin Arduino connectée au relais

  public:
    // Constructeur : définit la pin du relais
    C_Ventouse(byte pin);

    // Initialise la pin en sortie, ventouse fermée par défaut
    void begin();

    // Active la ventouse → ouvre la porte
    void ouvrir();

    // Désactive la ventouse → ferme la porte
    void fermer();
};

#endif
