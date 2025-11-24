<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
include '../core/db_conecta.php';

if($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /");
    exit;
}

$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$CPF_CNPJ = filter_input(INPUT_POST, 'cnpj_cpf', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email');
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS);
$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
$cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS);
$uf = filter_input(INPUT_POST, 'uf', FILTER_SANITIZE_SPECIAL_CHARS);
$tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
$senha = filter_input(INPUT_POST, 'senha');
$confirmar_senha = filter_input(INPUT_POST, 'confirmar');

//Verifica campos vazios do formulario
if (empty($nome) || empty($email) || empty($tipo) || empty($senha) || empty($confirmar_senha) || empty($cidade) || empty($endereco) || empty($uf)) {
    header("Location: ../../public/cadastro.php?status=erro&msg=campos_obrigatorios");
    exit;
}

//Confirmação das senhas
if ($senha !== $confirmar_senha) {
    header("Location: ../../public/cadastro.html?status=erro&msg=senha_diferente");
    exit;
}
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$pdo->beginTransaction();

try {
    //Verificação se o usuario ja existe no banco
    $sql_check = "SELECT id_usuario FROM USUARIO WHERE email = :email OR cpf_cnpj = :cpf_cnpj OR telefone = :telefone";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([
        ':email' => $email,
        'cpf_cnpj' => $CPF_CNPJ,
        'telefone' => $telefone
    ]);

    if ($stmt_check->rowCount() > 0) {
        $pdo->rollBack();
        header("Location: ../../public/login.php?status=erro&msg=Usuario existente");
        exit;
    }

    //Insere o usuario no banco de dados
    $sql_insert = "INSERT INTO USUARIO (perfil, nome, email, senha, cpf_cnpj, telefone, esta_ativo, endereco, cidade, uf) 
                   VALUES (:perfil, :nome, :email, :senha, :CPF_CNPJ, :telefone, true, :endereco, :cidade, :uf)";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        ':perfil'=> $tipo,
        ':nome'=> $nome,
        ':email'=> $email,
        ':senha'=> $senha_hash,
        ':CPF_CNPJ'=> $CPF_CNPJ,
        ':telefone'=> $telefone,
        ':endereco'=> $endereco,
        ':cidade'=> $cidade,
        ':uf'=> $uf
    ]);
    $id_usuario = $pdo->lastInsertId();
    
    if ($tipo === 'ARTISTA') {
        processa_artista($pdo, $id_usuario);
    } elseif ($tipo === 'PRODUTOR') {
        processa_produtor($pdo, $id_usuario);
    }

    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['perfil'] = $tipo;
    $_SESSION['logado'] = true;
    
    // Redirecionamento para estabelecimento
    if ($tipo === 'ESTABELECIMENTO') {
        $dados_estabelecimento_temp = [
            'razao_social' => filter_input(INPUT_POST, 'razao_social', FILTER_SANITIZE_SPECIAL_CHARS),
            'nome_fantasia'=> filter_input(INPUT_POST, 'nome_fantasia', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco'=> filter_input(INPUT_POST, 'endereco_estabelecimento', FILTER_SANITIZE_SPECIAL_CHARS),
            'cidade'=> filter_input(INPUT_POST, 'cidade_estabelecimento', FILTER_SANITIZE_SPECIAL_CHARS),
            'referencia'=> filter_input(INPUT_POST, 'referencia', FILTER_SANITIZE_SPECIAL_CHARS),
            'url_imagem_local'=> filter_input(INPUT_POST, 'imagem_local', FILTER_SANITIZE_SPECIAL_CHARS),
        ]; 
        $_SESSION['estabelecimento_temp'] = $dados_estabelecimento_temp;
        header("Location: ../../public/cadastroestabelecimento.php?status=sucesso&msg=Usuario criado com sucesso !! Conclua o cadastro do estabelecimento.");
    } else {
        header("Location: ../../public/login.php?status=sucesso&msg=Usuario criado");
    }
    $pdo->commit();
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erro interno no servidor. Tente novamente: " . $e->getMessage());
}


function processa_artista($pdo, $id_usuario) {
    $nome_artistico = filter_input(INPUT_POST, 'nome_artistico', FILTER_SANITIZE_SPECIAL_CHARS);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_SPECIAL_CHARS);
    

    $sql_artista = "INSERT INTO ARTISTAS (id_usuario, nome_artistico, bio) 
                    VALUES (:id_usuario, :nome_artistico, :bio)";
    $stmt_artista = $pdo->prepare($sql_artista);
    $stmt_artista->execute([
        ':id_usuario' => $id_usuario,
        ':nome_artistico' => $nome_artistico,
        ':bio' => $bio
    ]);
    $id_artista = $pdo->lastInsertId();

    // Lista com os generos selecionados
    $generos_selecionados = filter_input(INPUT_POST, 'generos', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    
    if (!empty($generos_selecionados) && is_array($generos_selecionados)) {
    
        $sql_busca_id = "SELECT id_genero_musical FROM GENERO_MUSICAL WHERE nome_genero_musical = :nome_genero LIMIT 1";
        $stmt_busca_id = $pdo->prepare($sql_busca_id);

        $sql_insert_pivo = "INSERT INTO ARTISTAS_GENERO (id_artista, id_genero_musical) 
                            VALUES (:id_artista, :id_genero_musical)";
        $stmt_insert_pivo = $pdo->prepare($sql_insert_pivo);

        foreach ($generos_selecionados as $nome_genero) {
            
            $stmt_busca_id->execute([':nome_genero' => $nome_genero]);
            $id_genero = $stmt_busca_id->fetchColumn(); 

            if ($id_genero) {
                $stmt_insert_pivo->execute([
                    ':id_artista' => $id_artista,
                    ':id_genero_musical' => $id_genero 
                ]);
            }
        }
    }
    $pdo->commit();
    header("Location: ../../public/dashboard_estabelecimento.php?status=sucesso&msg=Usuario criado");
    exit();
}

function processa_produtor($pdo, $id_usuario) {
    $produtora = filter_input(INPUT_POST, 'produtora', FILTER_SANITIZE_SPECIAL_CHARS);

    $sql_produtor = "INSERT INTO PRODUTOR (id_usuario, produtora) 
                    VALUES (:id_usuario, :produtora)";
    $stmt_produtor = $pdo->prepare($sql_produtor);
    $stmt_produtor->execute([
        ':id_usuario' => $id_usuario,
        ':produtora' => $produtora
    ]);
}
?>