<?php

class EditController extends Zend_Controller_Action {

	public function init() {
	}

	public function indexAction() {
		$this -> view -> title = 'Edit Form';

		$this -> view -> eventOptions = $this -> getOptions('event');
		$this -> view -> registrationOptions = $this -> getOptions('registration');
		$this -> view -> divisionOptions = $this -> getOptions('division');
		$this -> view -> sizeOptions = $this -> getOptions('size');

		if (!Zend_Registry::get('session') -> messages) {
			Zend_Registry::get('session') -> messages = array();
		}
		if (!Zend_Registry::get('session') -> editData) {
			Zend_Registry::get('session') -> editData = array();
		}

		$this -> view -> editData = Zend_Registry::get('session') -> editData;
		$this -> view -> notifications = Zend_Registry::get('session') -> messages;
		Zend_Registry::get('session') -> clearMessages = true;
	}

	public function pulleditAction() {
		$postData = $this -> _request -> getParams();
		foreach ($postData as $key => $pid) {
			if ($key != 'controller' && $key != 'module' && $key != 'action' && $key != 'submit') {
				// do the lookup
				$sql = "SELECT * FROM participant WHERE pid=" . $pid;
				// Connect to DB using creds stored in application.ini
				$db = Zend_Db_Table_Abstract::getDefaultAdapter();
				// Do our search
				$result = $db -> fetchAll($sql, 2);
				Zend_Registry::get('session') -> editData = $result;
				// Redirect back to page
				$this -> _redirect('/edit');

			}
		}
	}

	public function pusheditAction() {
		// Connect to DB using creds stored in application.ini
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$postData = $this -> _request -> getParams();

		//Build insert data the easy new way
		$insertData = array();
		foreach ($postData as $key => $value) {
			if ($key != 'controller' && $key != 'module' && $key != 'action' && $key != 'submit') {
				if ($key == 'pid') {
					$pid = $value;
				} else {
					$insertData[$key] = $value;
				}
			}
		}

		Zend_Registry::get('session') -> postData[] = $insertData;

		// Update the data and
		// Set the success/fail message

		$valid = $this -> validate($insertData);

		if ($this -> getRequest() -> getMethod() == 'POST' && $valid == "valid") {
			$result = $db -> update('participant', $insertData, "pid = '" . $pid . "'");
			Zend_Registry::get('session') -> messages[] = array('type' => 'success', 'text' => 'Update Was Successfull.');
		} else {
			Zend_Registry::get('session') -> messages[] = array('type' => 'error', 'text' => 'Update Failed: ' . $valid);
		}

		// Redirect back to page
		$this -> _redirect('/edit/pulledit?id=' . $pid);
	}

	public function validate($insertData) {
		$helpers = new TenK_Helpers;
		$options = $helpers -> validate($insertData,0);
		return $options;
	}

	public function getOptions($table) {
		$helpers = new TenK_Helpers;
		$options = $helpers -> getOptions($table);
		return $options;
	}

}
