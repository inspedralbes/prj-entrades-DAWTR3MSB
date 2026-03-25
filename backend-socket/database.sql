-- Esquema de Base de Dades per a Entradas huevostyle
CREATE DATABASE IF NOT EXISTS dawtr3msb;
USE dawtr3msb;

-- Taula d'Esdeveniments
CREATE TABLE IF NOT EXISTS esdeveniments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    data_hora DATETIME NOT NULL,
    recinte VARCHAR(255) NOT NULL,
    descripcio TEXT,
    aforament INT NOT NULL,
    imatge_url VARCHAR(255)
) ENGINE=InnoDB;

-- Taula de Seients
CREATE TABLE IF NOT EXISTS seients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    esdeveniment_id INT NOT NULL,
    fila INT,
    numero INT,
    estat ENUM('lliure', 'reservat', 'venut') DEFAULT 'lliure',
    preu DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(50),
    FOREIGN KEY (esdeveniment_id) REFERENCES esdeveniments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Taula de Reserves Temporals (Per gestionar la concurrència)
-- Tot i que Socket.IO ho gestiona en memòria, és bo tenir-ho persistit
CREATE TABLE IF NOT EXISTS reserves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seient_id INT NOT NULL,
    usuari_email VARCHAR(255),
    expira_at DATETIME NOT NULL,
    FOREIGN KEY (seient_id) REFERENCES seients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Dades inicials de prova
INSERT INTO esdeveniments (nom, data_hora, recinte, descripcio, aforament) 
VALUES ('Concert TR3 Final', '2026-06-15 21:00:00', 'Palau Sant Jordi', 'El concert més gran de la temporada amb efectes especials.', 50);

-- Generar 50 seients per l'esdeveniment 1
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS populate_seients()
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= 50 DO
        INSERT INTO seients (esdeveniment_id, fila, numero, estat, preu, categoria) 
        VALUES (1, FLOOR((i-1)/10) + 1, (i-1)%10 + 1, 'lliure', 45.00, 'General');
        SET i = i + 1;
    END WHILE;
END //
DELIMITER ;

CALL populate_seients();
DROP PROCEDURE IF EXISTS populate_seients;
