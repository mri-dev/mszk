<?php
use PortalManager\OfferRequests;
use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Template;
use MessageManager\Messanger;

class cron extends Controller
{

	const MSG_SEND_LIMIT = 50;
	const MSG_SEND_WAITING_MS = 100;

	function __construct(){
		parent::__construct();

    if ( !isset($_GET['key']) || (isset($_GET['key']) && $_GET['key'] != 't38fsdfu82f92r32ur9w(EU3r2u9Wd3f)3f'))
    {
      header("HTTP/1.0 404 Not Found");
      die();
    }
	}

		/**
		* CRONTAB esemény
		* Minden 5 percben futó esemény
		* http://www.mszk.web-pro.hu/cron/unreadedMessagesAlert
		* wget -q -O /dev/null http://www.mszk.web-pro.hu/cron/unreadedMessagesAlert
		**/
		public function unreadedMessagesAlert()
		{
			$messangers = new Messanger(array(
				'controller' => $this
			));

			$unreadeds = $messangers->collectAllUnreadedMessagesForEmailAlert();

			/* */
			if ($unreadeds && count($unreadeds['user_ids']) > 0)
			{
				$send_loop = 0;
				foreach ((array)$unreadeds['data'] as $user_id => $user) {

					if($send_loop > self::MSG_SEND_LIMIT) break;

					$email = $user['user']['email'];
					$relation = $user['user']['relation'];

					if (!empty($email))
					{
						$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
						$mail->add( trim($email) );
						$arg = array(
							'nev' => $user['user']['nev'],
							'user_email' => $user['user']['email'],
							'olvasatlan_uzenet_db' => $user['total_unreaded'],
							'settings' => $this->db->settings,
							'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
						);

						// Üzenetek
						$uzenetek = '';
						$uzenetek .= '<h4>'.count($user['items']).' '.__('projekt üzenetváltásánál található olvasatlan üzenet').':</h4>';
						foreach ((array)$user['items'] as $projekt) {
							if ($projekt['project']['title'] != '') {
								$uzenetek .= '<div><a href="'.$this->db->settings['page_url'].'/belepes?return=/uzenetek/session/'.$projekt['project']['session'].'"><strong style="color:#02a0e3;">'.$projekt['project']['title'].'</strong></a> &mdash; <strong style="color:#f75656;">'.count($projekt['items']).' '.__('olvasatlan üzenet').'</strong></div>';
							} else {
								$uzenetek .= '<div><a href="'.$this->db->settings['page_url'].'/belepes?return=/uzenetek/session/'.$projekt['project']['session'].'"><strong style="color:#02a0e3;"><u style="color:#d99d9d;">'.__('Névtelen projekt').'</u> ('.$projekt['project']['session'].')</strong></a> &mdash; <strong style="color:#f75656;">>'.count($projekt['items']).' '.__('olvasatlan üzenet').'</strong></div>';
							}
						}

						$arg['uzenetek'] = $uzenetek;

						$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('messanger_alert_unreadedmsg', $arg);
						$mail->setSubject(sprintf(__('%d db olvasatlan üzenete van!'), $user['total_unreaded']));
						$mailbody = (new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg ) ;
						$mail->setMsg( $mailbody );
						$re = $mail->sendMail();

						if ($re) {
							foreach ((array)$user['items'] as $s) {
								foreach ((array)$s['items'] as $m) {
									if(!empty($re['success'])) {
										$this->db->query("UPDATE ".\MessageManager\Messanger::DBTABLE_MESSAGES." SET ".$relation."_alerted = 1 WHERE ID = ".$m['ID']);
									} else {
										$this->db->query("UPDATE ".\MessageManager\Messanger::DBTABLE_MESSAGES." SET ".$relation."_alerted_fail = 1 WHERE ID = ".$m['ID']);
									}
								}
							}
						}

					}
					$send_loop++;
				 	usleep(self::MSG_SEND_WAITING_MS);
				}
			}
			/* */
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
