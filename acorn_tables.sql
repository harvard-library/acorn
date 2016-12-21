-- MySQL dump 10.11
--
-- Host: localhost    Database: acorntest
-- ------------------------------------------------------
-- Server version	5.0.95

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
  `AuditTrailID` int(10) unsigned NOT NULL auto_increment,
  `PersonID` int(10) unsigned NOT NULL,
  `ActionType` enum('Insert','Update','Delete') NOT NULL default 'Insert',
  `Date` datetime NOT NULL,
  `Details` varchar(1024) default NULL,
  `TableName` varchar(255) NOT NULL,
  `PKID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`AuditTrailID`),
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
  `AutotextID` int(10) unsigned NOT NULL auto_increment,
  `Caption` varchar(255) NOT NULL,
  `Autotext` text,
  `PersonID` int(10) unsigned default NULL,
  `AutotextType` enum('Dependency','Treatment','Description','Condition','Preservation','ProposedTreatment') default NULL,
  `DependentAutotextID` int(10) unsigned default NULL,
  `IsGlobal` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`AutotextID`),
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
  `IdentificationID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`CallNumber`,`IdentificationID`),
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
/*!50001 CREATE TABLE `ChargeTo` (
  `ChargeToID` varbinary(21),
  `ChargeToName` varchar(255),
  `ChargeToType` varchar(10)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `CombinedRecords`
--

DROP TABLE IF EXISTS `CombinedRecords`;
/*!50001 DROP VIEW IF EXISTS `CombinedRecords`*/;
/*!50001 CREATE TABLE `CombinedRecords` (
  `RecordType` varchar(4),
  `RecordID` int(10) unsigned,
  `IdentificationID` int(10) unsigned,
  `PurposeID` int(10) unsigned,
  `HomeLocationID` int(10) unsigned,
  `ChargeToID` varchar(50),
  `Title` varchar(255),
  `DepartmentID` int(10) unsigned,
  `GroupID` int(10) unsigned,
  `ProjectID` int(10) unsigned,
  `Comments` text,
  `Inactive` tinyint(1) unsigned,
  `EditCounter` int(10) unsigned,
  `NonDigitalImagesExist` tinyint(1) unsigned,
  `IsBeingEdited` tinyint(1) unsigned,
  `EditedByID` int(10) unsigned,
  `CuratorID` int(10) unsigned,
  `ApprovingCuratorID` int(10) unsigned,
  `FormatID` int(10) unsigned,
  `CoordinatorID` int(10) unsigned,
  `IsNonCollectionMaterial` tinyint(1) unsigned,
  `ExpectedDateOfReturn` date,
  `InsuranceValue` decimal(10,2),
  `FundMemo` text,
  `AuthorArtist` varchar(255),
  `DateOfObject` varchar(50),
  `CollectionName` varchar(1000),
  `Storage` varchar(255),
  `ManuallyClosed` tinyint(1),
  `ManuallyClosedDate` datetime
) ENGINE=MyISAM */;

--
-- Table structure for table `Departments`
--

DROP TABLE IF EXISTS `Departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Departments` (
  `DepartmentID` int(10) unsigned NOT NULL auto_increment,
  `LocationID` int(10) unsigned NOT NULL,
  `DepartmentName` varchar(255) NOT NULL,
  `ShortName` varchar(50) default NULL,
  `Acronym` varchar(10) default NULL,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`DepartmentID`),
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
  `FileID` int(10) unsigned NOT NULL auto_increment,
  `PKID` int(10) unsigned NOT NULL,
  `Path` varchar(255) NOT NULL,
  `Description` text,
  `FileType` varchar(255) NOT NULL default 'Image',
  `LinkType` enum('Item','OSW','Group') NOT NULL default 'Item',
  `DateEntered` datetime NOT NULL,
  `LastModified` datetime NOT NULL,
  `FileName` varchar(500) NOT NULL,
  `UploadStatus` enum('Pending','Complete') default NULL,
  `EnteredByID` int(10) unsigned default NULL,
  PRIMARY KEY  (`FileID`),
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
  `FormatID` int(10) unsigned NOT NULL auto_increment,
  `Format` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  `Rank` int(10) unsigned NOT NULL default '500',
  PRIMARY KEY  (`FormatID`)
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
  `GroupID` int(10) unsigned NOT NULL auto_increment,
  `GroupName` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  `ProjectID` int(10) unsigned default NULL,
  `IsBeingEdited` tinyint(1) unsigned NOT NULL default '0',
  `EditedByID` int(10) unsigned default NULL,
  PRIMARY KEY  (`GroupID`),
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
  `ImportanceID` int(10) unsigned NOT NULL auto_increment,
  `Importance` varchar(255) NOT NULL,
  `Inactive` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ImportanceID`)
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
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`ItemID`,`CountType`,`TotalCount`),
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
  `DateCompleted` date default NULL,
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
  `IdentificationID` int(10) unsigned NOT NULL auto_increment,
  `PurposeID` int(10) unsigned NOT NULL,
  `HomeLocationID` int(10) unsigned NOT NULL,
  `Title` varchar(255) default NULL,
  `DepartmentID` int(10) unsigned default NULL,
  `GroupID` int(10) unsigned default NULL,
  `ProjectID` int(10) unsigned default NULL,
  `Comments` text,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  `EditCounter` int(10) unsigned default NULL,
  `NonDigitalImagesExist` tinyint(1) unsigned NOT NULL default '0',
  `IsBeingEdited` tinyint(1) unsigned NOT NULL default '0',
  `EditedByID` int(10) unsigned default NULL,
  `CuratorID` int(10) unsigned NOT NULL,
  `ApprovingCuratorID` int(10) unsigned NOT NULL,
  `ManuallyClosed` tinyint(1) NOT NULL default '0',
  `ManuallyClosedDate` datetime default NULL,
  `ChargeToID` varchar(50) default NULL,
  PRIMARY KEY  (`IdentificationID`),
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
  PRIMARY KEY  (`ImportanceID`,`ReportID`),
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
  `LoginID` int(10) unsigned NOT NULL auto_increment,
  `IdentificationID` int(10) unsigned NOT NULL,
  `LoginByID` int(10) unsigned NOT NULL,
  `LoginDate` date NOT NULL,
  `FromLocationID` int(10) unsigned NOT NULL,
  `ToLocationID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`LoginID`),
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
  `LogoutID` int(10) unsigned NOT NULL auto_increment,
  `LogoutByID` int(10) unsigned NOT NULL,
  `IdentificationID` int(10) unsigned NOT NULL,
  `LogoutDate` date NOT NULL,
  `ToLocationID` int(10) unsigned NOT NULL,
  `FromLocationID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`LogoutID`),
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
  `ProposalID` int(10) unsigned NOT NULL auto_increment,
  `IdentificationID` int(10) unsigned NOT NULL,
  `ProposalDate` date NOT NULL,
  `Description` text,
  `Condition` text,
  `Treatment` text,
  `MinimumProposedHours` decimal(10,2) NOT NULL,
  `MaximumProposedHours` decimal(10,2) NOT NULL,
  `Height` decimal(10,2) default NULL,
  `Width` decimal(10,2) default NULL,
  `Thickness` decimal(10,2) default NULL,
  `DimensionUnit` enum('cm','in') default NULL,
  `ExamDate` date default NULL,
  `ExamLocationID` int(10) unsigned default NULL,
  PRIMARY KEY  (`ProposalID`),
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
  `ReportID` int(10) unsigned NOT NULL auto_increment,
  `FormatID` int(10) unsigned default NULL,
  `IdentificationID` int(10) unsigned NOT NULL,
  `ReportByID` int(10) unsigned NOT NULL,
  `ReportDate` date NOT NULL,
  `Treatment` text,
  `Summary` text,
  `Height` decimal(10,2) default NULL,
  `Width` decimal(10,2) default NULL,
  `Thickness` decimal(10,2) default NULL,
  `DimensionUnit` varchar(10) default NULL,
  `WorkLocationID` int(10) unsigned NOT NULL,
  `ExamOnly` tinyint(1) unsigned NOT NULL default '0',
  `CustomHousingOnly` tinyint(1) unsigned NOT NULL default '0',
  `AdminOnly` tinyint(1) unsigned NOT NULL default '0',
  `PreservationRecommendations` text,
  `AdditionalMaterialsOnFile` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ReportID`),
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
  `ItemID` int(10) unsigned NOT NULL auto_increment,
  `FormatID` int(10) unsigned default NULL,
  `IdentificationID` int(10) unsigned NOT NULL,
  `CoordinatorID` int(10) unsigned NOT NULL,
  `IsNonCollectionMaterial` tinyint(1) unsigned NOT NULL default '0',
  `Fund` varchar(255) default NULL,
  `InsuranceValue` decimal(10,2) default NULL,
  `FundMemo` text,
  `AuthorArtist` varchar(255) default NULL,
  `DateOfObject` varchar(50) default NULL,
  `EditCounter` int(10) unsigned default NULL,
  `CollectionName` varchar(1000) default NULL,
  `Storage` varchar(255) default NULL,
  `ExpectedDateOfReturn` date default NULL,
  PRIMARY KEY  (`ItemID`),
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
  `LocationHistoryID` int(10) unsigned NOT NULL auto_increment,
  `ItemID` int(10) unsigned NOT NULL,
  `DateIn` datetime NOT NULL,
  `LocationID` int(10) unsigned NOT NULL,
  `DateOut` datetime default NULL,
  `IsPartial` tinyint(1) unsigned NOT NULL default '0',
  `IsTemporary` tinyint(1) unsigned NOT NULL default '0',
  `PartialComments` text,
  PRIMARY KEY  (`LocationHistoryID`),
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
  `LocationID` int(10) unsigned NOT NULL auto_increment,
  `Location` varchar(255) NOT NULL,
  `TUB` varchar(100) default NULL,
  `ShortName` varchar(50) default NULL,
  `Acronym` varchar(10) default NULL,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  `IsRepository` tinyint(1) unsigned NOT NULL default '1',
  `IsWorkLocation` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`LocationID`)
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
  `OSWID` int(10) unsigned NOT NULL auto_increment,
  `IdentificationID` int(10) unsigned default NULL,
  `ProposedHours` decimal(10,2) default NULL,
  `WorkStartDate` date default NULL,
  `WorkEndDate` date default NULL,
  `FormatID` int(10) unsigned default NULL,
  PRIMARY KEY  (`OSWID`),
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
  PRIMARY KEY  (`OSWID`,`WorkTypeID`),
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
  `PersonID` int(10) unsigned NOT NULL auto_increment,
  `LocationID` int(10) unsigned default NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) default NULL,
  `LastName` varchar(50) NOT NULL,
  `Username` varchar(50) default NULL,
  `UserPassword` varchar(50) default NULL,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  `AccessLevel` enum('Admin','Regular','Repository Admin','Repository','None','Curator') NOT NULL default 'Regular',
  `DisplayName` varchar(255) NOT NULL,
  `SortName` varchar(255) NOT NULL,
  `Initials` varchar(50) default NULL,
  `EmailAddress` varchar(255) default NULL,
  PRIMARY KEY  (`PersonID`),
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
  `ProjectID` int(10) unsigned NOT NULL auto_increment,
  `ProjectName` varchar(255) NOT NULL,
  `StartDate` date default NULL,
  `EndDate` date default NULL,
  `Inactive` tinyint(3) unsigned NOT NULL default '0',
  `ProjectDescription` text,
  `IsBeingEdited` tinyint(1) unsigned NOT NULL default '0',
  `EditedByID` int(10) unsigned default NULL,
  PRIMARY KEY  (`ProjectID`)
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
  `HistoryID` int(10) unsigned NOT NULL auto_increment,
  `PKID` int(10) unsigned NOT NULL,
  `RecordType` enum('Item','Group') NOT NULL default 'Item',
  `ActivityType` varchar(255) NOT NULL,
  `Details` text,
  `AuthorID` int(10) unsigned NOT NULL,
  `DateEntered` date NOT NULL,
  PRIMARY KEY  (`HistoryID`),
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
  PRIMARY KEY  (`PersonID`,`ProposalID`),
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
  `PurposeID` int(10) unsigned NOT NULL auto_increment,
  `Purpose` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`PurposeID`)
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
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`ReportID`,`CountType`,`TotalCount`),
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
  PRIMARY KEY  (`PersonID`,`RoleType`),
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
  `SearchID` int(10) unsigned NOT NULL auto_increment,
  `SearchName` varchar(255) NOT NULL,
  `PersonID` int(10) unsigned NOT NULL,
  `IsGlobal` tinyint(1) unsigned NOT NULL default '0',
  `FileName` varchar(255) NOT NULL,
  PRIMARY KEY  (`SearchID`),
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
  `StorageID` int(10) unsigned NOT NULL auto_increment,
  `Storage` varchar(255) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`StorageID`)
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
  `TemporaryTransferID` int(10) unsigned NOT NULL auto_increment,
  `ItemID` int(10) unsigned NOT NULL,
  `TransferDate` date NOT NULL,
  `FromLocationID` int(10) unsigned NOT NULL,
  `ToLocationID` int(10) unsigned NOT NULL,
  `TransferType` enum('Transfer','Return') NOT NULL default 'Transfer',
  PRIMARY KEY  (`TemporaryTransferID`),
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
  `UnlockedID` int(10) unsigned NOT NULL auto_increment,
  `ItemID` int(10) unsigned NOT NULL,
  `ExpirationDate` date NOT NULL,
  `UnlockedByID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`UnlockedID`),
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
  PRIMARY KEY  (`PersonID`,`ItemID`),
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
  `WorkTypeID` int(10) unsigned NOT NULL auto_increment,
  `WorkType` varchar(255) NOT NULL,
  `Inactive` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`WorkTypeID`)
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
-- Final view structure for view `ChargeTo`
--

/*!50001 DROP TABLE `ChargeTo`*/;
/*!50001 DROP VIEW IF EXISTS `ChargeTo`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `ChargeTo` AS select concat(_latin1'Patron',`People`.`PersonID`) AS `ChargeToID`,`People`.`DisplayName` AS `ChargeToName`,_latin1'Patron' AS `ChargeToType` from `People` where (`People`.`Inactive` = 0) union select concat(_latin1'Project',`Projects`.`ProjectID`) AS `ChargeToID`,`Projects`.`ProjectName` AS `ChargeToName`,_latin1'Project' AS `ChargeToType` from `Projects` where (`Projects`.`Inactive` = 0) union select concat(_latin1'Repository',`Locations`.`LocationID`) AS `ChargeToID`,`Locations`.`Location` AS `ChargeToName`,_latin1'Repository' AS `ChargeToType` from `Locations` where ((`Locations`.`Inactive` = 0) and (`Locations`.`IsRepository` = 1)) */;

--
-- Final view structure for view `CombinedRecords`
--

/*!50001 DROP TABLE `CombinedRecords`*/;
/*!50001 DROP VIEW IF EXISTS `CombinedRecords`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`acorn`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `CombinedRecords` AS (select _latin1'Item' AS `RecordType`,`t`.`ItemID` AS `RecordID`,`i`.`IdentificationID` AS `IdentificationID`,`i`.`PurposeID` AS `PurposeID`,`i`.`HomeLocationID` AS `HomeLocationID`,`i`.`ChargeToID` AS `ChargeToID`,`i`.`Title` AS `Title`,`i`.`DepartmentID` AS `DepartmentID`,`i`.`GroupID` AS `GroupID`,`i`.`ProjectID` AS `ProjectID`,`i`.`Comments` AS `Comments`,`i`.`Inactive` AS `Inactive`,`i`.`EditCounter` AS `EditCounter`,`i`.`NonDigitalImagesExist` AS `NonDigitalImagesExist`,`i`.`IsBeingEdited` AS `IsBeingEdited`,`i`.`EditedByID` AS `EditedByID`,`i`.`CuratorID` AS `CuratorID`,`i`.`ApprovingCuratorID` AS `ApprovingCuratorID`,`t`.`FormatID` AS `FormatID`,`t`.`CoordinatorID` AS `CoordinatorID`,`t`.`IsNonCollectionMaterial` AS `IsNonCollectionMaterial`,`t`.`ExpectedDateOfReturn` AS `ExpectedDateOfReturn`,`t`.`InsuranceValue` AS `InsuranceValue`,`t`.`FundMemo` AS `FundMemo`,`t`.`AuthorArtist` AS `AuthorArtist`,`t`.`DateOfObject` AS `DateOfObject`,`t`.`CollectionName` AS `CollectionName`,`t`.`Storage` AS `Storage`,`i`.`ManuallyClosed` AS `ManuallyClosed`,`i`.`ManuallyClosedDate` AS `ManuallyClosedDate` from (`ItemIdentification` `i` join `Items` `t` on((`i`.`IdentificationID` = `t`.`IdentificationID`)))) */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-04  8:17:11
