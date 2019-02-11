SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `msu_research_data_index`
--

-- --------------------------------------------------------

--
-- Table structure for table `affiliations`
--

CREATE TABLE IF NOT EXISTS `affiliations` (
  `affiliation_key` int(10) NOT NULL,
  `creator_key` int(10) NOT NULL,
  `name_affiliation_msuCollege` varchar(255) NOT NULL COMMENT 'element_subelement_local-element - author msu college affiliation',
  `name_affiliation_msuDepartment` varchar(255) NOT NULL COMMENT 'element_subelement_local-element - author msu department affiliation',
  `name_affiliation_otherAffiliation` varchar(255) NOT NULL COMMENT 'element_subelement_local-element - author non msu affiliation'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `creators`
--

CREATE TABLE IF NOT EXISTS `creators` (
  `creator_key` int(10) NOT NULL,
  `recordInfo_recordIdentifier` int(10) NOT NULL,
  `creator_name` varchar(255) NOT NULL,
  `creator_orcid` varchar(255) DEFAULT NULL,
  `creator_type` varchar(255) NOT NULL COMMENT 'person or organization',
  `creator_url` varchar(255) NOT NULL,
  `creator_contactPoint` varchar(255) NOT NULL COMMENT 'person or organization'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `datasets`
--

CREATE TABLE IF NOT EXISTS `datasets` (
  `recordInfo_recordIdentifier` int(10) NOT NULL COMMENT 'element_subelement - record id',
  `dataset_name` varchar(300) NOT NULL DEFAULT '' COMMENT 'element_subelement - dataset title',
  `dataset_doi` varchar(300) DEFAULT NULL COMMENT 'element_subelement - original dataset DOI, points at external record',
  `dataset_repositoryName` varchar(255) DEFAULT NULL,
  `dataset_url` varchar(300) DEFAULT NULL COMMENT 'element_subelement - direct url for the actual dataset content',
  `dataset_description` text COMMENT 'element - dataset abstract',
  `dataset_keywords` varchar(255) DEFAULT NULL COMMENT 'element - dataset comma-delimited content keywords',
  `dataset_temporalCoverage` varchar(30) NOT NULL DEFAULT '' COMMENT 'element_subelement - date dataset published e.g., 1950-01-01/2013-12-18',
  `dataset_spatialCoverage` varchar(30) DEFAULT NULL COMMENT 'element_subelement - geoshape box coordinates OR latitude/longitude',
  `dataset_category1` varchar(255) DEFAULT NULL COMMENT 'element_subelement - linked data category',
  `dataset_category2` varchar(255) DEFAULT NULL COMMENT 'element_subelement - linked data category',
  `dataset_category3` varchar(255) DEFAULT NULL COMMENT 'element_subelement - linked data category',
  `dataset_encodingFormat` varchar(30) DEFAULT NULL COMMENT 'element_subelement_subelement - dataset format type e.g., CSV',
  `dataset_license` varchar(255) NOT NULL DEFAULT 'Attribution Non-Commercial Share Alike Creative Commons ' COMMENT 'element - dataset copyright conditions',
  `dataset_version` varchar(30) DEFAULT NULL COMMENT 'element_subelement - dataset version number',
  `dataset_sameAs` varchar(300) DEFAULT NULL COMMENT 'element_subelement - dataset duplicate content URL for disambiguation if/when dataset is listed in multiple repositories',
  `dataset_urlHash` varchar(40) DEFAULT NULL COMMENT 'local - sha1 hash of DOI to help with deduping during harvest',
  `recordInfo_languageOfCataloging` varchar(5) NOT NULL DEFAULT 'en' COMMENT 'element_subelement - language of record',
  `recordInfo_recordContentSource` varchar(10) NOT NULL DEFAULT 'MZF' COMMENT 'element_subelement - oclc institution id',
  `recordInfo_recordCreationDate` date NOT NULL DEFAULT '0000-00-00' COMMENT 'element_subelement - date record created',
  `recordInfo_recordModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'element_subelement - date record modified',
  `status` varchar(10) CHARACTER SET ucs2 NOT NULL DEFAULT 'r' COMMENT 'local-element - record activity status'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `feeds`
--

CREATE TABLE IF NOT EXISTS `feeds` (
  `relatedItem_originInfo_feed_identifier` int(10) NOT NULL COMMENT 'element_subelement_subelement_subelement - newsfeed id',
  `relatedItem_originInfo_feed_publisher` varchar(255) DEFAULT NULL COMMENT 'element_subelement_subelement_subelement - newsfeed publisher',
  `relatedItem_originInfo_feed_url` text COMMENT 'element_subelement_subelement_subelement - newsfeed url',
  `status` char(1) NOT NULL DEFAULT 'a'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliations`
--
ALTER TABLE `affiliations`
  ADD UNIQUE KEY `affiliation_key` (`affiliation_key`),
  ADD KEY `creator_key` (`creator_key`),
  ADD FULLTEXT KEY `name_affiliation_msuCollege` (`name_affiliation_msuCollege`);
ALTER TABLE `affiliations`
  ADD FULLTEXT KEY `name_affiliation_msuDepartment` (`name_affiliation_msuDepartment`);
ALTER TABLE `affiliations`
  ADD FULLTEXT KEY `name_affiliation_otherInstitution` (`name_affiliation_otherAffiliation`);

--
-- Indexes for table `creators`
--
ALTER TABLE `creators`
  ADD PRIMARY KEY (`creator_key`),
  ADD KEY `recordInfo_recordIdentifier` (`recordInfo_recordIdentifier`),
  ADD FULLTEXT KEY `creator_name` (`creator_name`);

--
-- Indexes for table `datasets`
--
ALTER TABLE `datasets`
  ADD PRIMARY KEY (`recordInfo_recordIdentifier`),
  ADD FULLTEXT KEY `name` (`dataset_name`);
ALTER TABLE `datasets`
  ADD FULLTEXT KEY `keywords` (`dataset_name`,`dataset_category1`,`dataset_description`);

--
-- Indexes for table `feeds`
--
ALTER TABLE `feeds`
  ADD PRIMARY KEY (`relatedItem_originInfo_feed_identifier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliations`
--
ALTER TABLE `affiliations`
  MODIFY `affiliation_key` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `creators`
--
ALTER TABLE `creators`
  MODIFY `creator_key` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `datasets`
--
ALTER TABLE `datasets`
  MODIFY `recordInfo_recordIdentifier` int(10) NOT NULL AUTO_INCREMENT COMMENT 'element_subelement - record id';
--
-- AUTO_INCREMENT for table `feeds`
--
ALTER TABLE `feeds`
  MODIFY `relatedItem_originInfo_feed_identifier` int(10) NOT NULL AUTO_INCREMENT COMMENT 'element_subelement_subelement_subelement - newsfeed id';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
