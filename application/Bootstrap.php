<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	protected function _initCOnfig() {
		Zend_Registry::set('config', $this -> getOptions());

	}

}

// Autoload custom namespace
require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader -> registerNamespace('TenK_');

Zend_Session::start();
$mainSession = new Zend_Session_Namespace('TenK');

Zend_Registry::set('session', $mainSession);

if (Zend_Registry::get('session') -> clearMessages) {
	Zend_Registry::get('session') -> messages = array();
	Zend_Registry::get('session') -> postData = array();
	Zend_Registry::get('session') -> searchData = array();
	Zend_Registry::get('session') -> searchKeys = array();
	Zend_Registry::get('session') -> editData = array();
	Zend_Registry::get('session') -> currentPid = array();
	Zend_Registry::get('session') -> clearMessages = false;
}
