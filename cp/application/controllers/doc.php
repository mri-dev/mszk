<?php
use PortalManager\Documents;

class doc extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Dokumentum');

      $hashkey = $this->gets[1];
      $uid = $this->view->_USERDATA['data']['ID'];

      $this->docs = new Documents(array('db' => $this->db));

			$arg = array();
			$arg['uid'] = $uid;
			$arg['get'] = $hashkey;
			$docs = $this->docs->getList( $arg );

      print_r($docs);

      $file = rtrim(ADMROOT,'/') . $docs['docfile'];

      echo $file;



		}

		function __destruct(){
		}
	}

?>
