// Gère le clic sur "Ouvrir la porte"
async function handleOuvrirPorte() {
    var bouton = document.getElementById("btn-open");

    // Désactive le bouton pendant l'envoi (anti double-clic)
    bouton.disabled = true;
    bouton.innerHTML = "<span>Envoi en cours...</span>";

    // Appelle l'API qui contacte l'Arduino
    var res = await envoyerCommandeOuverture(
        document.getElementById("ipbyname").textContent
    );

    if (res === true) {
        // Arduino joignable → ouverture physique réelle
        mettreAJourPorte(true);
        afficheFeedback("Porte ouverte", "success");
        ajouterLog("Ouverture", "OK", "ok");

    } else if (res === null) {
        // Arduino absent → mode démo / simulation
        mettreAJourPorte(true);
        afficheFeedback("(Simulation) Porte ouverte", "success");

 } else {
 // Erreur réseau ou Arduino en échec
 afficheFeedback("Erreur Arduino", "error");
 }
 // Fermeture automatique après 5 secondes
 if (res !== false) {
 setTimeout(() => {
 mettreAJourPorte(false);
 ajouterLog("Fermeture", "Auto", "ok");
 }, 5000);
 }
}