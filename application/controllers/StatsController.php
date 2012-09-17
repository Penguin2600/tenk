<?php

class StatsController extends Zend_Controller_Action {

	public function init() {

		// Init code here...

	}

	public function indexAction() {

		$this -> view -> title = 'Stats Form';

		//generate stats
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = "Select COUNT(pid) from participant";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalParticipants = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where sex like '%M%'";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalMale = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where sex like '%F%'";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalFemale = $result[0]['COUNT(pid)'];

		$id = $this -> findLike('eid', "event", "10K");
		$sql = "Select COUNT(pid) from participant Where event like " . $id;
		$result = $db -> fetchAll($sql);
		$this -> view -> totalTenk = $result[0]['COUNT(pid)'];

		$id = $this -> findLike('eid', "event", "5K");
		$sql = "Select COUNT(pid) from participant Where event like " . $id;
		$result = $db -> fetchAll($sql);
		$this -> view -> totalFivek = $result[0]['COUNT(pid)'];

		$id = $this -> findLike('eid', "event", "HALF");
		$sql = "Select COUNT(pid) from participant Where event like " . $id;
		$result = $db -> fetchAll($sql);
		$this -> view -> totalHalf = $result[0]['COUNT(pid)'];

		$id = $this -> findLike('eid', "event", "DOUBLE");
		$sql = "Select COUNT(pid) from participant Where event like " . $id;
		$result = $db -> fetchAll($sql);
		$this -> view -> totalDouble = $result[0]['COUNT(pid)'];

		$id = $this -> findLike('eid', "event", "WHEELCHAIR");
		$sql = "Select COUNT(pid) from participant Where event like " . $id;
		$result = $db -> fetchAll($sql);
		$this -> view -> totalWheel = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where age <= 12";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalAge1 = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where age > 12 and age <=17";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalAge2 = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where age > 17 and age <=24";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalAge3 = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where age > 24 and age <=34";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalAge4 = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where age > 34 and age <=44";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalAge5 = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where age > 44 and age <=59";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalAge6 = $result[0]['COUNT(pid)'];

		$sql = "Select COUNT(pid) from participant Where age > 60";
		$result = $db -> fetchAll($sql);
		$this -> view -> totalAge7 = $result[0]['COUNT(pid)'];

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
