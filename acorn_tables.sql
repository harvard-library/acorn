-- MySQL dump 10.13  Distrib 5.5.52, for Linux (x86_64)
--
-- Host: localhost    Database: acornosdev
-- ------------------------------------------------------
-- Server version	5.5.52

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
-- Table structure for table `AuditTrail`
--

DROP TABLE IF EXISTS `AuditTrail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuditTrail` (
  `AuditTrailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PersonID` int(10) unsigned NOT NULL,
  `ActionType` enum('Insert','Update','Delete') NOT NULL DEFAULT 'Insert',
  `Date` datetime NOT NULL,
  `Details` varchar(1024) DEFAULT NULL,
  `TableName` varchar(255) NOT NULL,
  `PKID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`AuditTrailID`),
  KEY `AuditTrail_FKIndex1` (`PersonID`),
  CONSTRAINT `Audit_Person` FOREIGN KEY (`PersonID`) REFERENCES `People` (`PersonID`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=49480 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuditTrail`
--

LOCK TABLES `AuditTrail` WRITE;
/*!40000 ALTER TABLE `AuditTrail` DISABLE KEYS */;
/*!40000 ALTER TABLE `AuditTrail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Autotext`
--

DROP TABLE IF EXISTS `Autotext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Autotext` (
  `AutotextID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Caption` varchar(255) NOT NULL,
  `Autotext` text,
  `PersonID` int(10) unsigned DEFAULT NULL,
  `AutotextType` enum('Dependency','Treatment','Description','Condition','Preservation','ProposedTreatment') DEFAULT NULL,
  `DependentAutotextID` int(10) unsigned DEFAULT NULL,
  `IsGlobal` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`AutotextID`),
  KEY `Person_AutoText` (`PersonID`),
  CONSTRAINT `Person_AutoText` FOREIGN KEY (`PersonID`) REFERENCES `People` (`PersonID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=266 DEFAULT CHARSET=utf8 PACK_KEYS=0 COMMENT='This is used to help populate certain fields.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Autotext`
--

LOCK TABLES `Autotext` WRITE;
/*!40000 ALTER TABLE `Autotext` DISABLE KEYS */;
/*!40000 ALTER TABLE `Autotext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CallNumbers`
--

DROP TABLE IF EXISTS `CallNumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CallNumbers` (
  `CallNumber` varchar(255) NOT NULL,
  `IdentificationID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`CallNumber`,`IdentificationID`),
  KEY `CallNumbers_Ident` (`IdentificationID`),
  CONSTRAINT `Call_ItemIdent` FOREIGN KEY (`IdentificationID`) REFERENCES `ItemIdentification` (`IdentificationID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CallNumbers`
--

LOCK TABLES `CallNumbers` WRITE;
/*!40000 ALTER TABLE `CallNumbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `CallNumbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `ChargeTo`
--

DROP TABLE IF EXISTS `ChargeTo`;
/*!50001 DROP VIEW IF EXISTS `ChargeTo`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ChargeTo` (
  `ChargeToID` tinyint NOT NULL,
  `ChargeToName` tinyint NOT NULL,
  `ChargeToType` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `CombinedRecords`
--

DROP TABLE IF EXISTS `CombinedRecords`;
/*!50001 DROP VIEW IF EXISTS `CombinedRecords`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `CombinedRecords` (
  `RecordType` tinyint NOT NULL,
  `RecordID` tinyint NOT NULL,
  `IdentificationID` tinyint NOT NULL,
  `PurposeID` tinyint NOT NULL,
  `HomeLocationID` tinyint NOT NULL,
  `ChargeToID` tinyint NOT NULL,
  `Title` tinyint NOT NULL,
  `DepartmentID` tinyint NOT NULL,
  `GroupID` tinyint NOT NULL,
  `ProjectID` tinyint NOT NULL,
  `Comments` tinyint NOT NULL,
  `Inactive` tinyint NOT NULL,
  `EditCounter` tinyint NOT NULL,
  `NonDigitalImagesExist` tinyint NOT NULL,
  `IsBeingEdited` tinyint NOT NULL,
  `EditedByID` tinyint NOT NULL,
  `CuratorID` tinyint NOT NULL,
  `ApprovingCuratorID` tinyint NOT NULL,
  `FormatID` tinyint NOT NULL,
  `CoordinatorID` tinyint NOT NULL,
  `IsNonCollectionMaterial` tinyint NOT NULL,
  `ExpectedDateOfReturn` tinyint NOT NULL,
  `InsuranceValue` tinyint NOT NULL,
  `FundMemo` tinyint NOT NULL,
  `AuthorArtist` tinyint NOT NULL,
  `DateOfObject` tinyint NOT NULL,
  `CollectionName` tinyint NOT NULL,
  `Storage` tinyint NOT NULL,
  `ManuallyClosed` tinyint NOT NULL,
  `ManuallyClosedDate` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Departments`
--

DROP TABLE IF EXISTS `Departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Departments` (
  `DepartmentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `LocationID` int(10) unsigned NOT NULL,
  `DepartmentName` varchar(255) NOT NULL,
  `ShortName` varchar(50) DEFAULT NULL,
  `Acronym` varchar(10) DEFAULT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`DepartmentID`),
  KEY `Departments_FKIndex1` (`LocationID`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Departments`
--

LOCK TABLES `Departments` WRITE;
/*!40000 ALTER TABLE `Departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `Departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Files`
--

DROP TABLE IF EXISTS `Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Files` (
  `FileID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PKID` int(10) unsigned NOT NULL,
  `Path` varchar(255) NOT NULL,
  `Description` text,
  `FileType` varchar(255) NOT NULL DEFAULT 'Image',
  `LinkType` enum('Item','OSW','Group') NOT NULL DEFAULT 'Item',
  `DateEntered` datetime NOT NULL,
  `LastModified` datetime NOT NULL,
  `FileName` varchar(500) NOT NULL,
  `UploadStatus` enum('Pending','Complete') DEFAULT NULL,
  `EnteredByID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`FileID`),
  KEY `Files_FKIndex1` (`PKID`),
  KEY `Files_EnteredBy` (`EnteredByID`),
  CONSTRAINT `Files_EnteredBy` FOREIGN KEY (`EnteredByID`) REFERENCES `People` (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=26609 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Files`
--

LOCK TABLES `Files` WRITE;
/*!40000 ALTER TABLE `Files` DISABLE KEYS */;
/*!40000 ALTER TABLE `Files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Formats`
--

DROP TABLE IF EXISTS `Formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Formats` (
  `FormatID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Format` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Rank` int(10) unsigned NOT NULL DEFAULT '500',
  PRIMARY KEY (`FormatID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Formats`
--

LOCK TABLES `Formats` WRITE;
/*!40000 ALTER TABLE `Formats` DISABLE KEYS */;
/*!40000 ALTER TABLE `Formats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Groups`
--

DROP TABLE IF EXISTS `Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Groups` (
  `GroupID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ProjectID` int(10) unsigned DEFAULT NULL,
  `IsBeingEdited` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `EditedByID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`GroupID`),
  KEY `Groups_FKIndex1` (`ProjectID`),
  CONSTRAINT `Groups_Project` FOREIGN KEY (`ProjectID`) REFERENCES `Projects` (`ProjectID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Groups`
--

LOCK TABLES `Groups` WRITE;
/*!40000 ALTER TABLE `Groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `Groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Importances`
--

DROP TABLE IF EXISTS `Importances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Importances` (
  `ImportanceID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Importance` varchar(255) NOT NULL,
  `Inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ImportanceID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Importances`
--

LOCK TABLES `Importances` WRITE;
/*!40000 ALTER TABLE `Importances` DISABLE KEYS */;
/*!40000 ALTER TABLE `Importances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `InitialCounts`
--

DROP TABLE IF EXISTS `InitialCounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `InitialCounts` (
  `ItemID` int(10) unsigned NOT NULL,
  `CountType` enum('Volumes','Sheets','Photos','Other','Boxes') NOT NULL,
  `TotalCount` int(10) unsigned NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ItemID`,`CountType`,`TotalCount`),
  KEY `InitialCounts_FKIndex1` (`ItemID`),
  CONSTRAINT `InitCt_Item` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `InitialCounts`
--

LOCK TABLES `InitialCounts` WRITE;
/*!40000 ALTER TABLE `InitialCounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `InitialCounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ItemConservators`
--

DROP TABLE IF EXISTS `ItemConservators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ItemConservators` (
  `PersonID` int(10) unsigned NOT NULL,
  `ReportID` int(10) unsigned NOT NULL,
  `CompletedHours` decimal(10,2) NOT NULL,
  `DateCompleted` date DEFAULT NULL,
  KEY `Preservation_FKIndex1` (`PersonID`),
  KEY `ItemConservators_FKIndex2` (`ReportID`),
  CONSTRAINT `Cons_Person` FOREIGN KEY (`PersonID`) REFERENCES `People` (`PersonID`),
  CONSTRAINT `Cons_Rep` FOREIGN KEY (`ReportID`) REFERENCES `ItemReport` (`ReportID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ItemConservators`
--

LOCK TABLES `ItemConservators` WRITE;
/*!40000 ALTER TABLE `ItemConservators` DISABLE KEYS */;
/*!40000 ALTER TABLE `ItemConservators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ItemIdentification`
--

DROP TABLE IF EXISTS `ItemIdentification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ItemIdentification` (
  `IdentificationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PurposeID` int(10) unsigned NOT NULL,
  `HomeLocationID` int(10) unsigned NOT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `DepartmentID` int(10) unsigned DEFAULT NULL,
  `GroupID` int(10) unsigned DEFAULT NULL,
  `ProjectID` int(10) unsigned DEFAULT NULL,
  `Comments` text,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `EditCounter` int(10) unsigned DEFAULT NULL,
  `NonDigitalImagesExist` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `IsBeingEdited` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `EditedByID` int(10) unsigned DEFAULT NULL,
  `CuratorID` int(10) unsigned NOT NULL,
  `ApprovingCuratorID` int(10) unsigned NOT NULL,
  `ManuallyClosed` tinyint(1) NOT NULL DEFAULT '0',
  `ManuallyClosedDate` datetime DEFAULT NULL,
  `ChargeToID` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdentificationID`),
  KEY `ItemIdentification_Purpose` (`PurposeID`),
  KEY `ItemIdentification_HomeLoc` (`HomeLocationID`),
  KEY `ItemIdentification_FKIndex3` (`DepartmentID`),
  KEY `ItemIdentification_FKIndex7` (`GroupID`),
  KEY `ItemIdentification_FKIndex6` (`ProjectID`),
  KEY `ItemIdentification_Curator` (`CuratorID`),
  KEY `Ident_ApprCur` (`ApprovingCuratorID`),
  CONSTRAINT `Ident_ApprCur` FOREIGN KEY (`ApprovingCuratorID`) REFERENCES `People` (`PersonID`),
  CONSTRAINT `Ident_Curator` FOREIGN KEY (`CuratorID`) REFERENCES `People` (`PersonID`),
  CONSTRAINT `Ident_Group` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`) ON DELETE SET NULL,
  CONSTRAINT `Ident_Project` FOREIGN KEY (`ProjectID`) REFERENCES `Projects` (`ProjectID`) ON DELETE SET NULL,
  CONSTRAINT `Ident_Purp` FOREIGN KEY (`PurposeID`) REFERENCES `Purposes` (`PurposeID`),
  CONSTRAINT `Loc_HomeLoc` FOREIGN KEY (`HomeLocationID`) REFERENCES `Locations` (`LocationID`)
) ENGINE=InnoDB AUTO_INCREMENT=9656 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ItemIdentification`
--

LOCK TABLES `ItemIdentification` WRITE;
/*!40000 ALTER TABLE `ItemIdentification` DISABLE KEYS */;
/*!40000 ALTER TABLE `ItemIdentification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ItemImportances`
--

DROP TABLE IF EXISTS `ItemImportances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ItemImportances` (
  `ImportanceID` int(10) unsigned NOT NULL,
  `ReportID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ImportanceID`,`ReportID`),
  KEY `ItemImportances_FKIndex2` (`ImportanceID`),
  KEY `ItemImportances_Rep` (`ReportID`),
  CONSTRAINT `ItemImp_Imp` FOREIGN KEY (`ImportanceID`) REFERENCES `Importances` (`ImportanceID`),
  CONSTRAINT `ItemImp_Rpt` FOREIGN KEY (`ReportID`) REFERENCES `ItemReport` (`ReportID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ItemImportances`
--

LOCK TABLES `ItemImportances` WRITE;
/*!40000 ALTER TABLE `ItemImportances` DISABLE KEYS */;
/*!40000 ALTER TABLE `ItemImportances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ItemLogin`
--

DROP TABLE IF EXISTS `ItemLogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ItemLogin` (
  `LoginID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentificationID` int(10) unsigned NOT NULL,
  `LoginByID` int(10) unsigned NOT NULL,
  `LoginDate` date NOT NULL,
  `FromLocationID` int(10) unsigned NOT NULL,
  `ToLocationID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`LoginID`),
  KEY `ItemLogin_LoginBy` (`LoginByID`),
  KEY `ItemLogin_Ident` (`IdentificationID`),
  CONSTRAINT `Login_Ident` FOREIGN KEY (`IdentificationID`) REFERENCES `ItemIdentification` (`IdentificationID`) ON DELETE CASCADE,
  CONSTRAINT `Login_LoginBy` FOREIGN KEY (`LoginByID`) REFERENCES `People` (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=8380 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ItemLogin`
--

LOCK TABLES `ItemLogin` WRITE;
/*!40000 ALTER TABLE `ItemLogin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ItemLogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ItemLogout`
--

DROP TABLE IF EXISTS `ItemLogout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ItemLogout` (
  `LogoutID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `LogoutByID` int(10) unsigned NOT NULL,
  `IdentificationID` int(10) unsigned NOT NULL,
  `LogoutDate` date NOT NULL,
  `ToLocationID` int(10) unsigned NOT NULL,
  `FromLocationID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`LogoutID`),
  KEY `ItemLogout_FKIndex2` (`LogoutByID`),
  KEY `ItemLogout_Ident` (`IdentificationID`),
  CONSTRAINT `Logout_Ident` FOREIGN KEY (`IdentificationID`) REFERENCES `ItemIdentification` (`IdentificationID`) ON DELETE CASCADE,
  CONSTRAINT `Logout_LogoutBy` FOREIGN KEY (`LogoutByID`) REFERENCES `People` (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=8225 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ItemLogout`
--

LOCK TABLES `ItemLogout` WRITE;
/*!40000 ALTER TABLE `ItemLogout` DISABLE KEYS */;
/*!40000 ALTER TABLE `ItemLogout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ItemProposal`
--

DROP TABLE IF EXISTS `ItemProposal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ItemProposal` (
  `ProposalID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentificationID` int(10) unsigned NOT NULL,
  `ProposalDate` date NOT NULL,
  `Description` text,
  `Condition` text,
  `Treatment` text,
  `MinimumProposedHours` decimal(10,2) NOT NULL,
  `MaximumProposedHours` decimal(10,2) NOT NULL,
  `Height` decimal(10,2) DEFAULT NULL,
  `Width` decimal(10,2) DEFAULT NULL,
  `Thickness` decimal(10,2) DEFAULT NULL,
  `DimensionUnit` enum('cm','in') DEFAULT NULL,
  `ExamDate` date DEFAULT NULL,
  `ExamLocationID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ProposalID`),
  KEY `ItemProposal_Ident` (`IdentificationID`),
  KEY `ItemProposal_FKIndex3` (`ExamLocationID`),
  CONSTRAINT `Prop_ExamLoc` FOREIGN KEY (`ExamLocationID`) REFERENCES `Locations` (`LocationID`),
  CONSTRAINT `Prop_Ident` FOREIGN KEY (`IdentificationID`) REFERENCES `ItemIdentification` (`IdentificationID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7960 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ItemProposal`
--

LOCK TABLES `ItemProposal` WRITE;
/*!40000 ALTER TABLE `ItemProposal` DISABLE KEYS */;
/*!40000 ALTER TABLE `ItemProposal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ItemReport`
--

DROP TABLE IF EXISTS `ItemReport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ItemReport` (
  `ReportID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormatID` int(10) unsigned DEFAULT NULL,
  `IdentificationID` int(10) unsigned NOT NULL,
  `ReportByID` int(10) unsigned NOT NULL,
  `ReportDate` date NOT NULL,
  `Treatment` text,
  `Summary` text,
  `Height` decimal(10,2) DEFAULT NULL,
  `Width` decimal(10,2) DEFAULT NULL,
  `Thickness` decimal(10,2) DEFAULT NULL,
  `DimensionUnit` varchar(10) DEFAULT NULL,
  `WorkLocationID` int(10) unsigned NOT NULL,
  `ExamOnly` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `CustomHousingOnly` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `AdminOnly` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `PreservationRecommendations` text,
  `AdditionalMaterialsOnFile` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ReportID`),
  KEY `ItemReport_FKIndex2` (`ReportByID`),
  KEY `ItemReport_FKIndex3` (`FormatID`),
  KEY `ItemReport_Ident` (`IdentificationID`),
  KEY `ItemReport_FKIndex4` (`WorkLocationID`),
  CONSTRAINT `Rep_Format` FOREIGN KEY (`FormatID`) REFERENCES `Formats` (`FormatID`),
  CONSTRAINT `Rep_Ident` FOREIGN KEY (`IdentificationID`) REFERENCES `ItemIdentification` (`IdentificationID`) ON DELETE CASCADE,
  CONSTRAINT `Rep_RepBy` FOREIGN KEY (`ReportByID`) REFERENCES `People` (`PersonID`),
  CONSTRAINT `Rep_WorkLoc` FOREIGN KEY (`WorkLocationID`) REFERENCES `Locations` (`LocationID`)
) ENGINE=InnoDB AUTO_INCREMENT=9402 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ItemReport`
--

LOCK TABLES `ItemReport` WRITE;
/*!40000 ALTER TABLE `ItemReport` DISABLE KEYS */;
/*!40000 ALTER TABLE `ItemReport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Items`
--

DROP TABLE IF EXISTS `Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items` (
  `ItemID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormatID` int(10) unsigned DEFAULT NULL,
  `IdentificationID` int(10) unsigned NOT NULL,
  `CoordinatorID` int(10) unsigned NOT NULL,
  `IsNonCollectionMaterial` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Fund` varchar(255) DEFAULT NULL,
  `InsuranceValue` decimal(10,2) DEFAULT NULL,
  `FundMemo` text,
  `AuthorArtist` varchar(255) DEFAULT NULL,
  `DateOfObject` varchar(50) DEFAULT NULL,
  `EditCounter` int(10) unsigned DEFAULT NULL,
  `CollectionName` varchar(1000) DEFAULT NULL,
  `Storage` varchar(255) DEFAULT NULL,
  `ExpectedDateOfReturn` date DEFAULT NULL,
  PRIMARY KEY (`ItemID`),
  KEY `Item_Coord` (`CoordinatorID`),
  KEY `Item_FKIndex5` (`IdentificationID`),
  KEY `Items_FKIndex4` (`FormatID`),
  CONSTRAINT `Items_Coord` FOREIGN KEY (`CoordinatorID`) REFERENCES `People` (`PersonID`),
  CONSTRAINT `Items_Format` FOREIGN KEY (`FormatID`) REFERENCES `Formats` (`FormatID`) ON DELETE SET NULL,
  CONSTRAINT `Items_Ident` FOREIGN KEY (`IdentificationID`) REFERENCES `ItemIdentification` (`IdentificationID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8735 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Items`
--

LOCK TABLES `Items` WRITE;
/*!40000 ALTER TABLE `Items` DISABLE KEYS */;
/*!40000 ALTER TABLE `Items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LocationHistory`
--

DROP TABLE IF EXISTS `LocationHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LocationHistory` (
  `LocationHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ItemID` int(10) unsigned NOT NULL,
  `DateIn` datetime NOT NULL,
  `LocationID` int(10) unsigned NOT NULL,
  `DateOut` datetime DEFAULT NULL,
  `IsPartial` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `IsTemporary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `PartialComments` text,
  PRIMARY KEY (`LocationHistoryID`),
  KEY `LocationHistory_FKIndex1` (`LocationID`),
  KEY `LocationHistory_FKIndex2` (`ItemID`),
  CONSTRAINT `LocHist_Item` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`) ON DELETE CASCADE,
  CONSTRAINT `LocHist_Loc` FOREIGN KEY (`LocationID`) REFERENCES `Locations` (`LocationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LocationHistory`
--

LOCK TABLES `LocationHistory` WRITE;
/*!40000 ALTER TABLE `LocationHistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `LocationHistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Locations`
--

DROP TABLE IF EXISTS `Locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Locations` (
  `LocationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Location` varchar(255) NOT NULL,
  `TUB` varchar(100) DEFAULT NULL,
  `ShortName` varchar(50) DEFAULT NULL,
  `Acronym` varchar(10) DEFAULT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `IsRepository` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `IsWorkLocation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`LocationID`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Locations`
--

LOCK TABLES `Locations` WRITE;
/*!40000 ALTER TABLE `Locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `Locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OSW`
--

DROP TABLE IF EXISTS `OSW`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OSW` (
  `OSWID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentificationID` int(10) unsigned DEFAULT NULL,
  `ProposedHours` decimal(10,2) DEFAULT NULL,
  `WorkStartDate` date DEFAULT NULL,
  `WorkEndDate` date DEFAULT NULL,
  `FormatID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`OSWID`),
  KEY `OSW_FKIndex1` (`IdentificationID`),
  KEY `OSW_Format` (`FormatID`),
  CONSTRAINT `OSW_Format` FOREIGN KEY (`FormatID`) REFERENCES `Formats` (`FormatID`) ON DELETE SET NULL,
  CONSTRAINT `OSW_Ident` FOREIGN KEY (`IdentificationID`) REFERENCES `ItemIdentification` (`IdentificationID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=987 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OSW`
--

LOCK TABLES `OSW` WRITE;
/*!40000 ALTER TABLE `OSW` DISABLE KEYS */;
/*!40000 ALTER TABLE `OSW` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OSWWorkTypes`
--

DROP TABLE IF EXISTS `OSWWorkTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OSWWorkTypes` (
  `OSWID` int(10) unsigned NOT NULL,
  `WorkTypeID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`OSWID`,`WorkTypeID`),
  KEY `OSWWorkTypes_FKIndex1` (`OSWID`),
  KEY `OSWWorkTypes_FKIndex2` (`WorkTypeID`),
  CONSTRAINT `OSWWkType_OSW` FOREIGN KEY (`OSWID`) REFERENCES `OSW` (`OSWID`) ON DELETE CASCADE,
  CONSTRAINT `OSWWkType_WkType` FOREIGN KEY (`WorkTypeID`) REFERENCES `WorkTypes` (`WorkTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OSWWorkTypes`
--

LOCK TABLES `OSWWorkTypes` WRITE;
/*!40000 ALTER TABLE `OSWWorkTypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `OSWWorkTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `People`
--

DROP TABLE IF EXISTS `People`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `People` (
  `PersonID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `LocationID` int(10) unsigned DEFAULT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `UserPassword` varchar(50) DEFAULT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `AccessLevel` enum('Admin','Regular','Repository Admin','Repository','None','Curator') NOT NULL DEFAULT 'Regular',
  `DisplayName` varchar(255) NOT NULL,
  `SortName` varchar(255) NOT NULL,
  `Initials` varchar(50) DEFAULT NULL,
  `EmailAddress` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`PersonID`),
  KEY `People_FKIndex3` (`LocationID`),
  CONSTRAINT `People_Repository` FOREIGN KEY (`LocationID`) REFERENCES `Locations` (`LocationID`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `People`
--

LOCK TABLES `People` WRITE;
/*!40000 ALTER TABLE `People` DISABLE KEYS */;
INSERT INTO `People` VALUES (330,NULL,'Admin',NULL,'User','adminuser','4cb9c8a8048fd02294477fcb1a41191a',0,'Admin','Admin User','User, Admin',NULL,NULL);
/*!40000 ALTER TABLE `People` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Projects`
--

DROP TABLE IF EXISTS `Projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Projects` (
  `ProjectID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectName` varchar(255) NOT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ProjectDescription` text,
  `IsBeingEdited` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `EditedByID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ProjectID`)
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Projects`
--

LOCK TABLES `Projects` WRITE;
/*!40000 ALTER TABLE `Projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `Projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ProposalApprovalHistory`
--

DROP TABLE IF EXISTS `ProposalApprovalHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProposalApprovalHistory` (
  `HistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PKID` int(10) unsigned NOT NULL,
  `RecordType` enum('Item','Group') NOT NULL DEFAULT 'Item',
  `ActivityType` varchar(255) NOT NULL,
  `Details` text,
  `AuthorID` int(10) unsigned NOT NULL,
  `DateEntered` date NOT NULL,
  PRIMARY KEY (`HistoryID`),
  KEY `Auth_Hist` (`AuthorID`),
  CONSTRAINT `Auth_Hist` FOREIGN KEY (`AuthorID`) REFERENCES `People` (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=5765 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ProposalApprovalHistory`
--

LOCK TABLES `ProposalApprovalHistory` WRITE;
/*!40000 ALTER TABLE `ProposalApprovalHistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `ProposalApprovalHistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ProposedBy`
--

DROP TABLE IF EXISTS `ProposedBy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProposedBy` (
  `PersonID` int(10) unsigned NOT NULL,
  `ProposalID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PersonID`,`ProposalID`),
  KEY `ProposedBy_Person` (`PersonID`),
  KEY `ProposedBy_Proposal` (`ProposalID`),
  CONSTRAINT ` ProposalBy _Person` FOREIGN KEY (`PersonID`) REFERENCES `People` (`PersonID`),
  CONSTRAINT `ProposalBy_Item` FOREIGN KEY (`ProposalID`) REFERENCES `ItemProposal` (`ProposalID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ProposedBy`
--

LOCK TABLES `ProposedBy` WRITE;
/*!40000 ALTER TABLE `ProposedBy` DISABLE KEYS */;
/*!40000 ALTER TABLE `ProposedBy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Purposes`
--

DROP TABLE IF EXISTS `Purposes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Purposes` (
  `PurposeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Purpose` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`PurposeID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Purposes`
--

LOCK TABLES `Purposes` WRITE;
/*!40000 ALTER TABLE `Purposes` DISABLE KEYS */;
/*!40000 ALTER TABLE `Purposes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ReportCounts`
--

DROP TABLE IF EXISTS `ReportCounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ReportCounts` (
  `ReportID` int(10) unsigned NOT NULL,
  `CountType` enum('Volumes','Sheets','Photos','Other','Housings','Boxes') NOT NULL,
  `TotalCount` int(10) unsigned NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ReportID`,`CountType`,`TotalCount`),
  KEY `ReportCounts_FKIndex1` (`ReportID`),
  CONSTRAINT `RepCounts_Rep` FOREIGN KEY (`ReportID`) REFERENCES `ItemReport` (`ReportID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReportCounts`
--

LOCK TABLES `ReportCounts` WRITE;
/*!40000 ALTER TABLE `ReportCounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `ReportCounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Roles` (
  `PersonID` int(10) unsigned NOT NULL,
  `RoleType` enum('Staff','Curator','Donor','Contractor') NOT NULL,
  PRIMARY KEY (`PersonID`,`RoleType`),
  KEY `Roles_FKIndex2` (`PersonID`),
  CONSTRAINT `Role_Person` FOREIGN KEY (`PersonID`) REFERENCES `People` (`PersonID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Roles`
--

LOCK TABLES `Roles` WRITE;
/*!40000 ALTER TABLE `Roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `Roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SavedSearches`
--

DROP TABLE IF EXISTS `SavedSearches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SavedSearches` (
  `SearchID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SearchName` varchar(255) NOT NULL,
  `PersonID` int(10) unsigned NOT NULL,
  `IsGlobal` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `FileName` varchar(255) NOT NULL,
  PRIMARY KEY (`SearchID`),
  KEY `AuditTrail_FKIndex1` (`PersonID`),
  CONSTRAINT `Search_Person` FOREIGN KEY (`PersonID`) REFERENCES `People` (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SavedSearches`
--

LOCK TABLES `SavedSearches` WRITE;
/*!40000 ALTER TABLE `SavedSearches` DISABLE KEYS */;
/*!40000 ALTER TABLE `SavedSearches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Storage`
--

DROP TABLE IF EXISTS `Storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Storage` (
  `StorageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Storage` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`StorageID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Storage`
--

LOCK TABLES `Storage` WRITE;
/*!40000 ALTER TABLE `Storage` DISABLE KEYS */;
/*!40000 ALTER TABLE `Storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TemporaryTransfers`
--

DROP TABLE IF EXISTS `TemporaryTransfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TemporaryTransfers` (
  `TemporaryTransferID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ItemID` int(10) unsigned NOT NULL,
  `TransferDate` date NOT NULL,
  `FromLocationID` int(10) unsigned NOT NULL,
  `ToLocationID` int(10) unsigned NOT NULL,
  `TransferType` enum('Transfer','Return') NOT NULL DEFAULT 'Transfer',
  PRIMARY KEY (`TemporaryTransferID`),
  KEY `TemporaryTransfer_From` (`FromLocationID`),
  KEY `TemporaryTransfer_To` (`ToLocationID`),
  KEY `TemporaryTransfer_Item` (`ItemID`),
  CONSTRAINT `TemporaryTransfer_From` FOREIGN KEY (`FromLocationID`) REFERENCES `Locations` (`LocationID`),
  CONSTRAINT `TemporaryTransfer_Item` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=683 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TemporaryTransfers`
--

LOCK TABLES `TemporaryTransfers` WRITE;
/*!40000 ALTER TABLE `TemporaryTransfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `TemporaryTransfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UnlockedItems`
--

DROP TABLE IF EXISTS `UnlockedItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UnlockedItems` (
  `UnlockedID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ItemID` int(10) unsigned NOT NULL,
  `ExpirationDate` date NOT NULL,
  `UnlockedByID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`UnlockedID`),
  KEY `Unlocked_FKIndex1` (`ItemID`),
  KEY `UnlockedBy_FKIndex1` (`UnlockedByID`),
  CONSTRAINT `UnlockedBy_Person` FOREIGN KEY (`UnlockedByID`) REFERENCES `People` (`PersonID`),
  CONSTRAINT `Unlocked_Item` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=454 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UnlockedItems`
--

LOCK TABLES `UnlockedItems` WRITE;
/*!40000 ALTER TABLE `UnlockedItems` DISABLE KEYS */;
/*!40000 ALTER TABLE `UnlockedItems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WorkAssignedTo`
--

DROP TABLE IF EXISTS `WorkAssignedTo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WorkAssignedTo` (
  `PersonID` int(10) unsigned NOT NULL,
  `ItemID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PersonID`,`ItemID`),
  KEY `WorkBy_Person` (`PersonID`),
  KEY `WorkBy_Item` (`ItemID`),
  CONSTRAINT `WorkBy_Item` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`) ON DELETE CASCADE,
  CONSTRAINT `WorkBy_Person` FOREIGN KEY (`PersonID`) REFERENCES `People` (`PersonID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `WorkAssignedTo`
--

LOCK TABLES `WorkAssignedTo` WRITE;
/*!40000 ALTER TABLE `WorkAssignedTo` DISABLE KEYS */;
/*!40000 ALTER TABLE `WorkAssignedTo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WorkTypes`
--

DROP TABLE IF EXISTS `WorkTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WorkTypes` (
  `WorkTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `WorkType` varchar(255) NOT NULL,
  `Inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`WorkTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `WorkTypes`
--

LOCK TABLES `WorkTypes` WRITE;
/*!40000 ALTER TABLE `WorkTypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `WorkTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'acornosdev'
--
/*!50003 DROP FUNCTION IF EXISTS `GetCallNumberString` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acorn`@`localhost` FUNCTION `GetCallNumberString`(IdentID int) RETURNS varchar(1000) CHARSET latin1
BEGIN DECLARE callNumbers VARCHAR(1000) DEFAULT NULL; SELECT GROUP_CONCAT(CallNumber SEPARATOR ', ') INTO callNumbers FROM CallNumbers WHERE IdentificationID = IdentID; RETURN callNumbers; END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GetChargeToText` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acorn`@`localhost` FUNCTION `GetChargeToText`(ChargeToID varchar(50)) RETURNS varchar(255) CHARSET latin1
BEGIN
        DECLARE retval VARCHAR(255) DEFAULT "";
        DECLARE ChargeToLocID INT DEFAULT 0;

        IF ChargeToID != "Project" AND ChargeToID != "Patron" THEN
SET ChargeToLocID = CAST( ChargeToID AS UNSIGNED);
SELECT Location INTO retval
FROM Locations WHERE LocationID = ChargeToLocID;
       ELSE
                SET retval = ChargeToID;
        END IF;

        RETURN retval;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GetConservatorHours` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acorn`@`localhost` FUNCTION `GetConservatorHours`(RepID int, PersID int) RETURNS decimal(10,2)
BEGIN
        DECLARE totalHours DECIMAL(10,2) DEFAULT 0.0;

        IF PersID IS NULL THEN
                SELECT SUM(CompletedHours) INTO totalHours
                FROM ItemConservators WHERE ReportID = RepID;
        ELSE
                SELECT SUM(CompletedHours) INTO totalHours
                FROM ItemConservators WHERE ReportID = RepID AND PersonID = PersID;
        END IF;

        RETURN totalHours;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GetProposalItemCount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acorn`@`localhost` FUNCTION `GetProposalItemCount`(ItID int) RETURNS int(11)
BEGIN
DECLARE itemCount INT DEFAULT 0;
SELECT SUM(TotalCount) INTO itemCount
FROM InitialCounts WHERE ItemID = ItID;
RETURN itemCount;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GetReportItemCount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acorn`@`localhost` FUNCTION `GetReportItemCount`(RepID int) RETURNS int(11)
BEGIN
DECLARE itemCount INT DEFAULT 0;
SELECT SUM(TotalCount) INTO itemCount
FROM ReportCounts WHERE ReportID = RepID;
RETURN itemCount;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GetTreatmentLevel` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acorn`@`localhost` FUNCTION `GetTreatmentLevel`(RepID int) RETURNS int(11)
BEGIN
        DECLARE totalHours, treatmentValue DECIMAL(10,2) DEFAULT 0.0;
        DECLARE totalVolumes, treatmentLevel INT DEFAULT 0;

        SELECT SUM(CompletedHours) INTO totalHours
        FROM ItemConservators WHERE ReportID = RepID;

        SELECT SUM(TotalCount) INTO totalVolumes
        FROM ReportCounts WHERE ReportID = RepID and CountType = 'Volumes';
        IF totalVolumes = 0 THEN
                SET treatmentLevel = 0;
        ELSE
                SET treatmentValue = totalHours/totalVolumes;

                IF treatmentValue >= 2.0 THEN
                        SET treatmentLevel = 3;
                ELSEIF treatmentValue < .25 THEN
                        SET treatmentLevel = 1;
                ELSE
                        SET treatmentLevel = 2;
                END IF;
        END IF;

        RETURN treatmentLevel;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `ChargeTo`
--

/*!50001 DROP TABLE IF EXISTS `ChargeTo`*/;
/*!50001 DROP VIEW IF EXISTS `ChargeTo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ChargeTo` AS select concat(_latin1'Patron',`People`.`PersonID`) AS `ChargeToID`,`People`.`DisplayName` AS `ChargeToName`,_latin1'Patron' AS `ChargeToType` from `People` where (`People`.`Inactive` = 0) union select concat(_latin1'Project',`Projects`.`ProjectID`) AS `ChargeToID`,`Projects`.`ProjectName` AS `ChargeToName`,_latin1'Project' AS `ChargeToType` from `Projects` where (`Projects`.`Inactive` = 0) union select concat(_latin1'Repository',`Locations`.`LocationID`) AS `ChargeToID`,`Locations`.`Location` AS `ChargeToName`,_latin1'Repository' AS `ChargeToType` from `Locations` where ((`Locations`.`Inactive` = 0) and (`Locations`.`IsRepository` = 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `CombinedRecords`
--

/*!50001 DROP TABLE IF EXISTS `CombinedRecords`*/;
/*!50001 DROP VIEW IF EXISTS `CombinedRecords`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`acorn`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `CombinedRecords` AS select 'Item' AS `RecordType`,`t`.`ItemID` AS `RecordID`,`i`.`IdentificationID` AS `IdentificationID`,`i`.`PurposeID` AS `PurposeID`,`i`.`HomeLocationID` AS `HomeLocationID`,`i`.`ChargeToID` AS `ChargeToID`,`i`.`Title` AS `Title`,`i`.`DepartmentID` AS `DepartmentID`,`i`.`GroupID` AS `GroupID`,`i`.`ProjectID` AS `ProjectID`,`i`.`Comments` AS `Comments`,`i`.`Inactive` AS `Inactive`,`i`.`EditCounter` AS `EditCounter`,`i`.`NonDigitalImagesExist` AS `NonDigitalImagesExist`,`i`.`IsBeingEdited` AS `IsBeingEdited`,`i`.`EditedByID` AS `EditedByID`,`i`.`CuratorID` AS `CuratorID`,`i`.`ApprovingCuratorID` AS `ApprovingCuratorID`,`t`.`FormatID` AS `FormatID`,`t`.`CoordinatorID` AS `CoordinatorID`,`t`.`IsNonCollectionMaterial` AS `IsNonCollectionMaterial`,`t`.`ExpectedDateOfReturn` AS `ExpectedDateOfReturn`,`t`.`InsuranceValue` AS `InsuranceValue`,`t`.`FundMemo` AS `FundMemo`,`t`.`AuthorArtist` AS `AuthorArtist`,`t`.`DateOfObject` AS `DateOfObject`,`t`.`CollectionName` AS `CollectionName`,`t`.`Storage` AS `Storage`,`i`.`ManuallyClosed` AS `ManuallyClosed`,`i`.`ManuallyClosedDate` AS `ManuallyClosedDate` from (`ItemIdentification` `i` join `Items` `t` on((`i`.`IdentificationID` = `t`.`IdentificationID`))) union select 'OSW' AS `RecordType`,`t`.`OSWID` AS `RecordID`,`i`.`IdentificationID` AS `IdentificationID`,`i`.`PurposeID` AS `PurposeID`,`i`.`HomeLocationID` AS `HomeLocationID`,`i`.`ChargeToID` AS `ChargeToID`,`i`.`Title` AS `Title`,`i`.`DepartmentID` AS `DepartmentID`,`i`.`GroupID` AS `GroupID`,`i`.`ProjectID` AS `ProjectID`,`i`.`Comments` AS `Comments`,`i`.`Inactive` AS `Inactive`,`i`.`EditCounter` AS `EditCounter`,`i`.`NonDigitalImagesExist` AS `NonDigitalImagesExist`,`i`.`IsBeingEdited` AS `IsBeingEdited`,`i`.`EditedByID` AS `EditedByID`,`i`.`CuratorID` AS `CuratorID`,`i`.`ApprovingCuratorID` AS `ApprovingCuratorID`,NULL AS `FormatID`,NULL AS `CoordinatorID`,0 AS `IsNonCollectionMaterial`,NULL AS `ExpectedDateOfReturn`,NULL AS `InsuranceValue`,NULL AS `FundMemo`,NULL AS `AuthorArtist`,NULL AS `DateOfObject`,NULL AS `CollectionName`,NULL AS `Storage`,`i`.`ManuallyClosed` AS `ManuallyClosed`,`i`.`ManuallyClosedDate` AS `ManuallyClosedDate` from (`ItemIdentification` `i` join `OSW` `t` on((`i`.`IdentificationID` = `t`.`IdentificationID`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-26 14:38:06
