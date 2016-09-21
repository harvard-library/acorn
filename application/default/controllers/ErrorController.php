<?php
/**
 * Copyright 2016 The President and Fellows of Harvard College
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

class ErrorController extends Zend_Controller_Action
{
	public function errorAction() 
    { 
        // Ensure the default view suffix is used so we always return good 
        // content
        $this->_helper->viewRenderer->setViewSuffix('phtml');

        // Grab the error object from the request
        $errors = $this->_getParam('error_handler'); 

        // $errors will be an object set as a parameter of the request object, 
        // type is a property
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 

                // 404 error -- controller or action not found 
                $this->getResponse()->setHttpResponseCode(404); 
                $this->view->message = 'Page not found'; 
                break; 
            default: 
                // application error 
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->message = 'Application error'; 
                break; 
        } 

        // pass the actual exception object to the view
        $this->view->exception = $errors->exception; 
        
        // pass the request to the view
        $this->view->request   = $errors->request; 
    } 
	
}

?>