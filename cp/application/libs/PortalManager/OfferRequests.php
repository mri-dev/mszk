<?
namespace PortalManager;

use PortalManager\Users;
use PortalManager\Template;
use MailManager\Mailer;

/**
* class OfferRequests
* @package PortalManager
* @version 1.0
*/
class OfferRequests
{
	private $db = null;

	function __construct( $arg = array() )
	{
		$this->db = $arg[db];
		return $this;
	}

  public function sendRequest( $requester, $config )
  {
		$ret = array();
		$users = new Users(array('db' => $this->db));

    // Ellenőrzés
    if (empty($requester['name'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a saját nevét!'));
    }

    if (empty($requester['email'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a saját e-mail címét!'));
    }

    if (empty($requester['phone'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a telefonszámát!'));
    }

    if (empty($requester['aszf'])) {
      throw new \Exception(__('Az ajánlatkérés elküldéséhez kötelezően el kell fogadni az Általános Szerződési Feltételeket.'));
    }

    if (empty($requester['adatvedelem'])) {
      throw new \Exception(__('Az ajánlatkérés elküldéséhez kötelezően hozzá kell járulni az adatai kezeléséhez, melyről az Adatvédelmi Tájékoztatótban tájékozódhat.'));
    }

		// Adat előkészítés
		// subservice leírások
		$service_desc = array();
		foreach ((array)$config['service_desc'] as $id => $desc)
		{
			if (empty($desc)) continue;
			$service_desc[$id] = $desc;
		}

		$selected_cashall = array();
		$total_cash = 0;
		foreach ((array)$config['selected_cashall'] as $id => $v)
		{
			if (empty($v)) continue;
			$total_cash += (float)$v;
			$selected_cashall[$id] = $v;
		}

		$selected_cashrow = array();
		foreach ((array)$config['selected_cashrow'] as $id => $v)
		{
			if (empty($v)) continue;
			$selected_cashrow[$id] = $v;
		}

    // Gyors felh. fiók regisztráció

		$user_id = 0;
		// check email usage
		$exists_user = $users->userExists( 'email', trim($requester['email']) );
		if ($exists_user)
		{
			$user_data = $users->getData(  trim($requester['email']), 'email' );
			$user_id = (int)$user_data['ID'];
		}

		// Ha nem létező felhasználó, akkor létrehozás
		if ( !$exists_user || $user_id == 0 )
		{
			$rand_password = substr(uniqid(), -10);
			$user_id = $users->createByAdmin(array(
				'data' => array(
					'felhasznalok' => array(
						'nev' => addslashes(trim($requester['name'])),
						'email' => addslashes(trim($requester['email'])),
						'jelszo' => $rand_password
					)
				)
			));
			if ($user_id) {
				$ret['created_user_id'] = $user_id;
			}
		}

		// Igény regisztrálása
		$hashkey = md5(uniqid());
		$this->db->insert(
				"requests",
				array(
					'hashkey' => $hashkey,
					'user_id' => $user_id,
					'email' => addslashes(trim($requester['email'])),
					'name' => addslashes(trim($requester['name'])),
					'company' => addslashes(trim($requester['company'])),
					'phone' => addslashes(trim($requester['phone'])),
					'message' => addslashes(trim($requester['message'])),
					'services' => json_encode($config['selected_services'], \JSON_UNESCAPED_UNICODE),
					'subservices' => json_encode($config['selected_subservices'], \JSON_UNESCAPED_UNICODE),
					'subservices_items' => json_encode($config['selected_subservices_items'], \JSON_UNESCAPED_UNICODE),
					'cash_config' => json_encode($selected_cashrow, \JSON_UNESCAPED_UNICODE),
					'cash' => json_encode($selected_cashall, \JSON_UNESCAPED_UNICODE),
					'cash_total' => $total_cash,
					'service_description' => json_encode($service_desc, \JSON_UNESCAPED_UNICODE)
				)
		);
		$request_id = $this->db->lastInsertId();

		$ret['request_hashkey'] = $hashkey;
		$ret['request_id'] = (int)$request_id;
		$ret['email'] = trim($requester['email']);

		// Ha van user_id vagy létre lett hozva
		if ( $user_id != 0 )
		{
			$ret['user_id'] = $user_id;

			// E-mail - Igénylő értesítő
			if (true)
			{
				$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
				$mail->add( trim($requester['email']) );
				$arg = array(
					'nev' => trim($requester['name']),
					'jelszo' => $rand_password,
					'settings' => $this->settings,
					'data' => array(
						'requester' => $requester,
						'config' => $config
					),
					'request' => array(
						'hashkey' => $hashkey,
						'id' => (int)$request_id,
					),
					'new_user_id' => $ret['created_user_id'],
					'user_id' => $user_id,
					'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
				);
				$mail->setSubject( __('Ajánlatkérését fogadtuk.') );
				$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'request_new_user', $arg ) );
				$re = $mail->sendMail();
			}

	    // E-mail - Admin értesítő
			if (false)
			{
				$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
				$mail->add( $this->settings['alert_email'] );
				$arg = array(
					'nev' => trim($requester['name']),
					'jelszo' => $rand_password,
					'settings' => $this->settings,
					'data' => array(
						'requester' => $requester,
						'config' => $config
					),
					'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
				);
				$mail->setSubject( __('Új ajánlatkérés érkezett.') );
				$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'request_new_admin', $arg ) );
				$re = $mail->sendMail();
			}
		}

		return $ret;
  }

	public function __destruct()
	{
		$this->db = null;
	}

}
?>
