<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="assets/css/comum.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/icofont.min.css">
        <link rel="stylesheet" href="assets/css/template.css?v=<?php echo time(); ?>">

        <title>Grupo Vega</title>
    </head>

    <body class="hide-sidebar">
        <header class="header">
            <div class="logo" style="justify-content: center; background-color: #1A1A1A; border-right: 1px solid #333; padding: 10px;">
                <img src="https://vegatec.com.br/wp-content/uploads/2022/04/logo.png" alt="Grupo Vega" style="max-height: 45px;">
            </div>

            <div class="menu-toggle mx-3">
                <i class="icofont-navigation-menu"></i>
            </div>

            <div class="spacer"></div>

            <div class="dropdown">
                <div class="dropdown-button">
                    <img
                        class="avatar"
                        src="<?= "http://www.gravatar.com/avatar.php?gravatar_id=" 
                            . md5(strtolower(trim($_SESSION['user']->email))) ?>" 
                        alt="user"
                    >

                    <span class="ml-3">
                        <?= $_SESSION['user']->name ?>
                    </span>

                    <i class="icofont-simple-down mx-2"></i>
                </div>

                <div class="dropdown-content">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="logout.php">
                                <i class="icofont-logout mr-2"></i>
                                Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>