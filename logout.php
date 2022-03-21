<?php
//Suppression de la session er redirection vers l'index
    session_start();
    session_destroy();

    header('Location: /');
