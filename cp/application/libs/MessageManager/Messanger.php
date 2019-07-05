<?php
namespace MessageManager;

use PortalManager\User;
use MailManager\Mailer;
use MailManager\Mails;

class Messanger
{
  const DBTABLE = 'messanger';
  const DBTABLE_MESSAGES = 'messanger_messages';

  public $db = null;
  private $controller = null;
  private $smarty = null;
  private $settings = array();
  private $admin = false;

  function __construct( $arg = array() )
	{
		if ( isset($arg['controller']) ) {
			$this->controller = $arg['controller'];
			$this->db = $arg['controller']->db;
			$this->settings = $arg['controller']->settings;
			$this->smarty = $arg['controller']->smarty;
		}

		return $this;
  }

  public function collectAllUnreadedMessagesForEmailAlert( $useradmin = 'user', $delay_in_min = 60 )
  {
    $datas = array(
      'total_items' => 0,
      'user_ids' => array(),
      'data' => false
    );

    $gets = $this->db->query("
    SELECT
      m.ID,
      m.sessionid,
      m.send_at,
      m.user_to_id as user_id,
      m.from_admin,
      m.message,
      mg.subject,
      mg.allas_id,
      TIMESTAMPDIFF(MINUTE, m.send_at, now()) as minafter
    FROM ".self::DBTABLE_MESSAGES." as m
    LEFT OUTER JOIN ".self::DBTABLE." as mg ON mg.sessionid = m.sessionid
    WHERE 1=1 and
    {$useradmin}_alerted = 0 and {$useradmin}_readed_at IS NULL and TIMESTAMPDIFF(MINUTE, m.send_at, now()) > {$delay_in_min}");

    if ($gets->rowCount() != 0) {
      $gets = $gets->fetchAll(\PDO::FETCH_ASSOC);

      foreach ((array)$gets as $d) {
        if (!isset($datas['data'][$d['user_id']]['userid'])) {
          $datas['data'][$d['user_id']]['user_id'] = $d['user_id'];
          $user = new User($d['user_id'], array('controller' => $this->controller));
          $datas['data'][$d['user_id']]['user'] = array(
            'name' => $user->getName(),
            'email' => $user->getEmail()
          );
        }

        if (!isset($datas['data'][$d['user_id']][items][$d['sessionid']])) {
          $datas['data'][$d['user_id']][items][$d['sessionid']]['allas_id'] = $d['allas_id'];
          $datas['data'][$d['user_id']][items][$d['sessionid']]['subject'] = $d['subject'];
        }

        if (!in_array($d['user_id'], $datas['user_ids'])) {
          $datas['user_ids'][] = $d['user_id'];
        }

        $datas['data'][$d['user_id']][items][$d['sessionid']]['items'][] = $d;
        $datas['data'][$d['user_id']]['total_unreaded']++;

        $datas['total_items']++;
      }
    }
    return $datas;
  }

  public function readInfos( $uid = false )
  {
    $datas = array();
    $outbox_unreaded = 0;
    $inbox_unreaded = 0;

    if (!$uid) {
      return false;
    }

    $qry = "SELECT
      ms.start_by,
      ms.start_by_id,
      m.user_readed_at,
      m.admin_readed_at,
      m.user_from_id,
      m.user_to_id
    FROM ".self::DBTABLE_MESSAGES." as m
    LEFT OUTER JOIN ".self::DBTABLE." as ms ON ms.sessionid = m.sessionid
    WHERE 1=1";

    $qry .= " and (m.user_from_id = {$uid} or m.user_to_id = {$uid}) ";

    $arg = array();
    $arg['multi'] = true;
    extract($this->db->q($qry, $arg));

    foreach ((array)$data as $d)
    {
      if($uid == $d['user_to_id'] && $d['start_by_id'] == $uid && is_null($d['user_readed_at'])){
          $outbox_unreaded++;
      }
      if($uid == $d['user_to_id'] && $d['start_by_id'] != $uid && is_null($d['user_readed_at'])){
          $inbox_unreaded++;
      }
    }

    $datas['inbox_unreaded'] = $inbox_unreaded;
    $datas['outbox_unreaded'] = $outbox_unreaded;
    $datas['total_unreaded'] = $inbox_unreaded + $outbox_unreaded;

    return $datas;
  }

  public function loadMessages( $uid, $arg = array() )
  {
    $datas = array();
    $this->admin = (isset($arg['admin'])) ? true : false;

    $qry = "SELECT
      m.*
    FROM ".self::DBTABLE_MESSAGES." as m
    LEFT OUTER JOIN ".self::DBTABLE." as ms ON ms.sessionid = m.sessionid
    WHERE 1=1";

    if ($this->admin) {
      $qry .= " ORDER BY m.send_at DESC";
    } else {
      $qry .= " ORDER BY m.send_at DESC";
    }

    $arg = array();
    $arg['multi'] = true;
    extract($this->db->q($qry, $arg));

    $datas['list'] = array();

    foreach ((array)$data as $d)
    {
      if (!isset($datas['list'][$d['sessionid']]['ID']))
      {
        $datas['list'][$d['sessionid']]['session'] = $d['sessionid'];
      }

      $is_today = (date('Ymd') == date('Ymd', strtotime($d['send_at']))) ? true : false;

      $datas['list'][$d['sessionid']]['msg'][] = array(
        'ID' => (int)$d['ID'],
        'msg' => $d['message'],
        'admin_readed_at' => $d['admin_readed_at'],
        'user_readed_at' => $d['user_readed_at'],
        'send_at' => ($is_today) ? date('H:i', strtotime($d['send_at'])) : date('Y. m. d. H:i', strtotime($d['send_at'])),
        'from_admin' => ($d['from_admin'] == 1) ? true : false,
        'from_id' => $d['user_from_id'],
        'to_id' => $d['user_to_id'],
        'unreaded' => $unreaded,
        'from' => array(
          'name' => $d['from_name'],
          'ID' => $d['user_from_id']
        )
      );
    }

    return $datas;
  }

  public function addMessage($session, $from, $to, $msg, $admin)
  {
    if ($this->isMessageSessionClosed($session)) {
      throw new \Exception($this->controller->lang('Ez a beszélgetés időközben lezárásra került.'));
    }

    $this->db->insert(
      self::DBTABLE_MESSAGES,
      array(
        'sessionid' => $session,
        'message' => $msg,
        'from_admin' => ($admin) ? 1 : 0,
        'user_from_id' => $from,
        'user_to_id' => $to,
        'user_readed_at' => ($admin) ? NULL : NOW,
        'admin_readed_at' => ($admin) ? NOW : NULL,
        'user_alerted' => ($admin) ? 0 : 1,
        'admin_alerted' => ($admin) ? 1 : 0,
      )
    );

    return $this->db->lastInsertId();
  }

  public function createSession( $data, $by = 'admin' )
  {
    extract($data);
    $createdSession = false;

    $lang = $this->controller->LANGUAGES->getCurrentLang();

    if (empty($msg)) {
      throw new \Exception($this->controller->lang("Első üzenet tartalmát kötelező megadni."));
    }

    if (empty($subject)) {
      throw new \Exception($this->controller->lang("A beszélgetés létrehozásához adja meg a témát."));
    }
    $createdSession = uniqid();

    $this->db->insert(
      self::DBTABLE,
      array(
        'sessionid' => $createdSession,
        'subject' => $subject,
        'allas_requester_user_id' => (isset($user_id) && !empty($user_id)) ? (int)$user_id : NULL,
        'start_by' => $by,
        'start_by_id' => ($by == 'admin') ? $admin_id : $user_id,
        'to_id' => ($by == 'admin') ? $user_id : NULL
      )
    );

    // Üzenet beszúrása
    $this->addMessage(
      $createdSession,
      $admin_id,
      $user_id,
      $msg,
      ($by == 'admin') ? true : false
    );

    // E-mail alert
    if (isset($user_id) && !empty($user_id))
    {
      $requestedUser = new User($user_id, array('controller' => $this->controller));

      $mail = new Mailer(
        $this->settings['page_title'],
        $this->settings['email_noreply_address'],
        $this->settings['mail_sender_mode']
      );
  		$mail->add( $requestedUser->getEmail() );

      $this->smarty->assign( 'subject', $subject );
      $this->smarty->assign( 'msg', $msg );
      $this->smarty->assign( 'settings', $this->controller->settings );
      $this->smarty->assign( 'user', $requestedUser );
      $this->smarty->assign( 'msgurl', '/ugyfelkapu/uzenetek/msg/'.$createdSession.'/?rel=email-alert' );

      $mail->setSubject( $this->controller->lang('MAIL_CP_MESSANGER_UJ_UZENET_BESZELGETES', array('tema' => $subject)));

  		$mail->setMsg( $this->smarty->fetch( 'mails/'.$lang.'/messanger_new_sesssion_user.tpl' ) );
  		$re = $mail->sendMail();
    }

    return $createdSession;
  }

  public function setReadedMessage($by, $session)
  {
    $aby = ($by == 'user_readed_at') ? 'user_alerted' : 'admin_alerted';
    $this->db->update(
      self::DBTABLE_MESSAGES,
      array(
        $by => NOW,
        $aby => 1
      ),
      sprintf($by." IS NULL and sessionid = '%s'", $session)
    );
  }

  public function isMessageSessionClosed($session)
  {
    return ($this->db->query("SELECT closed FROM ".self::DBTABLE." WHERE sessionid = '{$session}'")->fetchColumn() == 1) ? true : false;
  }

  public function editMessageData($session, $key, $value)
  {
    $this->db->update(
      self::DBTABLE,
      array(
        $key => $value
      ),
      sprintf("sessionid = '%s'", $session)
    );
  }

  public function __destruct()
	{
		$this->db = null;
		$this->smarty = false;
		$this->settings = null;
		$this->controller = null;
	}
}

?>
