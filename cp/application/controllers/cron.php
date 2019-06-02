<?php
use PortalManager\OfferRequests;
use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Template;

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

      $request_list = $requests->getList(array('ids' => $request_ids, 'bindIDToList' => 1));

      foreach ( (array)$email_stack as $es )
      {
				echo '<pre>';
	      print_r($es);

        if (true)
        {
          // Aktiváló e-mail kiküldése
      		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
      		//$mail->add();

      		$arg = array(
						'cimzett_neve' => 'Molnár István',
      			'settings' => $this->db->settings
      		);
      		$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('services_users_offerouts', $arg);

					$mailmsg = (new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg );
      		$mail->setSubject( sprintf(__('Új ajánlatkérései vannak: %d darab szolgáltatás.'), 1) );
      		$mail->setMsg( $mailmsg );
					echo $mailmsg;
      		//$re = $mail->sendMail();
        }
      }
    }

		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				//$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>
