-- #### TABELA USUARIO
CREATE TABLE IF NOT EXISTS USUARIO (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    perfil ENUM('ARTISTA', 'PRODUTOR', 'ESTABELECIMENTO') NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    esta_ativo BOOLEAN DEFAULT TRUE,
    INDEX idx_usuario_email (email)
);

-- #### TABELA GENERO MUSICAL
CREATE TABLE IF NOT EXISTS GENERO_MUSICAL (
    id_genero_musical INT PRIMARY KEY AUTO_INCREMENT,
    nome_genero_musical VARCHAR(100) UNIQUE NOT NULL
);

-- #### TABELA ARTISTAS
CREATE TABLE IF NOT EXISTS ARTISTAS (
    id_artista INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT UNIQUE NOT NULL,
    nome_artistico VARCHAR(100) NOT NULL,
    bio TEXT,
    estado VARCHAR(50),
    url_foto VARCHAR(255),
    
    FOREIGN KEY (id_usuario) 
        REFERENCES USUARIO(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
);

-- #### TABELA PRODUTOR
CREATE TABLE IF NOT EXISTS PRODUTOR (
    id_produtor INT PRIMARY KEY AUTO_INCREMENT, 
    id_usuario INT UNIQUE NOT NULL, 
    produtora VARCHAR(100),
    
    FOREIGN KEY (id_usuario) 
        REFERENCES USUARIO(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- #### TABELA ESTABELECIMENTO
CREATE TABLE IF NOT EXISTS ESTABELECIMENTO (
    id_estabelecimento INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT UNIQUE NOT NULL,
    nome_fantasia VARCHAR(100) NOT NULL,
    razao_social VARCHAR(100),
    CNPJ_CPF VARCHAR(20) UNIQUE,
    endereco VARCHAR(200),
    cidade VARCHAR(100),
    uf VARCHAR(2),
    descricao TEXT,
    referencia VARCHAR(100),
    
    FOREIGN KEY (id_usuario) 
        REFERENCES USUARIO(id_usuario)
        ON DELETE CASCADE 
        ON UPDATE CASCADE,

    INDEX idx_estabelecimento_cidade_uf (cidade, uf),
);

-- #### TABELA ARTISTAS_GENERO
CREATE TABLE IF NOT EXISTS ARTISTAS_GENERO (
    id_artista INT NOT NULL,
    id_genero_musical INT NOT NULL,
    
    PRIMARY KEY (id_artista, id_genero_musical),

    CONSTRAINT fk_artistas_genero_artista
    FOREIGN KEY (id_artista) 
    REFERENCES ARTISTAS(id_artista)
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
    
    CONSTRAINT fk_artistas_genero_musical
    FOREIGN KEY (id_genero_musical) 
    REFERENCES GENERO_MUSICAL(id_genero_musical)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);

-- #### TABELA ESTABELECIMENTO_GENERO
CREATE TABLE IF NOT EXISTS ESTABELECIMENTO_GENERO (
    id_estabelecimento INT NOT NULL,
    id_genero_musical INT NOT NULL,
    
    PRIMARY KEY (id_estabelecimento, id_genero_musical),

    CONSTRAINT fk_est_genero_estabelecimento
    FOREIGN KEY (id_estabelecimento) 
    REFERENCES ESTABELECIMENTO(id_estabelecimento)
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
    
    CONSTRAINT fk_est_genero_musical
    FOREIGN KEY (id_genero_musical) 
    REFERENCES GENERO_MUSICAL(id_genero_musical)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);