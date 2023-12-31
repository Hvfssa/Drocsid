<?php
$pageContent = "Inscrivez-vous pour acceder à toutes nos fonctions";
$pageTitle = "Formulaire d'inscription";
?>

<section class="body_form">
    <form class="form" method="POST" action="index.php?controller=user&action=register">
        <h1>Inscription</h1>
        <span class="input-span">
            <label for="pseudo" class="label">Pseudo</label>
            <input type="text" name="pseudo">
            <label for="mdp" class="label">Mot de passe</label>
            <input type="password" name="mdp" id="password">
            <label for="mdpVerify" class="label">Entrez de nouveau votre mot de passe</label>
            <input type="password" name="mdpVerify" id="password">
        </span>
        <input class="submit" type="submit" name="envoi" value="Inscription">
        <span class="span">Vous avez déjà un compte ?
            <a href="index.php?controller=user&action=showLoginForm">Connectez-vous</a>
        </span>
        <a href="index.php?controller=main&action=homepage">Retour sur la page d'accueil</a>
    </form>
</section>