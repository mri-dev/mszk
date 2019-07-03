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

      $file = rtrim(ADMROOT,'/') . $docs['docfile'];

			if ($docs) {
				$this->docs->logDocumentView($docs['hashkey']);
			}

			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$file);
			exit();

		}

		function __destruct(){
		}
	}

?>
