SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


TRUNCATE TABLE `etat`;
INSERT INTO `etat` (`id`, `libelle`) VALUES
('CL', 'Fiche Sign�e, saisie cl�tur�e'),
('CR', 'Fiche cr��e, saisie en cours'),
('MP', 'Mise en paiement'),
('RB', 'Rembours�e'),
('VA', 'Valid�e');

TRUNCATE TABLE `fichefrais`;
INSERT INTO `fichefrais` (`idVisiteur`, `mois`, `nbJustificatifs`, `montantValide`, `dateModif`, `idEtat`) VALUES
('a131', '201604', 0, '0.00', '2016-09-20', 'CR'),
('a131', '201605', 0, '0.00', '2016-09-20', 'CR'),
('a131', '201606', 0, '0.00', '2016-09-20', 'CR'),
('a131', '201607', 0, '0.00', '2016-09-20', 'CR'),
('a131', '201608', 0, '0.00', '2016-09-20', 'CR'),
('a131', '201609', 0, '0.00', '2016-09-20', 'CR'),
('a131', '201610', 0, '951.00', '2016-10-04', 'CL');

TRUNCATE TABLE `fraisforfait`;
INSERT INTO `fraisforfait` (`id`, `libelle`, `montant`) VALUES
('ETP', 'Forfait Etape', '110.00'),
('KM', 'Frais Kilom�trique', '0.62'),
('NUI', 'Nuit�e H�tel', '80.00'),
('REP', 'Repas Restaurant', '25.00');

TRUNCATE TABLE `lignefraisforfait`;
INSERT INTO `lignefraisforfait` (`idVisiteur`, `mois`, `idFraisForfait`, `quantite`, `montantApplique`) VALUES
('a131', '201604', 'ETP', 0, '110.00'),
('a131', '201604', 'KM', 0, '0.62'),
('a131', '201604', 'NUI', 0, '80.00'),
('a131', '201604', 'REP', 0, '25.00'),
('a131', '201605', 'ETP', 0, '110.00'),
('a131', '201605', 'KM', 0, '0.62'),
('a131', '201605', 'NUI', 0, '80.00'),
('a131', '201605', 'REP', 0, '25.00'),
('a131', '201606', 'ETP', 0, '110.00'),
('a131', '201606', 'KM', 0, '0.62'),
('a131', '201606', 'NUI', 0, '80.00'),
('a131', '201606', 'REP', 0, '25.00'),
('a131', '201607', 'ETP', 0, '110.00'),
('a131', '201607', 'KM', 0, '0.62'),
('a131', '201607', 'NUI', 0, '80.00'),
('a131', '201607', 'REP', 0, '25.00'),
('a131', '201608', 'ETP', 0, '110.00'),
('a131', '201608', 'KM', 0, '0.62'),
('a131', '201608', 'NUI', 0, '80.00'),
('a131', '201608', 'REP', 0, '25.00'),
('a131', '201609', 'ETP', 0, '110.00'),
('a131', '201609', 'KM', 0, '0.62'),
('a131', '201609', 'NUI', 0, '80.00'),
('a131', '201609', 'REP', 0, '25.00'),
('a131', '201610', 'ETP', 1, '110.00'),
('a131', '201610', 'KM', 50, '0.62'),
('a131', '201610', 'NUI', 2, '80.00'),
('a131', '201610', 'REP', 4, '25.00');

TRUNCATE TABLE `lignefraishorsforfait`;
INSERT INTO `lignefraishorsforfait` (`id`, `idVisiteur`, `mois`, `libelle`, `date`, `montant`) VALUES
(1, 'a131', '201610', 'yey', '1996-12-12', '550.00');

TRUNCATE TABLE `visiteur`;
INSERT INTO `visiteur` (`id`, `nom`, `prenom`, `login`, `mdp`, `adresse`, `cp`, `ville`, `dateEmbauche`, `Comptable`) VALUES
('a131', 'Villachane', 'Louis', 'lvillachane', 'jux7g', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 0),
('a17', 'Andre', 'David', 'dandre', 'oppg5', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', 0),
('a55', 'Bedos', 'Christian', 'cbedos', 'gmhxd', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', 0),
('a93', 'Tusseau', 'Louis', 'ltusseau', 'ktp3s', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', 0),
('b13', 'Bentot', 'Pascal', 'pbentot', 'doyw1', '11 all�e des Cerises', '46512', 'Bessines', '1992-07-09', 0),
('b16', 'Bioret', 'Luc', 'lbioret', 'hrjfs', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', 0),
('b19', 'Bunisset', 'Francis', 'fbunisset', '4vbnd', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', 0),
('b25', 'Bunisset', 'Denise', 'dbunisset', 's1y1r', '23 rue Manin', '75019', 'paris', '2010-12-05', 0),
('b28', 'Cacheux', 'Bernard', 'bcacheux', 'uf7r3', '114 rue Blanche', '75017', 'Paris', '2009-11-12', 0),
('b34', 'Cadic', 'Eric', 'ecadic', '6u8dc', '123 avenue de la R�publique', '75011', 'Paris', '2008-09-23', 0),
('b4', 'Charoze', 'Catherine', 'ccharoze', 'u817o', '100 rue Petit', '75019', 'Paris', '2005-11-12', 0),
('b50', 'Clepkens', 'Christophe', 'cclepkens', 'bw1us', '12 all�e des Anges', '93230', 'Romainville', '2003-08-11', 0),
('b59', 'Cottin', 'Vincenne', 'vcottin', '2hoh9', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', 0),
('c14', 'Daburon', 'Fran�ois', 'fdaburon', '7oqpv', '13 rue de Chanzy', '94000', 'Cr�teil', '2002-02-11', 0),
('c3', 'De', 'Philippe', 'pde', 'gk9kx', '13 rue Barthes', '94000', 'Cr�teil', '2010-12-14', 0),
('c54', 'Debelle', 'Michel', 'mdebelle', 'od5rt', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', 0),
('d13', 'Debelle', 'Jeanne', 'jdebelle', 'nvwqq', '134 all�e des Joncs', '44000', 'Nantes', '2000-05-11', 0),
('d51', 'Debroise', 'Michel', 'mdebroise', 'sghkb', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', 0),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', 'f1fob', '14 Place d Arc', '45000', 'Orl�ans', '2005-11-12', 0),
('e24', 'Desnost', 'Pierre', 'pdesnost', '4k2o5', '16 avenue des C�dres', '23200', 'Gu�ret', '2001-02-05', 0),
('e39', 'Dudouit', 'Fr�d�ric', 'fdudouit', '44im8', '18 rue de l �glise', '23120', 'GrandBourg', '2000-08-01', 0),
('e49', 'Duncombe', 'Claude', 'cduncombe', 'qf77j', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', 0),
('e5', 'Enault-Pascreau', 'C�line', 'cenault', 'y2qdu', '25 place de la gare', '23200', 'Gueret', '1995-09-01', 0),
('e52', 'Eynde', 'Val�rie', 'veynde', 'i7sn3', '3 Grand Place', '13015', 'Marseille', '1999-11-01', 0),
('f21', 'Finck', 'Jacques', 'jfinck', 'mpb3t', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', 0),
('f39', 'Fr�mont', 'Fernande', 'ffremont', 'xs5tq', '4 route de la mer', '13012', 'Allauh', '1998-10-01', 0),
('f4', 'Gest', 'Alain', 'agest', 'dywvt', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
