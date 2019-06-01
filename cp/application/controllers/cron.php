<?php
use PortalManager\OfferRequests;

class cron extends Controller
{
		function __construct(){
			parent::__construct();

      if ( !isset($_GET['key']) && $_GET['key'] !== 't38fsdfu82f92r32ur9w(EU3r2u9Wd3f)3f')
      {
        header("HTTP/1.0 404 Not Found");
        die();
      }
		}

    public function emailSendOfferouts()
    {
      $requests = new OfferRequests( array('db' => $this->db) );

      $request_ids = array();

      $email_stack = $requests->pickOfferoutEmailStack( 1 );

      foreach ( (array)$email_stack as $es )
      {
        if ($es['request_id'] != '' && !in_array($es['request_id'], $request_ids)) {
          $request_ids[] = (int)$es['request_id'];
        }
      }

      $request_list = $requests->getList(array('ids' => $request_ids));
      echo '<pre>';
      print_r($request_list);
    }

		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				//$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>
