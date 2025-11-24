<?php
$mensagem_erro = '';
$mensagem_html = '';

// Verificação de usuario não logado
if (isset($_GET['msg'], $_GET['status'])) {
    $mensagem_erro = $_GET['msg'];
    $status = $_GET['status'];
    
    $cor = ($status === 'sucesso') ? '#5cb85c' : '#d9534f';
    $mensagem_html = "
        <div id='alerta-login' style='background-color: {$cor};'>
            <p>" . htmlspecialchars($mensagem_erro) . "</p>
        </div>
    ";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Benjamin</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            animation: fadeIn 1.2s ease forwards;
        }

        /* Container principal */
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 350px;
            animation: slideUp 1s ease forwards;
        }

        #alerta-login {
            position: fixed; 
            top: 0; 
            left: 50%; 
            transform: translateX(-50%); 
            z-index: 1000; 

            padding: 15px 30px; 
            margin-top: 20px;
            border-radius: 5px; 
            color: white; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            opacity: 1; 
            transition: opacity 0.5s ease-out;
        }

        .login-container h1 {
            font-size: 2em;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .login-container p {
            font-size: 0.95em;
            margin-bottom: 30px;
            color: #e0e0e0;
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 0.9em;
            margin-bottom: 6px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 10px;
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1em;
            transition: background 0.3s, box-shadow 0.3s;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 10px rgba(255, 221, 89, 0.5);
        }

        .cta-button {
            background: linear-gradient(45deg, #ffdd59, #ffc107);
            color: #333;
            padding: 12px 35px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
        }

        .cta-button:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(255, 221, 89, 0.6);
        }

        .extra-links {
            margin-top: 20px;
            font-size: 0.9em;
        }

        .extra-links a {
            color: #ffdd59;
            text-decoration: none;
            transition: color 0.3s;
        }

        .extra-links a:hover {
            color: #fff;
        }

        /* Botão Voltar */
        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: #fff;
            font-size: 0.9em;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #ffdd59;
        }

        /* Animações */
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        @keyframes slideUp {
            from {opacity: 0; transform: translateY(40px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* Responsividade */
        @media (max-width: 400px) {
            .login-container {
                width: 90%;
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <?php echo $mensagem_html; ?> 
    <div class="login-container">
        <h1>Benjamin</h1>
        <p>Bem-vindo de volta! Faça login para continuar.</p>

        <form action="../app/process/processa_login.php" method="post">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>
            </div>

            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>

            <button type="submit" class="cta-button">Entrar</button>

            <div class="extra-links">
                <p>Não tem uma conta? <a href="cadastro.html">Cadastre-se</a></p>
                <p><a href="#">Esqueceu sua senha?</a></p>
            </div>
        </form>
        <a href="../index.html" class="back-link">⬅ Voltar para a Página Inicial</a>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var alerta = document.getElementById('alerta-login');
        
        if (alerta) {
            setTimeout(function() {
                alerta.style.opacity = '0';
                setTimeout(function() {
                    alerta.remove(); // Use .remove() para sumir completamente
                }, 500); 
            }, 5000); 
        }
    });
</script>
</html>
