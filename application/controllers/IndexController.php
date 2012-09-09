<?php

class IndexController extends Zend_Controller_Action {

	public function init() {

		// Init code here...

	}

	public function indexAction() {
		$this -> view -> title = 'Home';
		$this -> _redirect('/insert');
		
	}
}
