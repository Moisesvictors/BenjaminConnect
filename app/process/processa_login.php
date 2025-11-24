<?php
session_start();
include '../core/db_conecta.php'; 

$email = $_POST['email'] ?? '';
$senha_digitada = $_POST['senha'] ?? '';

if (empty($email) || empty($senha_digitada)) {
    header("Location: ../../public/login.php?status=erro&msg=Campos vazios");
    exit;
}

try {
    $sql = "SELECT id_usuario, nome, perfil, senha FROM USUARIO WHERE email = :email";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    //Verifica se o usuário foi encontrado e se a senha está correta
    if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
        
        $_SESSION['logado'] = true;
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome_usuario'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['perfil'];

        if($_SESSION['perfil'] === 'ESTABELECIMENTO'){
            header("Location: ../../public/dashboard_estabelecimento.php?status=sucesso&msg=Usuario logado com sucesso"); 
        }else if($_SESSION['perfil'] === 'ARTISTA' OR $_SESSION['perfil'] === 'PRODUTOR'){
            header("Location: ../../public/dashboard_estabelecimento.php?status=sucesso&msg=usuario_logado"); 
        }
        
        exit;

    } 
    //Se os dados digitados estiverem errados
    else {
        header("Location: ../../public/login.php?status=erro&msg=Usuario Inválido");
        exit;
    }

} catch (PDOException $e) {
    error_log("Erro de login: " . $e->getMessage()); 
    header("Location: ../../public/login.php?status=erro&msg=Erro com o banco");
    exit;
}
?>