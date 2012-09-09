<?php

class SearchController extends Zend_Controller_Action {

	public function init() {

		// Init code here...

	}

	public function indexAction() {

		$this -> view -> title = 'Search Form';

		if (!Zend_Registry::get('session') -> messages) {
			Zend_Registry::get('session') -> messages = array();
		}
		if (!Zend_Registry::get('session') -> searchData) {
			Zend_Registry::get('session') -> searchData = array();
		}
		if (!Zend_Registry::get('session') -> searchKeys) {
			Zend_Registry::get('session') -> searchKeys = array();
		}

		$this -> view -> searchData = Zend_Registry::get('session') -> searchData;
		$this -> view -> searchKeys = Zend_Registry::get('session') -> searchKeys;

		$this -> view -> notifications = Zend_Registry::get('session') -> messages;

		Zend_Registry::get('session') -> clearMessages = true;

	}

	public function deleteAction() {
		$postData = $this -> _request -> getParams();
		foreach ($postData as $key => $value) {
			if ($key != 'controller' && $key != 'module' && $key != 'action' && $key != 'submit') {
				// Connect to DB using creds stored in application.ini
				$db = Zend_Db_Table_Abstract::getDefaultAdapter();
				// do the delete.
				$where = $db -> quoteInto('pid = ?', $value);
				$result = $db -> delete('participant', $where);
				// Let the user know what happened.
				if ($result) {
					Zend_Registry::get('session') -> messages[] = array('type' => 'success', 'text' => 'Delete Successfull');
				} else {
					Zend_Registry::get('session') -> messages[] = array('type' => 'error', 'text' => 'Delete Failed');
				}
				$this -> _redirect('/search');
			}
		}
	}

	public function queryAction() {

		$postData = $this -> _request -> getParams();

		$queryData = array();
		//we just want enough info here to pick a person out of a list, editing comes later.
		$keys = array('pid', 'bibnumber', 'lastname', 'firstname', 'age','sex', 'timestamp');

		foreach ($keys as $key) {
			$commaKeys = $commaKeys . $key . ",";

		}
		//kill the extra comma
		$commaKeys = substr($commaKeys, 0, -1);

		$sql = "SELECT " . $commaKeys . " FROM `participant` WHERE ";
		foreach ($postData as $key => $value) {
			if ($key != 'controller' && $key != 'module' && $key != 'action' && $key != 'submit') {
				if ($value) {
					$sql = $sql . $key . " like '%" . $value . "%' AND ";

				}
			}
		}
		//cap off the dangling AND
		$sql = $sql . '1=1';

		// Connect to DB using creds stored in application.ini
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		// Do our search
		$result = $db -> fetchAll($sql, 2);

		if ($this -> getRequest() -> getMethod() == 'POST' && $result) {
			$numRows = sizeof($result);
			Zend_Registry::get('session') -> messages[] = array('type' => 'success', 'text' => 'Query returned ' . $numRows . ' results.');
			Zend_Registry::get('session') -> searchData[] = $result;
			Zend_Registry::get('session') -> searchKeys[] = $keys;
		} else {
			Zend_Registry::get('session') -> messages[] = array('type' => 'error', 'text' => 'No Results Found.');

		}
		// Redirect back to page
		$this -> _redirect('/search');

	}

}
