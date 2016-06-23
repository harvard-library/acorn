<?php
/**
 * PeopleDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class PeopleDAO extends Zend_Db_Table
{
	const PERSON_ID = 'PersonID';
	const FIRST_NAME = 'FirstName';
	const MIDDLE_NAME = 'MiddleName';
	const LAST_NAME = 'LastName';
	const USERNAME = 'Username';
	const USER_PASSWORD = 'UserPassword';
	const ACCESS_LEVEL = 'AccessLevel';
	const DISPLAY_NAME = 'DisplayName';
	const SORT_NAME = 'SortName';
	const INACTIVE = 'Inactive';
	const INITIALS = 'Initials';
	const EMAIL_ADDRESS = 'EmailAddress';
	const ROLE_TYPE = 'RoleType';
	
	//Access levels
	const ACCESS_LEVEL_ADMIN = 'Admin';
	const ACCESS_LEVEL_REGULAR = 'Regular';
	const ACCESS_LEVEL_REPOSITORY_ADMIN = 'Repository Admin';
	const ACCESS_LEVEL_REPOSITORY = 'Repository';
	const ACCESS_LEVEL_CURATOR = 'Curator';
	
	
    /* The name of the table */
	protected $_name = "People";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "PersonID";

    private static $peopleDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return PeopleDAO
     */
    public static function getPeopleDAO()
	{
		if (!isset(self::$peopleDAO))
		{
			self::$peopleDAO = new PeopleDAO();
		}
		return self::$peopleDAO;
	}

	/**
     * Returns a list of people
     *
     * @access public
     * @param array roletypes an array of role types to match the people to
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array, NULL if no rows found  (key = PeopleID, array of all information)
     */
    public function getAllPeople(array $roletypess = NULL, $includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	return $rows;
    }
    
	/**
     * Returns a list of people formatted for a select list
     *
     * @access public
     * @param array roletypes an array of role types to match the people to
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return JSON ready array
     */
    public function getAllPeopleForSelect(array $roletypes = NULL, $includeinactive = FALSE, $additionalwhere = NULL)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	
    	if (!empty($roletypes))
    	{
    		$select->from($this->_name, array('*'));
    		$select->setIntegrityCheck(FALSE);
    		$select->joinInner('Roles', 'People.PersonID = Roles.PersonID', array());
    		$select->distinct();
    		$in = '(';
    		$delimiter = "";
    		foreach ($roletypes as $roletype)
    		{
    			$in .= $delimiter . '"' . $roletype . '"';
    			$delimiter = ",";
    		}
    		$in .= ')';
    		$select->where('RoleType IN ' . $in);
    	}
    	
    	if (!empty($additionalwhere))
    	{
    		$select->where($additionalwhere);
    	}
    	
    	$select->order('DisplayName');
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
    
    /**
     * Short description of method isLoginValid
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  String username
     * @param  String password
     * @return mixed
     */
    public function getUserWithPassword($userlogin, $password)
	{
		$select = $this->select();
		$userlogin = $this->getAdapter()->quote($userlogin);
    	$password = $this->getAdapter()->quote($password);
    	$select->where("Username=".$userlogin . " AND UserPassword=". $password);
        $row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = $row->toArray();
    	}
    	return $retval;
	}
	
	public function getUser($userlogin)
	{
		$select = $this->select();
		$userlogin = $this->getAdapter()->quote($userlogin);
    	$select->where("Username=".$userlogin);
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
        $row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = $row->toArray();
    	}
    	return $retval;
	}

    /**
     * Gets the person with the given id
     *
     * @access public
     * @param  Integer personID
     * @return Person
     */
    public function getPerson($personID)
    {
    	$select = $this->select();
    	$personID = $this->getAdapter()->quote($personID, 'INTEGER');
    	$select->where('PersonID=' . $personID);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = self::buildPerson($row->toArray());
    	}
    	return $retval;
    }
    
	/**
     * Gets the person with the given id
     *
     * @access public
     * @param  Integer repositoryID
     * @return Person, NULL if no admin assigned.
     */
    public function getRepositoryAdmin($repositoryID)
    {
    	$select = $this->select();
    	$repositoryID = $this->getAdapter()->quote($repositoryID, 'INTEGER');
    	$select->where('AccessLevel="Repository Admin" AND LocationID=' . $repositoryID);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = self::buildPerson($row->toArray());
    	}
    	return $retval;
    }

    /**
     * @access public
     * @param  Person person
     * @return mixed
     */
    public function savePerson(Person $person, Person $oldperson = NULL)
    {
        $array = array(
        	self::FIRST_NAME => $person->getFirstName(),
        	self::MIDDLE_NAME => $person->getMiddleName(),
        	self::LAST_NAME => $person->getLastName(),
        	self::DISPLAY_NAME => $person->getDisplayName(),
        	self::SORT_NAME => $person->getSortName(),
        	self::USERNAME => $person->getUsername(),
        	self::ACCESS_LEVEL => $person->getAccessLevel(),
        	self::INITIALS => $person->getInitials(),
        	self::EMAIL_ADDRESS => $person->getEmailAddress(),
        	LocationDAO::LOCATION_ID => $person->getLocation(),
        	self::INACTIVE => $person->isInactive(),
         );
        
        $db = $this->getAdapter();
		$db->beginTransaction();
		try {
        	$personid = $this->savePersonData($person->getPrimaryKey(), $array, $db);
        	//If the login exists, then make sure there is also a password
        	//If there isn't, then set it to the username.
        	if (!is_null($person->getUsername()))
        	{
        		$user = $this->getUser($person->getUsername());
        		if (empty($user[self::USER_PASSWORD]))
        		{
        			$personid = $this->savePersonData($personid, array(self::USER_PASSWORD => md5($person->getUsername())), $db);
        		}
        	}
        	$roles = $person->getRoles();
        	//Only get the roles that have changed if there was an existing person.
        	if (!is_null($oldperson))
        	{
        		$removalroles = $oldperson->getRoleDifference($roles);
       			$roles = $person->getRoleDifference($oldperson->getRoles());
       			$this->removeRoles($db, $personid, $removalroles);
        	}
        	$this->insertRoles($db, $personid, $roles);
        	$db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$personid = NULL;
   		}
        return $personid;
    }
    
	/*
     * Insert related roles
     */
    private function insertRoles($db, $personid, array $roles)
    {
    	foreach ($roles as $role)
       	{
       		$rolearray = array(
       			self::ROLE_TYPE => trim($role),
       			self::PERSON_ID => $personid
       		);
       		$db->insert('Roles', $rolearray);
       	}
    }
    
	/*
     * Remove related roles
     */
    private function removeRoles($db, $personid, array $roles)
    {
    	foreach ($roles as $role)
       	{
       		$role = $this->getAdapter()->quote($role);
       		$db->delete('Roles', 'PersonID=' . $personid . ' AND RoleType=' . $role);
       	}
    }
    
	/**
     * @access public
     * @param  md5 encoded password
     * @return mixed
     */
    public function savePassword($personid, $password)
    {
    	Logger::log('saving password for ' . $personid . ': ' . $password, Zend_Log::DEBUG);
		$array = array(self::USER_PASSWORD => $password);
	        
		$personid = $this->savePersonData($personid, $array);
		return $personid;
    }
    
    /*
     * Saves the array of data for the given person
     * 
     * @param int personid
     * @param array - database ready
     * @return int personid (NULL if the insert/update failed)
     */
    private function savePersonData($personid = NULL, array $datatosave, $db = NULL)
    {
    	if (is_null($db))
    	{
	    	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try {
		   		if (is_null($personid) || !$this->personExists($personid))
		        {
		        	$db->insert('People', $datatosave);
		        	$personid = $db->lastInsertId();
		        }
		        else 
		        {
		        	$db->update('People', $datatosave, 'PersonID=' . $personid);
		        }
		        $db->commit();
		   	}
	    	catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$personid = NULL;
	   		}
    	}
    	else 
    	{
    		if (is_null($personid) || !$this->personExists($personid))
	        {
	        	$db->insert('People', $datatosave);
	        	$personid = $db->lastInsertId();
	        }
	        else 
	        {
	        	Logger::log('updating person');
	        	$db->update('People', $datatosave, 'PersonID=' . $personid);
	        }
    	}
   		return $personid;
    }
    
	private function personExists($personID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(PersonID)'));
    	$select->where('PersonID=' . $personID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
    
    /*
     * Determines if a login is already in use, excluding the given person.
     * 
     * @param string login
     * @param int excludepersonID - the person to exclude
     */
	public function loginExists($login, $excludepersonID = NULL)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(Username)'));
    	$login = $this->getAdapter()->quote($login);
    	$where = 'Username = ' . $login;
    	if (!is_null($excludepersonID))
    	{
    		$where .= ' AND PersonID <> ' . $excludepersonID;
    	}
    	$select->where($where);
    	$row = $this->fetchRow($select);
    	return $row->Count > 0;
    }
    
	/**
     * Returns an array of people whose first or last name
     * is like the given name
     *
     * @access public
     * @param string name
     * @param array roletypes <optional>
     * @return JSON ready array
     */
    public function getPeople($name, array $roletypes = NULL)
    {
    	$select = $this->select();
    	$select->from($this, array('DisplayName', 'PersonID'));
    	$name = $this->getAdapter()->quote($name . '%');
    	$where = '(FirstName LIKE ' . $name . ' OR LastName LIKE ' . $name . ')';
    	if (!is_null($roletypes))
    	{
    		$select->setIntegrityCheck(FALSE);
    		$select->joinInner('Roles', 'People.PersonID = Roles.PersonID', array());
    		$select->distinct();
    		$in = '(';
    		$delimiter = "";
    		foreach ($roletypes as $roletype)
    		{
    			$in .= $delimiter . '"' . $roletype . '"';
    			$delimiter = ",";
    		}
    		$in .= ')';
    		$where .= ' AND RoleType IN ' . $in;
    	}
    	$select->where($where);
    	$select->order('DisplayName');
    	$select->distinct();
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
    

    /*
     * @access private
     * @param  array values from the database
     * @return Person
     */
    public static function buildPerson(array $values)
    {
    	$person = new Person($values[self::PERSON_ID], $values[self::INACTIVE]);
    	$person->setAccessLevel($values[self::ACCESS_LEVEL]);
    	$person->setDisplayName($values[self::DISPLAY_NAME]);
    	$person->setFirstName($values[self::FIRST_NAME]);
    	$person->setLastName($values[self::LAST_NAME]);
    	$person->setMiddleName($values[self::MIDDLE_NAME]);
    	$person->setUsername($values[self::USERNAME]);
    	$person->setLocation($values[LocationDAO::LOCATION_ID]);
    	$person->setInitials($values[self::INITIALS]);
    	$person->setEmailAddress($values[self::EMAIL_ADDRESS]);
    	return $person;
    }

    /*
     * @access public
     * @param  int personID
     * @return array - Key and Value are both role type
     */
    public function getRoles($personID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$personID = $this->getAdapter()->quote($personID, 'INTEGER');
    	$select->from('Roles', array('RoleType', 'RoleType'));
    	$select->where('PersonID=' . $personID);
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$retval = $this->getAdapter()->fetchPairs($select);
    	return $retval;
    }
} 

?>