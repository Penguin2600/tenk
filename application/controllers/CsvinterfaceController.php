<?php

class CsvinterfaceController extends Zend_Controller_Action {

	public function init() {
	}

	public function indexAction() {

		$this -> view -> title = 'CSV Interface Form';

		if (!Zend_Registry::get('session') -> messages) {
			Zend_Registry::get('session') -> messages = array();
		}

		$this -> view -> notifications = Zend_Registry::get('session') -> messages;

		Zend_Registry::get('session') -> clearMessages = true;

	}

	public function exportAction() {
		$config = Zend_Registry::get('config');

		$exportPath = $config['exportpath'];
		$postData = $this -> _request -> getParams();
		$startBib = $postData['startbib'];
		$endBib = $postData['endbib'];
		$exportType = $postData['exportType'];
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		if ($exportType == "bib") {
			$filePart = "bib" . time() . ".csv";
			$fileName = $exportPath . $filePart;
			$dlPath = "/exports/" . $filePart;
			$sql = "(SELECT 'firstname','lastname','sex','age','size','bibnumber','event','team') UNION (SELECT firstname,lastname,sex,age,size.shortname,bibnumber,event.name,team INTO OUTFILE '" . $fileName . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' FROM participant, size, event WHERE participant.event=event.eid and participant.size=size.sid and bibnumber >= " . $startBib . " and bibnumber <= " . $endBib . " ORDER BY bibnumber);";
			$db -> query($sql);

		}

		if ($exportType == "mail") {
			$filePart = "mail" . time() . ".csv";
			$fileName = $exportPath . $filePart;
			$dlPath = "/exports/" . $filePart;

			$sql = "(SELECT 'firstname','lastname','sex','age','size','bibnumber','address1','address2','city','state','zipcode') UNION (SELECT firstname,lastname,sex,age,size.shortname,bibnumber,address1,address2,city,state,zipcode INTO OUTFILE '" . $fileName . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' FROM participant, size WHERE participant.size=size.sid and bibnumber >= " . $startBib . " and bibnumber <= " . $endBib . " ORDER BY bibnumber) ORDER BY ABS(bibnumber) ASC;";
			$db -> query($sql);

		}
		if ($exportType == "time") {
			$filePart = "time" . time() . ".csv";
			$fileName = $exportPath . $filePart;
			$dlPath = "/exports/" . $filePart;

			$sql = "(SELECT 'firstname','lastname','sex','age','bibnumber','event','team','division','city','state') UNION (SELECT firstname,lastname,sex,age,bibnumber,event.shortname,team, division.altname, city, state INTO OUTFILE '" . $fileName . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' FROM participant, division, event WHERE participant.event=event.eid and participant.division=division.did and bibnumber >= " . $startBib . " and bibnumber <= " . $endBib . ") ORDER BY ABS(bibnumber) ASC;";
			$db -> query($sql);
		}

		if ($exportType == "all") {
			$filePart = "all" . time() . ".csv";
			$fileName = $exportPath . $filePart;
			$dlPath = "/exports/" . $filePart;

			$sql = "(SELECT 'activeid','bibnumber','lastname','firstname','address1','address2','city','state','zipcode','email','age','sex','size','event','division','team','registration','timestamp') UNION (SELECT activeid,bibnumber,lastname,firstname,address1,address2,city,state,zipcode,email,age,sex,size.shortname,event.shortname,division.altname,team,registration.shortname,timestamp INTO OUTFILE '" . $fileName . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' FROM participant, size, event, division, registration WHERE participant.event=event.eid and participant.size=size.sid and participant.division=division.did and participant.registration=registration.rid and bibnumber >= " . $startBib . " and bibnumber <= " . $endBib . ") ORDER BY ABS(bibnumber) ASC;";
			$db -> query($sql);
		}
		Zend_Registry::get('session') -> messages[] = array('type' => 'success', 'text' => 'CSV Export Ready: <a href="' . $dlPath . '">Click Here to Download</a>');
		$this -> _redirect('/csvinterface');

	}

	public function importCSV($filePath, $bibstart) {
		//map active.com columns to tenk database columns (manually, sucks)
		$keyMap = array('activeid' => 0, 'lastname' => 1, 'firstname' => 2, 'address1' => 3, 'city' => 4, 'state' => 5, 'zipcode' => 6, 'email' => 7, 'age' => 8, 'sex' => 9, 'size' => 10, 'event' => 11, 'division' => 12, 'team' => 13, 'registration' => 14);
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$lineCount = 0;
		$recordsInserted = 0;
		$recordsRead = 0;
		$bibNumber = $bibstart;

		if (($handle = fopen($filePath, "r")) !== FALSE) {

			while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {
				$lineCount++;
				$insertData = array();

				if ($lineCount >= 2) {//SKIP THE HEADER
					foreach ($keyMap as $key => $column) {
						//teamname special rules
						if ($key == 'team') {
							//get both teamname fields and insert them in the proper place
							$insertData[$key] = $data[$column] . $data[15];

						} elseif ($key == 'size') {
							$result = $this -> findLike('sid', $key, $data[$column]);
							if (sizeof($result)) {
								$insertData[$key] = $result;

							} else {
								$insertData[$key] = 8;
								//unknown size
							}

							//division special rules, dont set if already set by event
						} elseif ($key == 'division') {
							if (!isset($insertData['division'])) {
								$result = $this -> findLike('did', $key, $data[$column]);
								if (sizeof($result)) {
									$insertData[$key] = $result;

								} else {
									$insertData[$key] = 1;
									//default to NONE
								}
							}

							//event special rules HALFC causes overwrite of division and event to HALF
						} elseif ($key == 'event') {
							if ($data[$column] == "HALFC") {
								$insertData[$key] = 6;
								//event value
								$insertData['division'] = 5;
								//division value
							} else {
								$result = $this -> findLike('eid', $key, $data[$column]);
								$insertData[$key] = $result;
							}

							//registration special rules
						} elseif ($key == 'registration') {
							$result = $this -> findLike('rid', $key, $data[$column]);
							if (sizeof($result)) {
								$insertData[$key] = $result;

							} else {
								$insertData[$key] = 3;
								// Default To Packet Pickup
							}

						} else {
							$insertData[$key] = $data[$column];
						}
					}// do insert with verified free bib number and only if activeid is unique
					$recordsRead++;
					if ($this -> checkConflicts($bibNumber, $insertData['activeid'])) {
						$insertData['bibnumber'] = $bibNumber;
						$db -> insert('participant', $insertData);
						$recordsInserted++;
						$bibNumber++;
					}
				}
			}
			fclose($handle);
			Zend_Registry::get('session') -> messages[] = array('type' => 'success', 'text' => 'CSV Import Complete, Last Bib: ' . $bibNumber . " Read: " . $recordsRead . " Inserted: " . $recordsInserted);
			$this -> _redirect('/csvinterface');

		}

	}

	public function checkConflicts($bibNumber, $activeid) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = "SELECT * FROM participant WHERE bibnumber = '" . $bibNumber . "'";
		$result = $db -> fetchAll($sql, 2);
		if (sizeof($result)) {
			Zend_Registry::get('session') -> messages[] = array('type' => 'error', 'text' => 'Bib Number ' . $bibNumber . ' In Use');
			$this -> _redirect('/csvinterface');
		}

		$sql = "SELECT * FROM participant WHERE activeid = '" . $activeid . "'";
		$result = $db -> fetchAll($sql, 2);
		if (sizeof($result)) {
			return 0;
		} else {
			return 1;
		}
	}

	public function uploadcsvAction() {

		if ($this -> getRequest() -> isPost()) {
			$config = Zend_Registry::get('config');
			$destination = $config['uploadpath'];
			$postData = $this -> _request -> getParams();
			$upload = new Zend_File_Transfer_Adapter_Http();
			$upload -> setDestination($destination);
			if ($upload -> receive()) {
				$filename = $upload -> getFileName();
				$this -> importCSV($filename, $postData['bibstart']);
			} else {
				echo "fail";
				die();
			}

		}

	}

	public function findLike($id, $table, $data) {

		$sql = "SELECT " . $id . " FROM " . $table . " WHERE shortname like '" . $data . "'";
		// Connect to DB using creds stored in application.ini
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		// Search for reg type
		$result = $db -> fetchAll($sql, 2);
		if ($result) {
			return $result[0][$id];
		} else {
			return;
		}
	}

}
