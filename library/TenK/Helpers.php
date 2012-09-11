<?php

class TenK_Helpers {

	public function getOptions($table) {

		// Connect to DB using creds stored in application.ini
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		//get our item list
		$sql = 'SELECT * FROM ' . $table;
		$result = $db -> fetchAll($sql, 2);
		return $result;

	}

	public function validate($insertData, $checkBib) {
		$valid = "valid";
		foreach ($insertData as $key => $value) {

			if ($key == "bibnumber") {
				if (!is_numeric($value)) {
					$valid = "bib not numeric";
				}

				if ($this -> checkBibConflicts($value) && $checkBib) {
					$valid = "bib already in use";
				}
			}

			if ($key == "firstname") {
				if (strlen($value) < 1) {
					$valid = "no firstname";
				}

			}
			if ($key == "lastname") {
				if (strlen($value) < 1) {
					$valid = "no lastname";
				}

			}

			if ($key == "state") {
				if (!preg_match('/[a-zA-Z]{2}/', $value, $matches)) {
					$valid = "invalid state";
				}
			}

			if ($key == "sex") {
				if (!preg_match('/[A-Z]{1}/', $value, $matches)) {
					$valid = "invalid sex";
				}

			}
			if ($key == "zipcode") {
				if (!is_numeric($value)) {
					$valid = "zipcode not numeric";
				}

			}
			if ($key == "age") {
				if (!is_numeric($value)) {
					$valid = "age not numeric";
				}

			}

		}
		return $valid;
	}

	public function checkBibConflicts($bibNumber) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = "SELECT * FROM participant WHERE bibnumber = '" . $bibNumber . "'";
		$result = $db -> fetchAll($sql, 2);
		if (sizeof($result)) {
			return true;
		} else {
			return false;
		}
	}

}
?>