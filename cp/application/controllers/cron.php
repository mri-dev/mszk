<?php
use PortalManager\OfferRequests;
use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Template;

class cron extends Controller
{
		function __construct(){
			parent::__construct();

      if ( !isset($_GET['key']) || (isset($_GET['key']) && $_GET['key'] != 't38fsdfu82f92r32ur9w(EU3r2u9Wd3f)3f'))
      {
        header("HTTP/1.0 404 Not Found");
        die();
      }
		}

    public function emailSendOfferouts()
    {
      $requests = new OfferRequests( array('db' => $this->db) );

      $request_ids = array();

      $email_stack = $requests->pickOfferoutEmailStack( 20 );

			if (!$email_stack) {
				return false;
			}

      foreach ( (array)$email_stack as $es )
      {
        if ($es['request_id'] != '' && !in_array($es['request_id'], $request_ids)) {
          $request_ids[] = (int)$es['request_id'];
        }
      }

      $request_list = $requests->getList(array('ids' => $request_ids, 'bindIDToList' => 1));

      foreach ( (array)$email_stack as $es )
      {
				//echo '<pre>';
	     	//print_r($es);

        if (true)
        {
          // Aktiváló e-mail kiküldése
      		$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
      		$mail->add($es['to_email']);

      		$arg = array(
						'offer_nums' => count($es['parameters']['offers']),
						'if_company_name'  => ($es['company_nev'] == '')?'':' a következő céghez: <span class="company_name">'.$es['company_nev'].'</span>',
						'cimzett_neve' => $es['cimzett_neve'],
						'to_email' => $es['to_email'],
      			'settings' => $this->db->settings
      		);

					// Request overview
					$es['request'] = $request_list[$es['request_id']];
					$request_arg = array(
						'request' => $es
					);
					$arg['request'] = (new Template( VIEW . 'templates/mail/' ))->get( 'offerouts_request_overview', $request_arg );

					$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('services_users_offerouts', $arg);

					$mailmsg = (new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg );
      		$mail->setSubject( sprintf(__('Új ajánlatkérései vannak: %d darab szolgáltatás.'), count($es['parameters']['offers'])) );
      		$mail->setMsg( $mailmsg );
					//echo $mailmsg;
      		$re = $mail->sendMail();

					if ($re) {
						if ( !empty($re['success'][0]) ) {
							// Sikeres
							$this->db->update(
								"requests_outgo_emails",
								array(
									"sended" => 1,
									"sended_at" => NOW
								),
								sprintf("ID = %d", (int)$es['ID'])
							);
						} else {
							// Sikertelen
							$this->db->update(
								"requests_outgo_emails",
								array(
									"cannot_send" => 1,
									"sended_at" => NOW,
									"send_error_msg" => $re['failed'][0]['msg']
								),
								sprintf("ID = %d", (int)$es['ID'])
							);
						}
					}
        }
				usleep(500);
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
