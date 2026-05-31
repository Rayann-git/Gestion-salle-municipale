// Base d'utilisateurs autorisés (côté client, démo)
var UTILISATEURS = {
  'test@test.com': {
    motDePasse: 'test',
    nom: 'Agent Test'
  }
};

// Vérifie les identifiants saisis dans le formulaire
function handleLogin() {
  var email = document
    .getElementById('email').value
    .trim().toLowerCase(); // Normalisation email
  var mdp = document
    .getElementById('password').value;
  var u = UTILISATEURS[email]; // Recherche utilisateur
  if (u && u.motDePasse === mdp) {
    // Connexion réussie : bascule vers le dashboard
    document.getElementById('login-view')
      .style.display = 'none';
    document.getElementById('dashboard-view')
      .style.display = 'block';

    .style.display = 'none';
  document.getElementById('login-view')
    .style.display = 'block';
}
 ajouterLog('Connexion',
 u.nom + ' connecté', 'ok'); // Trace l'accès
 mettreAJourPorte(false);
 } else {
 // Identifiants incorrects : affiche l'erreur
 document.getElementById('login-error')
 .style.display = 'block'; }
}
// Retour à l'écran de connexion
function handleLogout() {
 document.getElementById('dashboard-view')
 .style.display = 'none';