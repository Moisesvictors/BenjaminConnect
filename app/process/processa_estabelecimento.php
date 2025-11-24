<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
include '../core/db_conecta.php';

//Verificação de usuario logado
if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario']) || !isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../../public/login.php?erro=nao_autenticado&msg=Usuario não esta logado");
    exit; 
}

if($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /"); 
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$razao_social = filter_input(INPUT_POST, 'razao', FILTER_SANITIZE_SPECIAL_CHARS);
$nome_fantasia = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$endereco_estabelecimento = filter_input(INPUT_POST, 'endereco_estabelecimento', FILTER_SANITIZE_SPECIAL_CHARS);
$referencia = filter_input(INPUT_POST, 'referencia', FILTER_SANITIZE_SPECIAL_CHARS);
$cidade_estabelecimento = filter_input(INPUT_POST, 'cidade_estabelecimento', FILTER_SANITIZE_SPECIAL_CHARS);
$uf_estabelecimento = filter_input(INPUT_POST, 'uf_estabelecimento', FILTER_SANITIZE_SPECIAL_CHARS);
$generos_selecionados = filter_input(INPUT_POST, 'generos', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);


if (empty($razao_social) || empty($endereco_estabelecimento) || empty($cidade_estabelecimento) || empty($uf_estabelecimento) || empty($generos_selecionados) || empty($descricao)) {
    header("Location: ../../public/cadastroestabelecimento.php?status=erro&msg=Campos obrigatorios não preenchidos");
    exit;
}

$pdo->beginTransaction();

//Verifica se o esbelecimento ja existe no banco
try {
    $sql_verifica = "SELECT id_estabelecimento FROM estabelecimento WHERE razao_social = :razao_social";
    $stmt_verifica = $pdo->prepare($sql_verifica);
    $stmt_verifica->execute([':razao_social' => $razao_social]);

    if ($stmt_verifica->rowCount() > 0) {
        $pdo->rollBack();
        header("Location: ../../public/cadastroestabelecimento.php?status=erro&msg=estabelecimento_existente");
        exit;
    }
    
    // Insere estabelecimento no banco
    $sql_insere = "INSERT INTO ESTABELECIMENTO (id_usuario, nome_fantasia, razao_social, endereco_estabelecimento, cidade_estabelecimento, uf_estabelecimento, descricao, referencia)
                    VALUES (:id_usuario, :nome_fantasia, :razao_social, :endereco_estabelecimento, :cidade_estabelecimento, :uf_estabelecimento, :descricao, :referencia)";
    $stmt_insere = $pdo->prepare($sql_insere);
    $stmt_insere->execute([
        ':id_usuario' => $id_usuario,
        ':nome_fantasia' => $nome_fantasia,
        ':razao_social' => $razao_social,
        ':endereco_estabelecimento' => $endereco_estabelecimento,
        ':referencia' => $referencia,
        ':cidade_estabelecimento' => $cidade_estabelecimento,
        ':uf_estabelecimento' => $uf_estabelecimento,
        ':descricao' => $descricao
    ]);
    $id_estabelecimento = $pdo->lastInsertId();

    // Insere os generos selecionados
    if (!empty($generos_selecionados) && is_array($generos_selecionados)) {
    
        $sql_busca_id = "SELECT id_genero_musical FROM GENERO_MUSICAL WHERE nome_genero_musical = :nome_genero LIMIT 1";
        $stmt_busca_id = $pdo->prepare($sql_busca_id);

        $sql_insert_pivo = "INSERT INTO ESTABELECIMENTO_GENERO (id_estabelecimento, id_genero_musical) 
                            VALUES (:id_estabelecimento, :id_genero_musical)";
        $stmt_insert_pivo = $pdo->prepare($sql_insert_pivo);

        foreach ($generos_selecionados as $nome_genero) {
            
            $stmt_busca_id->execute([':nome_genero' => $nome_genero]);
            $id_genero = $stmt_busca_id->fetchColumn(); 

            if ($id_genero) {
                $stmt_insert_pivo->execute([
                    ':id_estabelecimento' => $id_estabelecimento,
                    ':id_genero_musical' => $id_genero 
                ]);
            }
        }
    }
    $pdo->commit();
    
    header("Location: ../../public/dashboard_estabelecimento.php?status=sucesso&msg=estabelecimento_criado");

    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erro ao tentar registrar estabelecimento: " . $e->getMessage());
}
?>