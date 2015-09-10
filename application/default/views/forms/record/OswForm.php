<?php
/**
 * OswForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class OswForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$oswidentificationform = new OswIdentificationForm();
    	$oswidentificationform->setName('oswidentificationform');
    	$this->addSubForm($oswidentificationform, 'oswidentificationform');
    	
    	$fileform = new FilesForm();
    	$fileform->setName('fileform');
    	$this->addSubForm($fileform, 'fileform');
    	
    }
    
    
}
?>