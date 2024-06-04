-- suppression des tables pré existante
DROP TABLE IF EXISTS Evenement;
DROP TABLE IF EXISTS Opportunités;
DROP TABLE IF EXISTS TypeEvenement;
DROP TABLE IF EXISTS Etapes;

-- creation des tables
CREATE TABLE Etapes (
    idEtape INT PRIMARY KEY,
    nomEtape VARCHAR(50) NOT NULL
);

CREATE TABLE TypeEvenement (
    idTypeEvenement INT PRIMARY KEY AUTO_INCREMENT,
    nomEvenement VARCHAR(50) NOT NULL
);

CREATE TABLE Opportunités (
    idOpportunité INT PRIMARY KEY AUTO_INCREMENT,
    nomOpportunité VARCHAR(255) NOT NULL,
    idEtape INT NOT NULL,
    FOREIGN KEY (idEtape) REFERENCES Etapes(idEtape)
);

CREATE TABLE Evenement (
    idEvenement INT PRIMARY KEY AUTO_INCREMENT,
    idOpportunité INT NOT NULL,
    idEtape INT NOT NULL,
    idTypeEvenement INT NOT NULL,
    dateEvenement Date NOT NULL,
    FOREIGN KEY (idOpportunité) REFERENCES Opportunités(idOpportunité),
    FOREIGN KEY (idEtape) REFERENCES Etapes(idEtape),
    FOREIGN KEY (idTypeEvenement) REFERENCES TypeEvenement(idTypeEvenement)
);

-- ajout d'une contrainte sur l'insertion d'événement pour une étapes future
DELIMITER //

CREATE TRIGGER before_insert_evenement
BEFORE INSERT ON Evenement
FOR EACH ROW
BEGIN
    DECLARE currentEtape INT;

    -- Récupérer l'étape actuelle de l'opportunité
    SELECT idEtape INTO currentEtape FROM Opportunités WHERE idOpportunité = NEW.idOpportunité;

    -- Vérifier si l'étape de l'événement est postérieure à l'étape actuelle de l'opportunité
    IF NEW.idEtape > currentEtape THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible d\'ajouter un événement pour une étape future';
    END IF;
END //

DELIMITER ;

-- insertion des données
INSERT INTO Etapes (idEtape, nomEtape) VALUES
(1, 'PROSPECT'),
(2, 'EN DISCUSSION'),
(3, 'RENDEZ-VOUS'),
(4, 'DEVIS ENVOYÉ'),
(5, 'CLOTURÉ');

INSERT INTO TypeEvenement (nomEvenement) VALUES
('Appel'),
('E-mail'),
('Rendez-vous'),
('Appel joints'),
('Rendez-vous annulé');

INSERT INTO Opportunités (nomOpportunité, idEtape) VALUES
('Opportunité A', 1),
('Opportunité B', 2),
('Opportunité C', 3);

INSERT INTO Evenement (idOpportunité, idEtape, idTypeEvenement, dateEvenement) VALUES
(1, 1, 1, '2024-05-01'),
(2, 2, 3, '2024-05-02'),  
(3, 3, 4, '2024-05-10'),  
(2, 2, 5, '2024-05-03');  