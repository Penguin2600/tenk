<?php

class InsertController extends Zend_Controller_Action {

	public function init() {

	}

	public function indexAction() {

		$this -> view -> title = 'Insert Form';
		$this -> view -> eventOptions = $this -> getOptions('event');
		$this -> view -> registrationOptions = $this -> getOptions('registration');
		$this -> view -> divisionOptions = $this -> getOptions('division');
		$this -> view -> sizeOptions = $this -> getOptions('size');

		if (!Zend_Registry::get('session') -> messages) {
			Zend_Registry::get('session') -> messages = array();
		}

		if (!Zend_Registry::get('session') -> postData) {
			Zend_Registry::get('session') -> postData = array();
		}
		if (!Zend_Registry::get('session') -> nextBib) {
			Zend_Registry::get('session') -> nextBib = array();
		}

		$this -> view -> postData = Zend_Registry::get('session') -> postData;
		$this -> view -> notifications = Zend_Registry::get('session') -> messages;
		$this -> view -> nextBib = Zend_Registry::get('session') -> nextBib;

		Zend_Registry::get('session') -> clearMessages = true;

	}

	public function postAction() {

		// Connect to DB using creds stored in application.ini
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$postData = $this -> _request -> getParams();
		//Build insert data the easy new way
		$insertData = array();
		foreach ($postData as $key => $value) {
			if ($key != 'controller' && $key != 'module' && $key != 'action' && $key != 'submit') {
				$insertData[$key] = $value;
			}
		}


		//Build insert data the old way
		//	$insertData = array(
		//'bibnumber' => $postData -> bibnumber,
		//'lastname' => $postData -> lastname,
		//'firstname' => $postData -> firstname,
		//'address1' => $postData -> address1,
		//'address2' => $postData -> address2,
		//'city' => $postData -> city,
		//'state' => $postData -> state,
		//'age' => $postData -> age,
		//'sex' => $postData -> sex,
		//'zipcode' => $postData -> zipcode,
		//'email' => $postData -> email,
		//'shirtsize' => $postData -> shirtsize,
		//'event' => $postData -> event,
		//'division' => $postData -> division,
		//'team' => $postData -> team,
		//'regtype' => $postData -> regtype );

		Zend_Registry::get('session') -> postData[] = $insertData;

		// Insert the data and
		// Set the success/fail message $db -> insert('participant', $insertData)
		$valid = $this -> validate($insertData);

		if ($this -> getRequest() -> getMethod() == 'POST' && $valid == "valid") {
			$db -> insert('participant', $insertData);
			Zend_Registry::get('session') -> messages[] = array('type' => 'success', 'text' => 'Last Insert Was Successfull.');
		} else {
			Zend_Registry::get('session') -> messages[] = array('type' => 'error', 'text' => 'Last Insert Failed: ' . $valid);
		}

		// Redirect back to page
		$this -> _redirect('/insert');
	}

	public function validate($insertData) {
		$helpers = new TenK_Helpers;
		$options = $helpers -> validate($insertData, 1);
		return $options;
	}

	public function checkbibAction() {

		//this is called from ajax, we dont want to autorender here
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		$postData = $this -> _request -> getParams();
		$bibNumber = $postData['bib'];
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = "SELECT * FROM participant WHERE bibnumber = '" . $bibNumber . "'";
		$result = $db -> fetchAll($sql, 2);
		if (sizeof($result)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function getOptions($table) {
		$helpers = new TenK_Helpers;
		$options = $helpers -> getOptions($table);
		return $options;
	}

}
