<?php
namespace MessageManager;

use PortalManager\Users;
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

  public function collectAllUnreadedMessagesForEmailAlert( )
  {
    $delay_in_min = 60;
    $datas = array(
      'total_items' => 0,
      'user_ids' => array(),
      'data' => false
    );

    $gets = $this->db->query($qry = "
    SELECT
      m.ID,
      m.sessionid,
      m.send_at,
      m.user_to_id as user_id,
      m.message,
      IF(m.user_to_id = mg.requester_id, p.requester_title, p.servicer_title) as project_title,
      IF(m.user_to_id = mg.requester_id, 'requester', 'servicer') as my_relation,
      TIMESTAMPDIFF(MINUTE, m.send_at, now()) as minafter
    FROM ".self::DBTABLE_MESSAGES." as m
    LEFT OUTER JOIN ".self::DBTABLE." as mg ON mg.sessionid = m.sessionid
    LEFT OUTER JOIN ".\PortalManager\Projects::DBPROJECTS." as p ON p.hashkey = m.sessionid
    WHERE 1=1 and
    m.user_from_id != 0 and
    m.user_to_id != 0 and
    ((m.requester_readed_at IS NULL and m.requester_alerted = 0 and m.user_to_id = mg.requester_id) or (m.servicer_readed_at IS NULL and m.servicer_alerted = 0 and m.user_to_id = mg.servicer_id))
    and TIMESTAMPDIFF(MINUTE, m.send_at, now()) > {$delay_in_min} ORDER BY m.send_at ASC");

    //echo $qry; exit;

    if ($gets->rowCount() != 0) {
      $gets = $gets->fetchAll(\PDO::FETCH_ASSOC);

      foreach ((array)$gets as $d) {
        if (!isset($datas['data'][$d['user_id']]['userid'])) {
          $datas['data'][$d['user_id']]['user_id'] = $d['user_id'];

          $user = $this->db->squery("SELECT nev, email FROM felhasznalok WHERE ID = :id", array('id' => $d['user_id']))->fetch(\PDO::FETCH_ASSOC);
          $datas['data'][$d['user_id']]['user'] = $user;
          $datas['data'][$d['user_id']]['user']['relation'] = $d['my_relation'];
        }

        if (!in_array($d['user_id'], $datas['user_ids'])) {
          $datas['user_ids'][] = $d['user_id'];
        }

        $datas['data'][$d['user_id']][items][$d['sessionid']]['project']['title'] = $d['project_title'];
        $datas['data'][$d['user_id']][items][$d['sessionid']]['project']['session'] = $d['sessionid'];

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
    $datas['unreaded'] = 0;
    $this->admin = (isset($arg['admin'])) ? true : false;

    // Load sessions
    $sess = $this->db->squery("SELECT
      m.sessionid,
      m.requester_id,
      m.servicer_id,
      m.project_id,
      m.closed,
      m.closed_at,
      p.requester_title,
      p.servicer_title,
      IF(:uid = m.requester_id,'requester','servicer') as my_relation,
      IF(:uid = m.requester_id, p.requester_title, p.servicer_title) as project_title,
      IF(:uid = m.requester_id, m.notice_by_requester, m.notice_by_servicer) as notice,
      IF(:uid = m.requester_id, m.archived_by_requester, m.archived_by_servicer) as archived,
      (SELECT COUNT(ms.ID) FROM ".self::DBTABLE_MESSAGES." as ms WHERE ms.sessionid = m.sessionid) as message_total,
      f.nev as partner_nev,
      IF(
        :uid = m.requester_id,
        (SELECT COUNT(msunre.ID) FROM ".self::DBTABLE_MESSAGES." as msunre WHERE msunre.sessionid = m.sessionid and (msunre.user_from_id != 0 and msunre.user_to_id) and msunre.user_from_id != :uid and msunre.requester_readed_at IS NULL),
        (SELECT COUNT(msunse.ID) FROM ".self::DBTABLE_MESSAGES." as msunse WHERE msunse.sessionid = m.sessionid and (msunse.user_from_id != 0 and msunse.user_to_id) and msunse.user_from_id != :uid and msunse.servicer_readed_at IS NULL)) as message_unreaded,
      m.created_at
    FROM ".self::DBTABLE." as m
    LEFT OUTER JOIN ".\PortalManager\Projects::DBPROJECTS." as p ON p.ID = m.project_id
    LEFT OUTER JOIN felhasznalok as f ON f.id = IF(:uid = m.requester_id, m.servicer_id, m.requester_id)
    WHERE 1=1 and (m.requester_id = :uid or m.servicer_id = :uid)
    ORDER BY m.closed ASC, archived ASC", array('uid' => $uid));

    if ( $sess->rowCount() == 0 ) {
      return $datas;
    }

    $sessdata = $sess->fetchAll(\PDO::FETCH_ASSOC);

    foreach ( (array)$sessdata as $s )
    {
      if ( $s['message_unreaded'] != 0 ) {
        $datas['unreaded'] += (int)$s['message_unreaded'] ;
      }
      $s['archived'] = (int)$s['archived'];
      $s['notice'] = nl2br($s['notice']);
      $datas['sessions'][$s['sessionid']] = $s;
    }

    // Message
    if (isset($arg['load_session']))
    {
      // Log visit
      $my_relation = $datas['sessions'][$arg['load_session']]['my_relation'];

      if (!empty($my_relation)) {
        $this->db->update(
          self::DBTABLE_MESSAGES,
          array(
            $my_relation.'_readed_at' => NOW
          ),
          sprintf("sessionid = '%s' and ".$my_relation."_readed_at IS NULL and user_from_id != %d and (user_from_id = %d or user_to_id = %d)", $arg['load_session'], (int)$uid, (int)$uid, (int)$uid)
        );
      }

      $qry = "SELECT
        m.*,
        IF('".(int)$uid."' = ms.requester_id,'requester','servicer') as my_relation,
        IF(m.user_from_id = ms.requester_id, 'requester', 'servicer') as user_from_relation,
        IF('".(int)$uid."' != ms.requester_id, m.requester_readed_at, m.servicer_readed_at) as user_readed_at,
        IF(m.user_from_id = 0 and m.user_to_id = 0, 1, 0) as system_msg,
        f.nev as from_name
      FROM ".self::DBTABLE_MESSAGES." as m
      LEFT OUTER JOIN ".self::DBTABLE." as ms ON ms.sessionid = m.sessionid
      LEFT OUTER JOIN felhasznalok as f ON f.ID = m.user_from_id
      WHERE 1=1";

      $qry .= " and m.sessionid = '".$arg['load_session']."'";

      if ($this->admin) {
        $qry .= " ORDER BY m.send_at DESC";
      } else {
        $qry .= " ORDER BY m.send_at DESC";
      }

      $arg = array();
      $arg['multi'] = true;
      extract($this->db->q($qry, $arg));

      $datas['messages'] = array();

      foreach ((array)$data as $d)
      {
        if (!isset($datas['messages'][$d['sessionid']]['ID']))
        {
          $datas['messages']['session'] = $d['sessionid'];
        }

        $is_today = (date('Ymd') == date('Ymd', strtotime($d['send_at']))) ? true : false;

        $datas['messages']['msg'][] = array(
          'ID' => (int)$d['ID'],
          'msg' => nl2br($d['message']),
          'user_from_relation' => $d['user_from_relation'],
          'system_msg' => ($d['system_msg'] == 1) ? true : false,
          'my_relation' => $d['my_relation'],
          'user_readed_at' => $d['user_readed_at'],
          'send_at' => ($is_today) ? date('H:i', strtotime($d['send_at'])) : date('Y. m. d. H:i', strtotime($d['send_at'])),
          'from_id' => $d['user_from_id'],
          'to_id' => $d['user_to_id'],
          'unreaded' => $unreaded,
          'from_me' => ($d['user_from_id'] == $uid) ? true : false,
          'from' => array(
            'name' => $d['from_name'],
            'ID' => $d['user_from_id']
          )
        );
      }
    } else {
      $datas['messages'] = array();
    }

    return $datas;
  }

  public function editMessangerComment( $session, $relation, $new_notice )
  {
    if (!in_array($relation, array('servicer','requester'))) {
      return false;
    }

    $this->db->update(
      self::DBTABLE,
      array(
        'notice_by_'.$relation => ($new_notice == '') ? NULL : $new_notice
      ),
      sprintf("sessionid = '%s'", $session)
    );
  }

  public function archiveSession( $session, $relation, $archive )
  {
    if (!in_array($relation, array('servicer','requester'))) {
      return false;
    }

    $this->db->update(
      self::DBTABLE,
      array(
        'archived_by_'.$relation => $archive
      ),
      sprintf("sessionid = '%s'", $session)
    );
  }

  public function addMessage( $uid, $msg, $sessionid)
  {
    if ($this->isMessageSessionClosed($session)) {
      throw new \Exception(__('Ez a beszélgetés időközben lezárásra került.'));
    }

    $sessiondata = $this->db->squery("SELECT m.* FROM ".self::DBTABLE." as m WHERE m.sessionid = :sid", array('sid' => $sessionid));

    if ($sessiondata->rowCount() == 0) {
      throw new \Exception(sprintf(__('Hibás üzenet session (%s)!'), $sessionid));
    }

    $sessiondata = $sessiondata->fetch(\PDO::FETCH_ASSOC);
    $to = ($sessiondata['partner_id'] == $uid) ? 0 : (int)$sessiondata['partner_id'];

    $this->db->insert(
      self::DBTABLE_MESSAGES,
      array(
        'sessionid' => $sessionid,
        'message' => $msg,
        'user_from_id' => $uid,
        'user_to_id' => $to
      )
    );

    return $this->db->lastInsertId();
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
