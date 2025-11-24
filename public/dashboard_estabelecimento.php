<?php
session_start();
include '../app/core/db_conecta.php'; 

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php?status=erro&msg=usuario_nao_logado"); 
    exit;
}

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
    die("Erro ao carregar artistas: " . $e->getMessage());
}

$nome_usuario = htmlspecialchars($_SESSION['nome_usuario'] ?? 'Usuário');

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Benjamin</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }
    body {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        color: #fff;
        min-height: 100vh;
    }

    .navbar {
        background: #4a008a;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; 
        transition: background 0.4s;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 10;
        position: relative;
    }

    .navbar .logo {
        font-size: 1.5em;
        font-weight: bold;
        color: #ffdd59;
        text-decoration: none;
        flex-shrink: 0;
    }
    
    .menu-toggle {
        display: none; 
        background: none;
        border: none;
        color: #ffdd59;
        font-size: 1.8em;
        cursor: pointer;
        padding: 0 10px;
    }

    .navbar-content {
        display: flex;
        align-items: center;
        flex-grow: 1;
        justify-content: flex-end;
        transition: transform 0.3s ease-in-out;
    }

    .navbar nav ul {
        list-style: none;
        display: flex;
        gap: 20px;
        margin: 0;
    }

    .navbar nav ul li a {
        color: #fff;
        text-decoration: none;
        font-size: 0.95em;
        transition: color 0.3s;
        padding: 5px 0;
    }
    
    .navbar nav ul li a:hover {
        color: #ffdd59;
    }

    .navbar nav ul li.logout-link a {
        color: #f44336;
        font-weight: bold;
    }
    
    .saudacao {
        font-size: 0.9em;
        color: #e0e0e0;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .content-container {
        padding: 40px 20px 60px;
        max-width: 1200px;
        width: 100%;
        text-align: center;
        margin: 0 auto;
    }
    .content-container h2 {
        font-size: 1.8em;
        margin-bottom: 40px;
        font-weight: 500;
        color: #fff;
    }
    .hall {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    .card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 20px;
        border-radius: 15px;
        transition: transform 0.3s, background 0.3s;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        text-align: left;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .card:hover {
        transform: scale(1.03); 
        background: rgba(255, 255, 255, 0.2);
    }
    .card h3 {
        color: #ffdd59; 
        margin-bottom: 5px;
        font-size: 1.3em;
    }
    .card .bio {
        font-size: 0.9em;
        color: #ddd;
        margin-bottom: 8px;
        font-weight: 300;
    }
    .card .estilos {
        font-size: 0.9em;
        color: #fff;
        font-weight: 500;
        margin-bottom: 20px;
    }
    .card .estilos strong {
        color: #ffdd59;
    }
    .card-actions {
        display: flex;
        justify-content: space-between; 
        align-items: center; 
        margin-top: auto; 
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .action-button {
        padding: 10px 15px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.3s, transform 0.2s;
        font-size: 0.9em;
    }
    .details-button {
        background: #ffdd59;
        color: #333;
    }
    .save-button {
        background: #6a11cb;
        color: #fff;
        border: 2px solid #6a11cb;
    }
    .details-button:hover, .save-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    
    @media (max-width: 900px) {
        .menu-toggle {
            display: block;
        }

        .navbar-content {
            width: 100%; 
            display: none; 
            flex-direction: column;
            align-items: flex-start;
            background: #4a008a;
            padding: 15px 0;
            position: absolute;
            top: 50px;
            left: 0;
            z-index: 1000;
        }

        .navbar-content.open {
            display: flex; 
        }

        .saudacao {
            margin-right: 0;
            margin-bottom: 10px;
            padding: 0 30px; 
        }
        .navbar nav ul {
            flex-direction: column;
            gap: 15px;
            width: 100%;
            padding: 0 30px;
        }
        .navbar nav ul li {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 5px;
        }
        .hall {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>
    
    <div class="navbar">
        <a href="./dashboard_estabelecimento.php" class="logo">Benjamin</a>
        
        <button class="menu-toggle" aria-label="Abrir Menu">
            ☰
        </button>
        
        <div class="navbar-content" id="navbarContent">
            <span class="saudacao">Olá, <?php echo $nome_usuario; ?>!</span>
            
            <nav>
                <ul>
                    <li><a href="#">Cadastrar Estabelecimento</a></li>
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">Sobre</a></li>
                    <li class="logout-link"><a href="./logout.php">Sair</a></li>
                </ul>
            </nav>
        </div>
    </div>
    
    <div class="content-container">
        <h2>Artistas Disponíveis para Contratação</h2>
        
        <div class="hall">
            <?php 
            if (empty($artistas)): ?>
                <p>Nenhum artista encontrado no sistema.</p>
            <?php else: ?>
                
                <?php foreach ($artistas as $a): 
                    $estilos = htmlspecialchars($a['generos_tocados'] ?? 'Nenhum estilo cadastrado');
                ?>
                    <div class="card">
                        <div>
                            <h3><?php echo htmlspecialchars($a['nome_artistico']); ?></h3>
                            
                            <p class="bio"><?php echo htmlspecialchars($a['bio']); ?></p>
                            
                            <p class="estilos">Estilos: <strong><?php echo $estilos; ?></strong></p>
                        </div>

                        <div class="card-actions">
                            <button 
                                class="action-button details-button"
                                onclick="window.location.href='perfil_artista.php?id=<?php echo $id_artista; ?>'">
                                Saber Mais
                            </button>
                            
                            <button 
                                class="action-button save-button"
                                onclick="alert('Funcionalidade de Salvar para o artista <?php echo $id_artista; ?> em desenvolvimento!')">
                                Salvar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navbarContent = document.getElementById('navbarContent');

        menuToggle.addEventListener('click', function() {
            navbarContent.classList.toggle('open');
        });
        
        const menuLinks = navbarContent.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navbarContent.classList.contains('open')) {
                    navbarContent.classList.remove('open');
                }
            });
        });
    });
</script>
</body>
</html>

</html>