<?php
class Person extends AcornClass
{
	private $displayName = NULL;
    private $firstName = NULL;
	private $lastName = NULL;
	private $middleName = NULL;
	private $location = NULL;
	private $accessLevel = NULL;
	private $username = NULL;
	private $initials = NULL;
	private $emailAddress = NULL;
	
	/**
	 * @return mixed
	 */
	public function getEmailAddress()
	{
		return $this->emailAddress;
	}
	
	/**
	 * @param mixed $emailAddress
	 */
	public function setEmailAddress($emailAddress)
	{
		$this->emailAddress = $emailAddress;
	}

	private $roles = NULL;


    /**
     * @access public
     * @return mixed
     */
    public function getDisplayName($reset = FALSE)
    {
    	if ($reset)
    	{
    		$this->displayName = $this->firstName;
    		if (!empty($this->middleName))
    		{
    			$this->displayName .= ' ' . $this->middleName;
    		}
    		$this->displayName .= ' ' . $this->lastName;
    	}
    	return $this->displayName;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getSortName()
    {
    	$sortname = $this->lastName;
    	$sortname .= ', ' . $this->firstName;
    	if (!empty($this->middleName))
    	{
    		$sortname .= ' ' . $this->middleName;
    	}
    	return $sortname;
    }	
    
	/**
     * @access public
     * @return mixed
     */
    public function getInitials()
    {
    	return $this->initials;
    }
    
 	/**
     * @access public
     * @return mixed
     */
    public function getFirstName()
    {
    	return $this->firstName;
    }
    
 	/**
     * @access public
     * @return mixed
     */
    public function getMiddleName()
    {
    	return $this->middleName;
    }
    
 	/**
     * @access public
     * @return mixed
     */
    public function getLastName()
    {
    	return $this->lastName;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getUsername()
    {
    	return $this->username;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getAccessLevel()
    {
    	return $this->accessLevel;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getLocation()
    {
    	return $this->location;
    }

    /**
    * @access public
     * @param  string accessLevel
     */
    public function setAccessLevel($accessLevel)
    {
    	$this->accessLevel = $accessLevel;
    }
    
    /**
     * @access public
     * @param  String username
     */
    public function setUsername($username)
    { 
    	$this->username = $username;
    }
    
	/**
     * @access public
     * @param  String initials
     */
    public function setInitials($initials)
    { 
    	$this->initials = $initials;
    }
    

    /**
     * @access public
     * @param  String firstName
     */
    public function setFirstName($firstName)
    { 
    	$this->firstName = $firstName;
    }

    /**
     * @access public
     * @param  String middleName
     */
    public function setMiddleName($middleName)
    {
    	$this->middleName = $middleName;
    }

    /**
     * @access public
     * @param  String lastName
     */
    public function setLastName($lastName)
    {
    	$this->lastName = $lastName;
    }
    
	/**
     * @access public
     * @param  String displayName
     */
    public function setDisplayName($displayName)
    {
    	$this->displayName = $displayName;
    }

    /**
     * @access public
     * @param  location
     */
    public function setLocation($location)
    {
    	$this->location = $location;
    }

   	/**
     * @access public
     * @return mixed
     */
    public function getAuditTrailForPerson()
    {
    	return NULL;
    }
	
	/**
     * @access public
     * @return mixed
     */
    public function getRoles()
    {
    	if (is_null($this->roles))
    	{
    		$this->roles = PeopleDAO::getPeopleDAO()->getRoles($this->getPrimaryKey());
    	}
    	return $this->roles;
    }
    
	/**
     * @access public
     * @param  array roles
     */
    public function setRoles(array $roles)
    {
    	$this->roles = $roles;
    }
    
	/**
     * @access public
     * @param  string role
     */
    public function addRole($role)
    {
    	$roles = $this->getRoles();
    	$roles[$role] = $role;
    	$this->setRoles($roles);
    	$this->updateNamespacePerson();
    }
    
	/**
     * @access public
     * @param  string role
     */
    public function removeRole($role)
    {
    	$roles = $this->getRoles();
    	unset($roles[$role]);
    	$this->setRoles($roles);
    	$this->updateNamespacePerson();
    }
    
	protected function updateNamespacePerson()
    {
    	$currentperson = PeopleNamespace::getCurrentPerson();
    	if ($currentperson->getPrimaryKey() == $this->getPrimaryKey())
    	{
    		PeopleNamespace::setCurrentPerson($this);
    	}
    }
    
	/*
     * Returns the array of roles that are in this person's roles
     * and are not in the array of compare roles
     * @param array - roles to compare
     * @return array - roles not in given array
     */
    public function getRoleDifference(array $compareroles)
    {
    	return array_diff($this->getRoles(), $compareroles);
    }
    
	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case PeopleDAO::DISPLAY_NAME:
    			$this->setDisplayName($newdata);
    			break;
    		case PeopleDAO::FIRST_NAME:
    			$this->setFirstName($newdata);
    			break;
    		case PeopleDAO::MIDDLE_NAME:
    			$this->setMiddleName($newdata);
    			break;
    		case PeopleDAO::LAST_NAME:
    			$this->setLastName($newdata);
    			break;
    		case PeopleDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    	}
    }
    
    public function __toString()
    {
    	return $this->getDisplayName();;
    }
} 

?>