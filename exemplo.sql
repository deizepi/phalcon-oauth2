-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.38-MariaDB


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema exemplo
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ exemplo;
USE exemplo;

--
-- Table structure for table `exemplo`.`autenticacao`
--

DROP TABLE IF EXISTS `autenticacao`;
CREATE TABLE `autenticacao` (
  `autenticacao` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`autenticacao`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`autenticacao`
--

/*!40000 ALTER TABLE `autenticacao` DISABLE KEYS */;
INSERT INTO `autenticacao` (`autenticacao`) VALUES 
 ('cliente_credenciado'),
 ('codigo_autorizacao'),
 ('senha'),
 ('token_atualizacao');
/*!40000 ALTER TABLE `autenticacao` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`escopo`
--

DROP TABLE IF EXISTS `escopo`;
CREATE TABLE `escopo` (
  `escopo` varchar(100) NOT NULL DEFAULT '',
  `padrao` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`escopo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`escopo`
--

/*!40000 ALTER TABLE `escopo` DISABLE KEYS */;
INSERT INTO `escopo` (`escopo`,`padrao`) VALUES 
 ('email',1),
 ('padrao',1),
 ('perfil',0);
/*!40000 ALTER TABLE `escopo` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth`
--

DROP TABLE IF EXISTS `oauth`;
CREATE TABLE `oauth` (
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `chave_secreta` varchar(100) NOT NULL DEFAULT '',
  `expiracao` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth`
--

/*!40000 ALTER TABLE `oauth` DISABLE KEYS */;
INSERT INTO `oauth` (`identificador`,`chave_secreta`,`expiracao`) VALUES 
 ('cliente','+yyat69!S@:pjZ;Et:tWu&]XRw6CAX&e-}ez?|tG=c-`1[zf&S1^R*\']~HUZoU>_)\\T6o#xa}dv;^+@^W\'m|T>p*u!C8OQ3q7gg-','2020-05-14 22:03:00');
/*!40000 ALTER TABLE `oauth` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth_autenticacao`
--

DROP TABLE IF EXISTS `oauth_autenticacao`;
CREATE TABLE `oauth_autenticacao` (
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `autenticacao` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`identificador`,`autenticacao`),
  KEY `FK_oauth_autenticacao_autenticacao` (`autenticacao`),
  CONSTRAINT `FK_oauth_autenticacao_autenticacao` FOREIGN KEY (`autenticacao`) REFERENCES `autenticacao` (`autenticacao`),
  CONSTRAINT `FK_oauth_autenticacao_identificador` FOREIGN KEY (`identificador`) REFERENCES `oauth` (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth_autenticacao`
--

/*!40000 ALTER TABLE `oauth_autenticacao` DISABLE KEYS */;
INSERT INTO `oauth_autenticacao` (`identificador`,`autenticacao`) VALUES 
 ('cliente','cliente_credenciado'),
 ('cliente','codigo_autorizacao'),
 ('cliente','senha'),
 ('cliente','token_atualizacao');
/*!40000 ALTER TABLE `oauth_autenticacao` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth_codigo_autorizacao`
--

DROP TABLE IF EXISTS `oauth_codigo_autorizacao`;
CREATE TABLE `oauth_codigo_autorizacao` (
  `codigo_autorizacao` varchar(128) NOT NULL DEFAULT '',
  `codigo_desafio` varchar(128) NOT NULL DEFAULT '',
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `expiracao` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codigo_autorizacao`),
  KEY `FK_oauth_codigo_autorizacao_identificador` (`identificador`),
  KEY `FK_oauth_codigo_autorizacao_codigo_desafio` (`codigo_desafio`),
  CONSTRAINT `FK_oauth_codigo_autorizacao_codigo_desafio` FOREIGN KEY (`codigo_desafio`) REFERENCES `oauth_codigo_desafio` (`codigo_desafio`),
  CONSTRAINT `FK_oauth_codigo_autorizacao_identificador` FOREIGN KEY (`identificador`) REFERENCES `oauth` (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth_codigo_autorizacao`
--

/*!40000 ALTER TABLE `oauth_codigo_autorizacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_codigo_autorizacao` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth_codigo_desafio`
--

DROP TABLE IF EXISTS `oauth_codigo_desafio`;
CREATE TABLE `oauth_codigo_desafio` (
  `codigo_desafio` varchar(128) NOT NULL DEFAULT '',
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `metodo_desafio` enum('S256') NOT NULL DEFAULT 'S256',
  PRIMARY KEY (`codigo_desafio`),
  KEY `FK_oauth_codigo_desafio_identificador` (`identificador`),
  CONSTRAINT `FK_oauth_codigo_desafio_identificador` FOREIGN KEY (`identificador`) REFERENCES `oauth` (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth_codigo_desafio`
--

/*!40000 ALTER TABLE `oauth_codigo_desafio` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_codigo_desafio` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth_dominio`
--

DROP TABLE IF EXISTS `oauth_dominio`;
CREATE TABLE `oauth_dominio` (
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `dominio` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`identificador`,`dominio`),
  CONSTRAINT `FK_oauth_dominio_identificador` FOREIGN KEY (`identificador`) REFERENCES `oauth` (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth_dominio`
--

/*!40000 ALTER TABLE `oauth_dominio` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_dominio` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth_escopo`
--

DROP TABLE IF EXISTS `oauth_escopo`;
CREATE TABLE `oauth_escopo` (
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `escopo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`identificador`,`escopo`),
  KEY `FK_oauth_escopo_escopo` (`escopo`),
  CONSTRAINT `FK_oauth_escopo_escopo` FOREIGN KEY (`escopo`) REFERENCES `escopo` (`escopo`),
  CONSTRAINT `FK_oauth_escopo_identificador` FOREIGN KEY (`identificador`) REFERENCES `oauth` (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth_escopo`
--

/*!40000 ALTER TABLE `oauth_escopo` DISABLE KEYS */;
INSERT INTO `oauth_escopo` (`identificador`,`escopo`) VALUES 
 ('cliente','perfil');
/*!40000 ALTER TABLE `oauth_escopo` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth_token_acesso`
--

DROP TABLE IF EXISTS `oauth_token_acesso`;
CREATE TABLE `oauth_token_acesso` (
  `token_acesso` varchar(40) NOT NULL DEFAULT '',
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `expiracao` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`token_acesso`),
  KEY `FK_oauth_token_acesso_identificador` (`identificador`),
  CONSTRAINT `FK_oauth_token_acesso_identificador` FOREIGN KEY (`identificador`) REFERENCES `oauth` (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth_token_acesso`
--

/*!40000 ALTER TABLE `oauth_token_acesso` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_token_acesso` ENABLE KEYS */;


--
-- Table structure for table `exemplo`.`oauth_token_atualizacao`
--

DROP TABLE IF EXISTS `oauth_token_atualizacao`;
CREATE TABLE `oauth_token_atualizacao` (
  `token_atualizacao` varchar(80) NOT NULL DEFAULT '',
  `token_acesso` varchar(40) NOT NULL DEFAULT '',
  `identificador` varchar(100) NOT NULL DEFAULT '',
  `expiracao` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`token_atualizacao`),
  KEY `FK_oauth_token_atualizacao_identificador` (`identificador`),
  KEY `FK_oauth_token_atualizacao_token_acesso` (`token_acesso`),
  CONSTRAINT `FK_oauth_token_atualizacao_identificador` FOREIGN KEY (`identificador`) REFERENCES `oauth` (`identificador`),
  CONSTRAINT `FK_oauth_token_atualizacao_token_acesso` FOREIGN KEY (`token_acesso`) REFERENCES `oauth_token_acesso` (`token_acesso`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exemplo`.`oauth_token_atualizacao`
--

/*!40000 ALTER TABLE `oauth_token_atualizacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_token_atualizacao` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
