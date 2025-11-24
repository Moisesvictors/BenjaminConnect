<?php
session_start();
include '../app/core/db_conecta.php'; 

try {
    $sql = "
        SELECT 
            A.id_artista,
            A.nome_artistico, 
            A.bio, 
            GROUP_CONCAT(G.nome_genero_musical SEPARATOR ', ') AS generos_tocados
        FROM 
            ARTISTAS A
        LEFT JOIN 
            ARTISTAS_GENERO AG ON A.id_artista = AG.id_artista  
        LEFT JOIN 
            GENERO_MUSICAL G ON AG.id_genero_musical = G.id_genero_musical
        GROUP BY 
            A.id_artista, A.nome_artistico, A.bio
        ORDER BY 
            A.nome_artistico ASC
    ";
    
    $stmt = $pdo->query($sql);
    $artistas = $stmt->fetchAll(PDO::FETCH_ASSOC); 

} catch (PDOException $e) {
    die("Erro ao carregar estabelecimentos: " . $e->getMessage());
}

$artistas_agrupados = [];

foreach ($artistas as $row) {
    $id = $row['id_artista'];
    
    if (!isset($artistas_agrupados[$id])) {
        $artistas_agrupados[$id] = [
            'nome_artistico'=> $row['nome_artistico'],
            'bio'=> $row['bio'],
            'generos'=> []
        ];
    }

    // if ($row['generos_tocados']) {
    //     $artistas_agrupados[$id]['generos'][] = $row['generos_tocados'];
    // }
    
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artistas - Benjamin</title>
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

        h1{
            text-align:center;margin-bottom:30px;
        }
        
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
    <h1>Artistas Benjamin</h1>

    <h2>Conheça nossos talentos</h2>
    
    <div class="hall">
        <?php 
        if (empty($artistas)): ?>
            <p >Nenhum artista encontrado.</p>
        <?php else: ?>
            
            <?php foreach ($artistas as $a): 
                $estilos = htmlspecialchars($a['generos_tocados'] ?? 'Nenhum estilo cadastrado');
            ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($a['nome_artistico']); ?></h3>
                    <p class="bio"><strong>Descrição: </strong><?php echo htmlspecialchars($a['bio']); ?></p>
                    <p class="estilos"> <strong>Estilos: </strong><?php echo $estilos; ?></p>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
    <a href="../index.html" class="back-link">⬅ Voltar para a Home</a>
</body>
</html>