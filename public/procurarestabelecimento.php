<?php
session_start();
include '../app/core/db_conecta.php'; 

try {
    $sql = "
        SELECT 
            E.id_estabelecimento, 
            E.razao_social, 
            E.cidade_estabelecimento, 
            E.uf_estabelecimento, 
            GM.nome_genero_musical
        FROM 
            ESTABELECIMENTO E
        JOIN 
            USUARIO U ON E.id_usuario = U.id_usuario
        LEFT JOIN 
            ESTABELECIMENTO_GENERO EG ON E.id_estabelecimento = EG.id_estabelecimento
        LEFT JOIN 
            GENERO_MUSICAL GM ON EG.id_genero_musical = GM.id_genero_musical
        ORDER BY 
            E.nome_fantasia, GM.nome_genero_musical;
    ";
    
    $stmt = $pdo->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar estabelecimentos: " . $e->getMessage());
}

$estabelecimentos_agrupados = [];

foreach ($resultados as $row) {
    $id = $row['id_estabelecimento'];
    
    if (!isset($estabelecimentos_agrupados[$id])) {
        // Inicializa o novo estabelecimento
        $estabelecimentos_agrupados[$id] = [
            'razao_social'=> $row['razao_social'],
            'local'=> $row['cidade_estabelecimento'] . ' - ' . $row['uf_estabelecimento'],
            'generos'=> [] 
        ];
    }
    
    if ($row['nome_genero_musical']) {
        $estabelecimentos_agrupados[$id]['generos'][] = $row['nome_genero_musical'];
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurar Estabelecimentos - Benjamin</title>
    <style>
        *{margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif;}
        body{
          background:linear-gradient(to right,#6a11cb,#2575fc);
          color:#fff;
          min-height:100vh;
          display:flex;
          flex-direction:column;
          align-items:center;
          padding:60px 20px;
          animation:fadeIn 1s ease forwards;
        }

        h1{text-align:center;margin-bottom:30px;}
        
        .hall{
          display:flex;
          flex-wrap:wrap;
          justify-content:center;
          gap:25px;
          max-width:1000px;
        }

        .card{
          background:rgba(255,255,255,0.1);
          backdrop-filter:blur(10px);
          padding:20px;
          width:280px;
          border-radius:15px;
          transition:transform 0.3s, background 0.3s;
          box-shadow:0 6px 20px rgba(0,0,0,0.2);
        }

        .card:hover{
          transform:scale(1.05);
          background:rgba(255,255,255,0.2);
        }

        .card h3{color:#ffdd59; margin-bottom:10px;}
        .card p{font-size:0.9em; color:#e0e0e0;}

        .back-link{
          margin-top:40px;
          color:#fff;
          text-decoration:none;
          font-size:0.9em;
          transition:color 0.3s;
        }

        .back-link:hover{color:#ffdd59;}

        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
    </style>
</head>
<body>
    <h1>Estabelecimentos Cadastrados</h1>

    <div class="hall">
        <?php 
        if (empty($estabelecimentos_agrupados)): ?>
            <p>Nenhum estabelecimento encontrado.</p>
        <?php else: ?>
            
            <?php foreach ($estabelecimentos_agrupados as $e): 
                $estilos = implode(', ', $e['generos']);
                if (empty($estilos)) {
                    $estilos = 'Nenhum estilo cadastrado';
                }
            ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($e['razao_social']); ?></h3>
                    <p><strong>Local:</strong> <?php echo htmlspecialchars($e['local']); ?></p>
                    <p><strong>Estilos:</strong> <?php echo htmlspecialchars($estilos); ?></p>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <a href="./estabelecimentos.html" class="back-link">⬅ Voltar</a>
</body>
</html>