<?php
/**
 * LoginDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class LoginDAO extends Zend_Db_Table
{
	const IDENTIFICATION_ID = 'IdentificationID';
	const LOGIN_ID = 'LoginID';
	const LOGIN_BY_ID = 'LoginByID';
	const LOGIN_DATE = 'LoginDate';
	const FROM_LOCATION_ID = 'FromLocationID';
	const TO_LOCATION_ID = 'ToLocationID';
	
	/* The name of the table */
	protected $_name = "ItemLogin";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "LoginID";

    private static $loginDAO;
    private $logintable = NULL;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return LoginDAO
     */
    public static function getLoginDAO()
	{
		if (!isset(self::$loginDAO))
		{
			self::$loginDAO = new LoginDAO();
		}
		return self::$loginDAO;
	}
	
	private function getLoginTable()
	{
		if (is_null($this->logintable))
		{
			$this->logintable = $this->getAdapter()->describeTable('ItemLogin');
		}
		return $this->logintable;
	}
	
    /**
     * Returns the item login with the given id.
     *
     * @access public
     * @param  Integer identificationID
     * @return ItemLogin
     */
    public function getLogin($identificationID)
    {
    	$item = NULL;
	    if (!empty($identificationID))
    	{
	    	$identificationID = $this->getAdapter()->quote($identificationID, 'INTEGER');
	    	$select = $this->select();
	    	$select->where('IdentificationID=' . $identificationID);
	    	$row = $this->fetchRow($select);
	    	if (!is_null($row))
	    	{
	    		$item = $this->buildItemLogin($row->toArray());
	    	}
    	}
    	return $item;
    }
    
    /*
     * @access private
     * @param  array values from the database
     * @return ItemLogin
     */
    private function buildItemLogin(array $values)
    {
    	$login = new Login($values[self::LOGIN_ID]);
    	$login->setIdentificationID($values[self::IDENTIFICATION_ID]);
    	$login->setLoginBy($values[self::LOGIN_BY_ID]);
    	$login->setLoginDate($values[self::LOGIN_DATE]);
    	$login->setTransferFrom($values[self::FROM_LOCATION_ID]);
    	$login->setTransferTo($values[self::TO_LOCATION_ID]);
    	return $login;
    }
    
    public function saveGroupLogins(array $records, Login $modellogin)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$recordssaved = 0;
   		try {
   			foreach ($records as $record)
   			{
   				$loginid = $record->getFunctions()->getLogin()->getPrimaryKey();
   				$login = clone $modellogin;
   				if (!is_null($loginid))
   				{
   					$login->setPrimaryKey($loginid);
   				}
   				$record->getFunctions()->setLogin($login);
   				$loginid = $this->saveItemLogin($record, $db);
   				if (!is_null($loginid))
   				{
   					$recordssaved++;
   				}
   			}
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$recordssaved = 0;
   		}
   		return $recordssaved;
    }
    
	/**
     * Saves the Item Login information
     * @access public
     * @param  Item item
     */
    public function saveItemLogin(Item $item, $db = NULL)
    {
    	$login = $item->getFunctions()->getLogin();
    	$loginid = $login->getPrimaryKey();
        //Build the array to save
        $loginarray = $this->buildLoginArray($login);
        if (is_null($db))
        {
	        $db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
		   		//If the item isn't in the database,
		        //insert it
		        if (is_null($loginid) || !$this->loginExists($loginid))
		        {
		        	$loginarray[ItemDAO::IDENTIFICATION_ID] = $item->getPrimaryKey();
		        	$db->insert('ItemLogin', $loginarray);
		        	$loginid = $db->lastInsertId();
		        }
		        //otherwise, update it
		        else 
		        {
		        	$db->update('ItemLogin', $loginarray, 'LoginID=' . $loginid);	
		        }

		        $db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$loginid = NULL;
	   		}
        }
        else 
        {
        	//If the item isn't in the database,
	        //insert it
	        if (is_null($loginid) || !$this->loginExists($loginid))
	        {
	        	$loginarray[ItemDAO::IDENTIFICATION_ID] = $item->getPrimaryKey();
	        	$db->insert('ItemLogin', $loginarray);
	        	$loginid = $db->lastInsertId();
	        }
	        //otherwise, update it
	        else 
	        {
	        	$db->update('ItemLogin', $loginarray, 'LoginID=' . $loginid);	
		    }
        }
   		return $loginid; 
    }

	/*
     * Data to be loaded into the Item Login table
     */
    private function buildLoginArray(Login $login)
    {
    	//$zenddate = new Zend_Date($login->getLoginDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	//$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$idarray = array(
    		self::LOGIN_BY_ID => $login->getLoginBy(),
    		self::LOGIN_DATE => $login->getLoginDate(),
    		self::FROM_LOCATION_ID => $login->getTransferFrom(),
    		self::TO_LOCATION_ID => $login->getTransferTo()
    	);
    	return $idarray;
    }
    
	private function loginExists($loginID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(LoginID)'));
    	$select->where('LoginID=' . $loginID);
    	$row = $this->fetchRow($select);
    	return $row->Count = 1;
    }
    
	public function deleteLogin(Login $login)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
	   		$rowsdeleted = $db->delete('ItemLogin', 'LoginID=' . $login->getPrimaryKey());	
	        $db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsdeleted = 0;
	   	}
	   	return $rowsdeleted;
    }
    
    public static function isLoginSearch($column)
    {
    	$logintable =  $this->getLoginTable();
    	return isset($logintable[$column]);
    }
} 

?>