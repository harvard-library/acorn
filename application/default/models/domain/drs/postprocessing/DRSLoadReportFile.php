<?php
/**********************************************************************
 * Copyright (c) 2010 by the President and Fellows of Harvard College
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA.
 *
 * Contact information
 *
 * Library Technology Services, Harvard University Information Technology
 * Harvard Library
 * Harvard University
 * Cambridge, MA  02138
 * (617)495-3724
 * hulois@hulmail.harvard.edu
 **********************************************************************/
 
/**
 * The metadata for the files in the DRS Load Report
 *
 * @access public
 * @author Valdeva Crema
 */
class DRSLoadReportFile
{
	
    /**
     * The directory path of the file that was uploaded
     *
     * @access private
     * @var String
     */
    private $filePath;

    /**
     * The name that the user gave the file (probably the same as the filename in the file path).
     *
     * @var String
     */
    private $ownerSuppliedName;
    
    /**
     * The urn for the file (for jpegs/jp2s)
     *
     * @access private
     * @var String
     */
    private $urn;
    
    /**
     * The DRS object id.
     *
     * @access private
     * @var String
     */
    private $objectID;
 
	/**
     * The file type
     *
     * @access private
     * @var String
     */
    private $fileType;

    /**
     * The size of the file
     *
     * @var String
     */
   	private $fileSize;
   	
   	/**
     * The size of the file
     *
     * @var String
     */
   	private $insertionDate;
   	
   	/**
     * The role
     *
     * @var String
     */
   	private $role;
   	
   	/**
     * The purpose
     *
     * @var String
     */
   	private $purpose;
   	
   	/**
     * The quality
     *
     * @var String
     */
   	private $quality;
   	
   	/**
     * The file owner
     *
     * @var String
     */
   	private $owner;
   	
   	/**
     * The md5 signature
     *
     * @var String
     */
   	private $md5;
	
   	
	/**
	 * @return String
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}
	
	/**
	 * @return String
	 */
	public function getFileSize()
	{
		return $this->fileSize;
	}
	
	/**
	 * @return String
	 */
	public function getFileType()
	{
		return $this->fileType;
	}
	
	/**
	 * @return String
	 */
	public function getInsertionDate()
	{
		return $this->insertionDate;
	}
	
	/**
	 * @return String
	 */
	public function getMd5()
	{
		return $this->md5;
	}
	
	/**
	 * @return String
	 */
	public function getObjectID()
	{
		return $this->objectID;
	}
	
	/**
	 * @return String
	 */
	public function getOwner()
	{
		return $this->owner;
	}
	
	/**
	 * @return String
	 */
	public function getOwnerSuppliedName()
	{
		return $this->ownerSuppliedName;
	}
	
	/**
	 * @return String
	 */
	public function getPurpose()
	{
		return $this->purpose;
	}
	
	/**
	 * @return String
	 */
	public function getQuality()
	{
		return $this->quality;
	}
	
	/**
	 * @return String
	 */
	public function getRole()
	{
		return $this->role;
	}
	
	/**
	 * @return String
	 */
	public function getUrn()
	{
		return $this->urn;
	}
	
	/**
	 * @param String $filePath
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;
	}
	
	/**
	 * @param String $fileSize
	 */
	public function setFileSize($fileSize)
	{
		$this->fileSize = $fileSize;
	}
	
	/**
	 * @param String $fileType
	 */
	public function setFileType($fileType)
	{
		$this->fileType = $fileType;
	}
	
	/**
	 * @param String $insertionDate
	 */
	public function setInsertionDate($insertionDate)
	{
		$this->insertionDate = $insertionDate;
	}
	
	/**
	 * @param String $md5
	 */
	public function setMd5($md5)
	{
		$this->md5 = $md5;
	}
	
	/**
	 * @param String $objectID
	 */
	public function setObjectID($objectID)
	{
		$this->objectID = $objectID;
	}
	
	/**
	 * @param String $owner
	 */
	public function setOwner($owner)
	{
		$this->owner = $owner;
	}
	
	/**
	 * @param String $ownerSuppliedName
	 */
	public function setOwnerSuppliedName($ownerSuppliedName)
	{
		$this->ownerSuppliedName = $ownerSuppliedName;
	}
	
	/**
	 * @param String $purpose
	 */
	public function setPurpose($purpose)
	{
		$this->purpose = $purpose;
	}
	
	/**
	 * @param String $quality
	 */
	public function setQuality($quality)
	{
		$this->quality = $quality;
	}
	
	/**
	 * @param String $role
	 */
	public function setRole($role)
	{
		$this->role = $role;
	}
	
	/**
	 * @param String $urn
	 */
	public function setUrn($urn)
	{
		$this->urn = $urn;
	}

} /* end of class DRSLoadReportFile */

?>