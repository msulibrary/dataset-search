-- MySQL dump 10.14  Distrib 5.5.64-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: msu_dataset_search
-- ------------------------------------------------------
-- Server version	5.5.64-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `affiliations`
--

DROP TABLE IF EXISTS `affiliations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliations` (
  `affiliation_key` int(10) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier for this record',
  `creator_key` int(10) NOT NULL COMMENT 'creator key',
  `name_affiliation_msuCollege` varchar(255) NOT NULL COMMENT 'author msu college affiliation',
  `name_affiliation_msuDepartment` varchar(255) NOT NULL COMMENT 'author msu department affiliation',
  `name_affiliation_otherAffiliation` varchar(255) NOT NULL COMMENT 'author non msu affiliation',
  UNIQUE KEY `affiliation_key` (`affiliation_key`),
  KEY `creator_key` (`creator_key`),
  FULLTEXT KEY `name_affiliation_msuCollege` (`name_affiliation_msuCollege`),
  FULLTEXT KEY `name_affiliation_msuDepartment` (`name_affiliation_msuDepartment`),
  FULLTEXT KEY `name_affiliation_otherInstitution` (`name_affiliation_otherAffiliation`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliations`
--

LOCK TABLES `affiliations` WRITE;
/*!40000 ALTER TABLE `affiliations` DISABLE KEYS */;
INSERT INTO `affiliations` VALUES (2,31,'Letters & Science','Chemical & Biological Engineering','Center for Biofilm Engineering');
INSERT INTO `affiliations` VALUES (4,33,'Letters & Science','Chemical & Biological Engineering','Center for Biofilm Engineering');
/*!40000 ALTER TABLE `affiliations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `creators`
--

DROP TABLE IF EXISTS `creators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `creators` (
  `creator_key` int(10) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier for this record',
  `recordInfo_recordIdentifier` int(10) NOT NULL COMMENT 'datasets recordIdentifier',
  `creator_name` varchar(255) NOT NULL COMMENT 'name of creator',
  `creator_orcid` varchar(255) DEFAULT NULL COMMENT 'orcid of creator',
  `creator_type` varchar(255) NOT NULL COMMENT 'person or organization',
  `creator_url` varchar(255) NOT NULL COMMENT 'url of creator',
  `creator_contactPoint` varchar(255) NOT NULL COMMENT 'can be person or organization',
  PRIMARY KEY (`creator_key`),
  KEY `recordInfo_recordIdentifier` (`recordInfo_recordIdentifier`),
  FULLTEXT KEY `creator_name` (`creator_name`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `creators`
--

LOCK TABLES `creators` WRITE;
/*!40000 ALTER TABLE `creators` DISABLE KEYS */;
INSERT INTO `creators` VALUES (1,3,'Schuh, Michael A.',NULL,'','','');
INSERT INTO `creators` VALUES (2,3,'Angryk, Rafal A.',NULL,'','','');
INSERT INTO `creators` VALUES (3,3,'Martens, Petrus C.',NULL,'','','');
INSERT INTO `creators` VALUES (42,2,'Matandiko, Wigganson ','','','','');
INSERT INTO `creators` VALUES (41,2,'Becker, Matthew S. ','','','','');
INSERT INTO `creators` VALUES (40,2,'Rosenblatt, Eli ','','','','');
INSERT INTO `creators` VALUES (39,2,'DrÃ¶ge, Egil ','','','','');
INSERT INTO `creators` VALUES (38,2,'M\\\'soka, Jassiel ','','','','');
INSERT INTO `creators` VALUES (36,1,'Reddy, Gadi V. P. ','','','','');
INSERT INTO `creators` VALUES (35,1,'Krishnankutty, Sindhu M. ','','','','');
INSERT INTO `creators` VALUES (34,1,'Portman , Scott L. ','','','','');
INSERT INTO `creators` VALUES (37,2,'Creel, Scott','','','','');
INSERT INTO `creators` VALUES (31,5,'Foreman, Christine','','','','');
INSERT INTO `creators` VALUES (33,6,'Foreman, Christine','','','','');
INSERT INTO `creators` VALUES (43,2,'Simpamba, Twakundine ','','','','');
/*!40000 ALTER TABLE `creators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datasets`
--

DROP TABLE IF EXISTS `datasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datasets` (
  `recordInfo_recordIdentifier` int(10) NOT NULL AUTO_INCREMENT COMMENT 'record id',
  `dataset_name` varchar(300) NOT NULL DEFAULT '' COMMENT 'dataset title',
  `dataset_doi` varchar(300) DEFAULT NULL COMMENT 'original dataset DOI, points at external record',
  `dataset_repositoryName` varchar(255) DEFAULT NULL COMMENT 'name of repository',
  `dataset_url` varchar(300) DEFAULT NULL COMMENT 'direct url for the actual dataset content',
  `dataset_description` text COMMENT 'dataset abstract',
  `dataset_keywords` varchar(255) DEFAULT NULL COMMENT 'dataset comma-delimited content keywords',
  `dataset_temporalCoverage` varchar(30) DEFAULT NULL COMMENT 'date dataset published e.g., 1950-01-01/2013-12-18',
  `dataset_spatialCoverage` varchar(30) DEFAULT NULL COMMENT 'geoshape box coordinates OR latitude/longitude',
  `dataset_category1` varchar(255) DEFAULT NULL COMMENT 'linked data category',
  `dataset_category1_uri` varchar(255) DEFAULT NULL COMMENT 'linked data URI',
  `dataset_category2` varchar(255) DEFAULT NULL COMMENT 'linked data category',
  `dataset_category2_uri` varchar(255) DEFAULT NULL COMMENT 'linked data URI',
  `dataset_category3` varchar(255) DEFAULT NULL COMMENT 'linked data category',
  `dataset_category3_uri` varchar(255) DEFAULT NULL COMMENT 'linked data URI',
  `dataset_category4` varchar(255) DEFAULT NULL COMMENT 'linked data category',
  `dataset_category4_uri` varchar(255) DEFAULT NULL COMMENT 'linked data URI',
  `dataset_category5` varchar(255) DEFAULT NULL COMMENT 'linked data category',
  `dataset_category5_uri` varchar(255) DEFAULT NULL COMMENT 'linked data URI',
  `dataset_encodingFormat` varchar(30) DEFAULT NULL COMMENT 'dataset format type e.g., CSV',
  `dataset_license` varchar(255) NOT NULL DEFAULT 'Attribution Non-Commercial Share Alike Creative Commons ' COMMENT 'dataset copyright conditions',
  `dataset_version` varchar(30) DEFAULT NULL COMMENT 'dataset version number',
  `dataset_sameAs` varchar(300) DEFAULT NULL COMMENT 'dataset duplicate content URL for disambiguation if/when dataset is listed in multiple repositories',
  `dataset_urlHash` varchar(40) DEFAULT NULL COMMENT 'sha1 hash of harvest info to help with deduping during harvest',
  `recordInfo_languageOfCataloging` varchar(5) NOT NULL DEFAULT 'en' COMMENT 'language of record',
  `recordInfo_recordContentSource` varchar(10) NOT NULL DEFAULT 'MZF' COMMENT 'oclc institution id',
  `recordInfo_recordCreationDate` date NOT NULL DEFAULT '0000-00-00' COMMENT 'date record created',
  `recordInfo_recordModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'date record modified',
  `status` varchar(10) CHARACTER SET ucs2 NOT NULL DEFAULT 'u' COMMENT 'record activity status',
  PRIMARY KEY (`recordInfo_recordIdentifier`),
  FULLTEXT KEY `name` (`dataset_name`),
  FULLTEXT KEY `keywords` (`dataset_name`,`dataset_category1`,`dataset_description`)
) ENGINE=MyISAM AUTO_INCREMENT=256 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datasets`
--

LOCK TABLES `datasets` WRITE;
/*!40000 ALTER TABLE `datasets` DISABLE KEYS */;
INSERT INTO `datasets` VALUES (1,'Data from: Entomopathogenic Nematodes Combined with Adjuvants Presents a New Potential Biological Control Method for Managing the Wheat Stem Sawfly, Cephus cinctus (Hymenoptera: Cephidae)','https://doi.org/10.5061/dryad.4p6pf','Dryad','','The wheat stem sawfly, (Cephus cinctus Norton) Hymenoptera: Cephidae, has been a major pest of winter wheat and barley in the northern Great Plains for more than 100 years. The insect\'s cryptic nature and lack of safe chemical control options make the wheat stem sawfly (WSS) difficult to manage; thus, biological control offers the best hope for sustainable management of WSS. Entomopathogenic nematodes (EPNs) have been used successfully against other above-ground insect pests and adding adjuvants to sprays containing EPNs has been shown to improve their effectiveness. We tested the hypothesis that adding chemical adjuvants to sprays containing EPNs will increase the ability of EPNs to enter wheat stems and kill diapausing WSS larvae. This is the first study to test the ability of EPNs to infect the WSS, C. cinctus, and test EPNs combined with adjuvants against C. cinctus in both the laboratory and the field. Infection assays showed that three different species of EPNs caused 60-100% mortality to WSS larvae. Adding Penterra, Silwet L-77, Sunspray 11N, or Syl-Tac to solutions containing EPNs resulted in higher WSS mortality than solutions made with water alone. Field tests showed that sprays containing S. feltiae added to 0.1% Penterra increased WSS mortality up to 29.1%. These results indicate a novel control method for WSS, and represent a significant advancement in the biological control of this persistent insect pest.','Cephus cinctus','2017-01-26','Montana','',NULL,'',NULL,'',NULL,NULL,NULL,NULL,NULL,'','CC0','','',NULL,'en','MZF','0000-00-00','2017-11-30 20:55:49','a');
INSERT INTO `datasets` VALUES (2,'Data from: Assessing the sustainability of African lion trophy hunting, with recommendations for policy','https://doi.org/10.5061/dryad.mc2p8','Dryad','','While trophy hunting provides revenue for conservation, it must be carefully managed to avoid negative population impacts, particularly for long-lived species with low natural mortality rates. Trophy hunting has had negative effects on lion populations throughout Africa, and the species serves as an important case study to consider the balance of costs and benefits, and to consider the effectiveness of alternative strategies to conserve exploited species. Age-restricted harvesting is widely recommended to mitigate negative effects of lion hunting, but this recommendation was based on a population model parameterized with data from a well-protected and growing lion population. Here, we used demographic data from lions subject to more typical conditions, including sourceâ€“sink dynamics between a protected National Park and adjacent hunting areas in Zambia\'s Luangwa Valley, to develop a stochastic population projection model and evaluate alternative harvest scenarios. Hunting resulted in population declines over a 25-yr period for all continuous harvest strategies, with large declines for quotas >1 lion/concession (~0.5 lion/1,000 km2) and hunting of males younger than seven years. A strategy that combined periods of recovery, an age limit of â‰¥7 yr, and a maximum quota of ~0.5 lions shot/1,000 km2 yielded a risk of extirpation <10%. Our analysis incorporated the effects of human encroachment, poaching, and prey depletion on survival, but assumed that these problems will not increase, which is unlikely. These results suggest conservative management of lion trophy hunting with a combination of regulations. To implement sustainable trophy hunting while maintaining revenue for conservation of hunting areas, our results suggest that hunting fees must increase as a consequence of diminished supply. These findings are broadly applicable to hunted lion populations throughout Africa and to inform global efforts to conserve exploited carnivore populations.','hunting, harvest, sustainable offtake, conservation, carnivore, Lion, Panthera leo','2016-05-23','Africa, Zambia, Luangwa Valley','',NULL,'',NULL,'',NULL,NULL,NULL,NULL,NULL,'','CC0','','',NULL,'en','MZF','0000-00-00','2017-11-30 20:56:00','a');
INSERT INTO `datasets` VALUES (3,'Supporting Data: A large-scale dataset of solar event reports from automated feature recognition modules','https://doi.org/10.5281/zenodo.48187','','','This is the supporting dataset for the paper:\r\n\r\nA large-scale dataset of solar event reports from automated feature recognition modules. Michael A. Schuh, Rafal A. Angryk, Petrus C. Martens. Journal of Space Weather and Space Climate, 2016.','astroinformatics, data mining, solar activity','','','',NULL,'',NULL,'',NULL,NULL,NULL,NULL,NULL,'','Creative Commons Zero','','',NULL,'en','MZF','0000-00-00','2017-11-18 00:20:47','a');
INSERT INTO `datasets` VALUES (4,'Molecular Models and Wave Function Definitions for Models A-E of the [2Fe]F Cluster in FeFe-hydrogenase Maturase Enzyme HydF','https://doi.org/10.5281/zenodo.1067173','','','The dataset contains all relevant computational models for 2Fe-clusters, and their electronic wave function data (using formatted Gaussian16 checkpoint files) as described in the related publication (see citation).\r\nThe top folder contains \"analysis.xlsx\" electronic spreadsheet that summarizes all the numerical results for absolute and relative electronic energy values, internal coordinates, calculated and scaled vibrational frequencies for diatomic stretching modes. The details of developing scaled quantum forcefields as a function of level of theory and model composition are also given. The schematic structural definitions are given in the \"models.pdf\" file and keys for abbreviations are provided in \"symbols.txt\" file.','density functional theory; Fe-S clusters; HydF maturase enzyme; hydrogenase; FTIR; scaled quantum mechanical force field; carbonyl stretching frequencies; cyanide stretching frequencies','2017-11-16','','',NULL,'',NULL,'',NULL,NULL,NULL,NULL,NULL,'','Creative Commons Attribution 4.0','','',NULL,'en','MZF','0000-00-00','2017-11-29 23:28:33','a');
INSERT INTO `datasets` VALUES (5,' Molecular Level Characterization of Dissolved Organic Carbon and Microbial Diversity in the WAIS Divide Replicate Core','https://doi.org/10.15784/600133','U.S. Antarctic Program Data Center','','This award supports a detailed, molecular level characterization of dissolved organic carbon and microbes in Antarctic ice cores. Using the most modern biological (genomic), geochemical techniques, and advanced chemical instrumentation researchers will 1) optimize protocols for collecting, extracting and amplifying DNA from deep ice cores suitable for use in next generation pyrosequencing; 2) determine the microbial diversity within the ice core; and 3) obtain and analyze detailed molecular characterizations of the carbon in the ice by ultrahigh resolution Fourier Transform Ion Cyclotron Resonance Mass Spectrometry (FT-ICR-MS). With this pilot study investigators will be able to quantify the amount of material (microbial biomass and carbon) required to perform these characterizations, which is needed to inform future ice coring projects. The ultimate goal will be to develop protocols that maximize the yield, while minimizing the amount of ice required. The broader impacts include education and outreach at both the local and national levels. As a faculty mentor with the American Indian Research Opportunities and BRIDGES programs at Montana State University, Foreman will serve as a mentor to a Native American student in the lab during the summer months. Susan Kelly is an Education and Outreach Coordinator with a MS degree in Geology and over 10 years of experience in science outreach. She will coordinate efforts for comprehensive educational collaboration with the Hardin School District on the Crow Indian Reservation in South-central Montana.','','2015-01-01','79.468Â° S 112.086Â° W','',NULL,'',NULL,'',NULL,NULL,NULL,NULL,NULL,'','','','',NULL,'en','MZF','0000-00-00','2017-11-30 20:49:30','a');
INSERT INTO `datasets` VALUES (6,' The Biogeochemical Evolution of Dissolved Organic Matter in a Fluvial System on the Cotton Glacier, Antarctica','https://doi.org/10.15784/600104','U.S. Antarctic Program Data Center','','Dissolved organic matter (DOM) comprises a significant pool of Earth\'s organic carbon that dwarfs the amount present in living aquatic organisms. The properties and reactivity of DOM are not well defined, and the evolution of autochthonous DOM from its precursor materials in freshwater has not been observed. Recent sampling of a supraglacial stream formed on the Cotton Glacier in the Transantarctic Mountains revealed DOM that more closely resembles an assemblage of recognizable precursor organic compounds, based upon its UV-VIS and fluorescence spectra. It is suggested that the DOM from this water evolved over time to resemble materials present in marine and many inland surface waters. The transient nature of the system i.e., it reforms seasonally, also prevents any accumulation of the refractory DOM present in most surface waters. Thus, the Cotton Glacier provides us with a unique environment to study the formation of DOM from precursor materials. An interdisciplinary team will study the biogeochemistry of this progenitor DOM and how microbes modify it. By focusing on the chemical composition of the DOM as it shifts from precursor material to the more humified fractions, the investigators will relate this transition to bioavailability, enzymatic activity, community composition and microbial growth efficiency. This project will support education at all levels, K-12, high school, undergraduate, graduate and post-doc and will increase participation by under-represented groups in science. Towards these goals, the investigators have established relationships with girls\' schools and Native American programs. Additional outreach will be carried out in coordination with PolarTREC, PolarPalooza, and if possible, an Antarctic Artist and Writer.','','2014-06-30','161.667Â°  S -77.117Â°  W','',NULL,'',NULL,'',NULL,NULL,NULL,NULL,NULL,'','','','',NULL,'en','MZF','0000-00-00','2017-11-30 20:55:00','a');
/*!40000 ALTER TABLE `datasets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feeds`
--

DROP TABLE IF EXISTS `feeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feeds` (
  `relatedItem_originInfo_feed_identifier` int(10) NOT NULL AUTO_INCREMENT COMMENT 'newsfeed id',
  `relatedItem_originInfo_feed_publisher` varchar(255) DEFAULT NULL COMMENT 'newsfeed publisher',
  `relatedItem_originInfo_feed_url` text COMMENT 'newsfeed url',
  `relatedItem_originInfo_feed_contentType` char(1) NOT NULL DEFAULT 'x' COMMENT 'Feed Response Content Type',
  `status` char(1) NOT NULL DEFAULT 'a' COMMENT 'newsfeed status',
  PRIMARY KEY (`relatedItem_originInfo_feed_identifier`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feeds`
--

LOCK TABLES `feeds` WRITE;
/*!40000 ALTER TABLE `feeds` DISABLE KEYS */;
INSERT INTO `feeds` VALUES (1,'SHARE','https://share.osf.io/api/v2/atom/?elasticQuery=%7B%22bool%22%3A%7B%22must%22%3A%7B%22query_string%22%3A%7B%22query%22%3A%22%5C%22montana%20state%5C%22%22%7D%7D%2C%22filter%22%3A%5B%7B%22term%22%3A%7B%22types%22%3A%22data%20set%22%7D%7D%5D%7D%7D','x','a');
INSERT INTO `feeds` VALUES (2,'Metabolomic Exchange','http://www.metabolomexchange.org/rss/#/search?search=%22Montana%20State%20University%22#top','x','i');
INSERT INTO `feeds` VALUES (3,'Dryad-RSS','http://datadryad.org/feed/rss_2.0/site','x','i');
INSERT INTO `feeds` VALUES (4,'DataMed','https://datamed.org/webapi/esearch?searchtype=data&query=%22montana%20state%20university%22','x','a');
INSERT INTO `feeds` VALUES (5,'Elsevier DataSearch - old','https://api.datasearch.elsevier.com/api/v2/search?query=institutionName:montana*','j','i');
INSERT INTO `feeds` VALUES (6,'DataCite-Dryad','https://api.test.datacite.org/dois?client-id=dryad.dryad','j','a');
INSERT INTO `feeds` VALUES (7,'DataCite','https://api.datacite.org/dois?query=publisher:*Montana*','j','a');
INSERT INTO `feeds` VALUES (8,'Elsevier-Dryad','https://api.datasearch.elsevier.com/api/v2/search?query=source:dryad','j','a');
INSERT INTO `feeds` VALUES (9,'Dryad','https://datadryad.org/api/v2/datasets','j','i');
INSERT INTO `feeds` VALUES (10,'Elsevier DataSearch','https://api.datasearch.elsevier.com/api/v2/search?query=%22montana%20state%20university%22','j','a');
/*!40000 ALTER TABLE `feeds` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-12-26 13:17:38
