<?php

require_once './app/core/models/ChannelModel.php';
require "vendor/wixel/gump/gump.class.php";

function showAllChannels()
{
    $channels = getAllChannels();
    require_once './app/core/views/channels/all.php';
}
function showAddFormChannel()
{
    require_once './app/core/views/channels/add.php';
}
function sendAddChannel()
{
    if ($_POST && $_POST["submit"]) {

        $verif = new GUMP('fr');


        $verif->validation_rules([
            'nom' => 'required|alpha_numeric|max_len,50',
            'description' => 'alpha_numeric|max_len,255',
        ]);

        $verif->set_fields_error_messages([
            'nom' => [
                'required' => 'le nom ne peut pas être vide',
                'alpha_numeric' => 'le nom ne peut contenir que des lettres et des chiffres',
                'max_len' => 'le nom ne doit pas dépasser 50 caractères',
            ],
            'description' => [
                'alpha_numeric' => 'la description ne peut contenir que des lettres et des chiffres',
                'max_len' => 'la description ne doit pas dépasser 255 caractères',
            ],
            'image' => [
                'max_len' => 'le chemin de l\'image ne doit pas dépasser 255 caractères',
            ]
        ]);

        $verif->filter_rules([
            'nom' => 'trim|htmlencode|sanitize_string',
            'description' => 'trim|htmlencode|sanitize_string',
            'image' => 'trim|htmlencode|sanitize_string',
        ]);
        $is_valid = $verif->run($_POST);

        require_once './app/core/functions.php';
        $error = Validator($_POST);

        if ($verif->errors() || $error != null) {
            var_dump($verif->get_readable_errors());
            var_dump($error);
        } else {
            var_dump($is_valid);
            $name = $_POST['nom'];
            $desc = $_POST['description'];
            if ($_FILES["image"]["name"] != null) {
                $_FILES["image"];
                $nameFile = $_FILES["image"]["name"];
                $tmpPath = $_FILES["image"]["tmp_name"];
                $uploadPath = './app/public/src/uploads/';
                $uploadFile = $uploadPath . basename(md5($nameFile));
                move_uploaded_file($tmpPath, $uploadFile);
                $picture = substr($uploadFile, 0);
                addChannel($name, $desc, $picture);
                header('Location: index.php?controller=channel&action=showAllChannels');
            } else {
                addChannel($name, $desc, './app/public/src/img/discord.svg');
                header('Location: index.php?controller=channel&action=showAllChannels');
            }
        }
    } else {
        header('Location: ./app/core/views/main/error.php');
    }
}
function showUpdateFormChannel()
{
    if ($_POST && $_POST["id"]) {

        $verif = new GUMP('fr');

        $verif->validation_rules([
            'id' => 'required|numeric',
        ]);

        $verif->set_fields_error_messages([
            'id' => [
                'required' => 'l\'id ne peut pas être vide',
                'numeric' => 'l\'id ne peut contenir que des chiffres',
            ],
        ]);

        $verif->filter_rules([
            'id' => 'trim|sanitize_numbers',
        ]);

        $is_valid = $verif->run($_POST);

        if ($verif != null) {
            $id = $_POST['id'];
            if (getByIdChannel($id) != null) {
                $channel = getByIdChannel($id);
                require_once './app/core/views/channels/update.php';
            } else {
                header('Location: index.php?controller=main&action=error');
            }
        } else {
            var_dump($verif->get_readable_errors()); // ['Field <span class="gump-field">Somefield</span> is required.']
        }
    } else {
        header('Location: index.php?controller=main&action=error');
    }
}
function sendUpdateChannel()
{
    var_dump($_POST);
    if ($_POST && $_POST["submit"]) {

        $verif = new GUMP('fr');

        $verif->validation_rules([
            'id' => 'required|numeric',
            'nom' => 'required|alpha_numeric|max_len,50',
            'description' => 'alpha_numeric|max_len,255',
            'image' => 'max_len,255',
        ]);

        $verif->set_fields_error_messages([
            'id' => [
                'required' => 'le nom ne peut pas être vide',
                'numeric' => 'le nom ne peut contenir que des chiffres',
            ],
            'nom' => [
                'required' => 'le nom ne peut pas être vide',
                'alpha_numeric' => 'le nom ne peut contenir que des lettres et des chiffres',
                'max_len' => 'le nom ne doit pas dépasser 50 caractères',
            ],
            'description' => [
                'alpha_numeric' => 'la description ne peut contenir que des lettres et des chiffres',
                'max_len' => 'la description ne doit pas dépasser 255 caractères',
            ],
            'image' => [
                'max_len' => 'le chemin de l\'image ne doit pas dépasser 255 caractères',
            ]
        ]);

        $verif->filter_rules([
            'id' => 'trim|sanitize_numbers',
            'nom' => 'trim|htmlencode|sanitize_string',
            'description' => 'trim|htmlencode|sanitize_string',
            'image' => 'trim|htmlencode|sanitize_string',
        ]);

        $is_valid = $verif->run($_POST);

        require_once './app/core/functions.php';
        $error = Validator($_POST);

        if ($verif->errors() || $error != null) {
            var_dump($verif->get_readable_errors());
            var_dump($error);
        } else {
            var_dump($is_valid);
            $id = $_POST['id'];
            $name = $_POST['nom'];
            $desc = $_POST['description'];
            if ($_FILES["image"]["name"] != null) {
                if (getByIdChannel($id) != null) {
                    $channel = getByIdChannel($id);
                    if($channel['0']['picture'] != null && $channel['0']['picture'] != './app/public/src/img/discord.svg'){
                        unlink($channel['0']['picture']);
                    }
                }
                $_FILES["image"];
                $nameFile = $_FILES["image"]["name"];
                $tmpPath = $_FILES["image"]["tmp_name"];
                $uploadPath = './app/public/src/uploads/';
                $uploadFile = $uploadPath . basename(md5($nameFile));
                move_uploaded_file($tmpPath, $uploadFile);
                $picture = substr($uploadFile, 0);
                updateChannel($id, $name, $desc, $picture);
                header('Location: index.php?controller=channel&action=showAllChannels');
            } else {
                $channel = getByIdChannel($id);
                $pic = $channel[0]['picture'];
                updateChannel($id, $name, $desc, $pic);
                header('Location: index.php?controller=channel&action=showAllChannels');
            }

        }
        } else {
            var_dump($verif->get_readable_errors());
        }
}
function sendDeleteChannel()
{
    if ($_POST && $_POST["id"]) {

        $verif = new GUMP('fr');

        $verif->validation_rules([
            'id' => 'required|numeric',
        ]);

        $verif->set_fields_error_messages([
            'id' => [
                'required' => 'l\'id ne peut pas être vide',
                'numeric' => 'l\'id ne peut contenir que des chiffres',
            ],
        ]);

        $verif->filter_rules([
            'id' => 'trim|sanitize_numbers',
        ]);

        $is_valid = $verif->run($_POST);

        if ($verif->errors()) {
            var_dump($verif->get_readable_errors());
        } else {
            $id = $_POST['id'];
            if (getByIdChannel($id) != null) {
                $channel = getByIdChannel($id);
                if($channel['0']['picture'] != null && $channel['0']['picture'] != './app/public/src/img/discord.svg'){
                    unlink($channel['0']['picture']);
                }
                deleteChannel($id);
                header('Location: index.php?controller=channel&action=showAllChannels');
            } else {
                header('Location: index.php?controller=main&action=error');
            }
        }
    } else {
        header('Location: index.php?controller=main&action=error');
    }
}
function searchChannel()
{
}
