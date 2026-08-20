<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="assets/css/comum.css">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/icofont.min.css">
        <link rel="stylesheet" href="assets/css/login.css">

        <title>Grupo Vega</title>
        <style>
            body {
                background-color: #0A0A0A !important;
                position: relative;
                overflow: hidden;
            }
            #bg-video {
                position: absolute;
                top: 50%;
                left: 50%;
                min-width: 100%;
                min-height: 100%;
                width: auto;
                height: auto;
                z-index: -1;
                transform: translateX(-50%) translateY(-50%);
                filter: blur(8px) brightness(0.3); /* BLUR and Darken effect */
                object-fit: cover;
            }
            .form-login {
                z-index: 10;
                position: relative;
            }
            .login-card {
                background-color: rgba(26, 26, 26, 0.75) !important;
                border: 1px solid rgba(255,255,255,0.1) !important;
                box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.8);
                color: #FFFFFF !important;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
            .login-card .card-header {
                background-color: transparent !important;
                border-bottom: 1px solid rgba(255,255,255,0.1) !important;
            }
            .form-group label {
                color: #EEEEEE !important;
            }
            .form-control {
                background-color: rgba(42, 42, 42, 0.7) !important;
                border: 1px solid rgba(255,255,255,0.1) !important;
                color: #FFF !important;
            }
            .form-control:focus {
                background-color: rgba(51, 51, 51, 0.9) !important;
                border-color: #D7DF35 !important;
                color: #FFF !important;
                box-shadow: 0 0 0 0.2rem rgba(215, 223, 53, 0.25) !important;
            }
            .btn-primary {
                background-color: #D7DF35 !important;
                border-color: #D7DF35 !important;
                color: #0A0A0A !important;
                font-weight: bold !important;
            }
            .btn-primary:hover {
                background-color: #c0c72e !important;
                border-color: #c0c72e !important;
            }
        </style>
    </head>

    <body>
        <!-- Video em Loop (Placeholder - Você pode alterar o arquivo na pasta) -->
        <video autoplay loop muted playsinline id="bg-video">
            <source src="assets/video/bg.mp4" type="video/mp4">
        </video>

        <form class="form-login" action="#" method="post">
            <div class="login-card card">
                <div class="card-header" style="justify-content: center;">
                    <img src="https://vegatec.com.br/wp-content/uploads/2022/04/logo.png" alt="Grupo Vega" style="max-height: 60px;">
                </div>

                <div class="card-body">
                    <?php include(TEMPLATE_PATH . '/messages.php') ?>

                    <div class="form-group">
                        <label for="email">Usuário</label>
                        
                        <input 
                            type="text"
                            name="email" 
                            id="email" 
                            value="<?= isset($email) ? $email : '' ?>"
                            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            placeholder="Informe o usuário" 
                            autofocus
                        >

                        <div class="invalid-feedback">
                            <?= $errors['email'] ?? '' ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Senha</label>
                        
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                            placeholder="Informe a senha"
                        >

                        <div class="invalid-feedback">
                            <?= $errors['password'] ?? '' ?>
                        </div>
                    </div>
                </div>

                <div class="card-footer" style="border-top: 1px solid rgba(255,255,255,0.1); background-color: transparent;">
                    <button class="btn btn-lg btn-primary">Entrar</button>
                </div>
            </div>
        </form>
    </body>
</html>






