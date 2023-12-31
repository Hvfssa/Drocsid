<?php

require_once("./vendor/wixel/gump/gump.class.php");

function showRegisterForm()
{
    require_once("./app/core/views/users/register.php");
}

function register()
{
    require_once("./app/core/models/UserModel.php");

    $regex = "/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-])(?=.*?[0-9])/";

    $gump = new GUMP();

    $gump->validation_rules([
        'pseudo' => 'required|max_len,50|alpha_numeric_dash',
        'mdp' => 'required|max_len,255|min_len,8|regex,' . $regex,
        'mdpVerify' => 'required|max_len,255|equalsfield,mdp'
    ]);

    $gump->set_fields_error_messages([
        'pseudo' => [
            'required' => 'Veuillez remplir le champ pseudo.',
            'max_lengh' => 'Le pseudo ne peut faire que 50 charactère de long.',
            'alpha_numeric_dash' => 'Le pseudo ne peut contenir que des charactères aphanumériques, des dash et des underscores.'
        ],
        'mdp' => [
            'required' => 'Veuillez remplir ce champ.',
            'min_len' => 'Le mot de passe doit faire au moins 8 charactères'
        ],
        'mdpVerify' => [
            'required' => 'Veuillez remplir ce champ',
            'equalsfield,mdp' => 'Les mots de passe doivent etre identiques.'
        ]

    ]);

    $gump->filter_rules([
        'pseudo' => 'trim|htmlencode',
        'mdp' => 'trim|htmlencode',
        'mdpVerify' => 'trim|htmlencode',
    ]);

    $valid_data = $gump->run($_POST);

    if ($gump->errors()) {
        $error = $gump->get_readable_errors();
    } else {
        if (isset($_POST)) {
            $pseudo = $valid_data["pseudo"];
            $alreadyExists = getByOneUser($pseudo);

            if ($alreadyExists === false) {
                $mdp = password_hash($valid_data["mdp"], PASSWORD_ARGON2I);
                addUser($pseudo, $mdp);


            } else {
                $error = "Le nom d'utilisateur est déja utilisé";
            }
        }
        return $error;
    }
}

function showLoginForm()
{
    require_once './app/core/views/users/login.php';
}

function login()
{
    require_once("./app/core/models/UserModel.php");
    $connectGump = new GUMP();

    $connectGump->validation_rules([
        'pseudo' => ['required' => 'Veuillez remplir le champ pseudo.'],
        'mdp' => ['required' => 'Veuillez remplir le champ pseudo.'],

    ]);

    $connectGump->set_fields_error_messages([
        'pseudo' => ['required' => 'Veuillez remplir le champ pseudo.'],
        'mdp' => ['required' => 'Veuillez remplir ce champ.'],
    ]);

    $connectGump->filter_rules([
        'pseudo' => 'trim|htmlencode',
        'mdp' => 'trim|htmlencode',
    ]);

    $valid_data = $connectGump->run($_POST);

    if ($connectGump->errors()) {
        $error = $connectGump->get_readable_errors();
        // redirect form
    } else {
        $pseudo = $valid_data["pseudo"];

        $alreadyExists = getByOneUser($pseudo);

        $verify = password_verify($valid_data["mdp"], $alreadyExists['passwd']);

        if ($alreadyExists != false  && $verify === true) {

            // creation session
            // redirection page

            setcookie("isConnected", true, time()+30);
            $jsonUserInfo = json_encode(["id" => $alreadyExists["userId"], "pseudo" => $alreadyExists["pseudo"]]);
            setcookie("User", $jsonUserInfo, time()+40);
            
            header("Location: ");
        } else {
            $error = "Le nom d'utilisateur ou le mot de passe n'est pas correcte";
        }

        return $error;
    }
}

function signOut()
{
    setcookie("isConnected", "", time()-3600);
    setcookie("User", "", time()-3600);
    
    header("Location : index.php?controller=User&action=showRegisterForm");
}

function showDataUpdateForm(){
    require_once("./app/core/views/users/formUserUpdate.php");
}

function showPasswordRecoveryForm(){
    require_once("./app/core/views/users/passwordRecoveryForm.php");
}

function passwordRecovery(){
    require_once("./app/core/models/UserModel.php");

    $gump = new GUMP();

    $gump->validation_rules([
        'pseudo' => 'required'
    ]);

    $gump->set_fields_error_messages([
        'pseudo' => [
            'required' => 'Veuillez remplir le champ pseudo.'
        ]
    ]);

    $gump->filter_rules([
        'pseudo' => 'trim|htmlencode'
    ]);

    $valid_data = $gump->run($_POST);

   if ($gump->errors()) {
    $error = $gump->get_readable_errors();
   }
   else{
       $exists = getByOneUser($valid_data["pseudo"]);
    
       if ($exists != false) {

        session_start();
        $_SESSION["userID"] = $exists["userId"];
        session_destroy();

        header("Location: index.php?controller=User&action=showPassowrdUpdateForm");
       }
       else{
           $error = "Le pseudo n'est pas valide";
           var_dump($error);
       }

   }
}

function showUpdatePassForm()
{
    require_once("./app/core/models/UserModel.php");

    getByIdUser($id);

    require_once("./app/core/views/users/UpdatePass.php"); 
}

function updatePassword(){
    require_once("./app/core/models/UserModel.php");

    $regex = "/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-])(?=.*?[0-9])/";

    $gump = new GUMP();

    $gump->validation_rules([
        'newMdp' => 'required|max_len,255|min_len,8|regex,'.$regex,
        'newMdpConfirm' => 'required|max_len,255|equalsfield,mdp'
    ]);

    $gump->set_fields_error_messages([
        'newMdp' => [
            'required' => 'Veuillez remplir ce champ.',
            'min_len' => 'Le mot de passe doit faire au moins 8 charactères'
        ],
        'newMdpConfirm' => [
            'required' => 'Veuillez remplir ce champ',
            'equalsfield,mdp' => 'Les mots de passe doivent etre identiques.'
        ]

    ]);

    $gump->filter_rules([
        'newMdp' => 'trim|htmlencode',
        'newMdpConfirm' => 'trim|htmlencode',
    ]);

    $valid_data = $gump->run($_POST);

    if ($gump->errors()) {
        $error = $gump->get_readable_errors();
    } else {
        $mdp = password_hash($valid_data["mdp"], PASSWORD_ARGON2I);

        // updatePasswordById();

    }

}

function modify()
{
    require_once("./app/core/models/UserModel.php");

    // controlle données

    // modification BDD

    // redirection ou message erreur
    updateUser($id, $pseudo, $mdp, $pfpic);
}

function showProfil()
{
    require_once './app/core/views/users/profil.php';
}
