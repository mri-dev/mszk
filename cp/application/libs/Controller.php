<?
use DatabaseManager\Database;
use PortalManager\Template;
use PortalManager\AdminUser;
use PortalManager\Users;
use PortalManager\Installer;
use AlertsManager\Alerts;

class Controller {
    public $db = null;
    public $hidePatern 	= true;
    private $theme_wire 	= '';
    private $theme_folder 	= '';
    private $start_time     = 0;
    private $finish_time    = 0;
    private $is_admin       = false;
    public $page_pagination = array( );

    public static $pageTitle;
    public $fnTemp = array();
    public static $user_opt = array();

    function __construct($arg = array())
    {
        $this->start_time = microtime(true);
        $this->is_admin = $arg[admin];
        Session::init();
        Helper::setMashineID();
        $this->gets = Helper::GET();

        //$this->memory_usage();
        // CORE
        $this->view = new View();
        $this->db = new Database();
        $this->installer = new Installer(array('db'=> $this->db));
        $this->ALERTS = new Alerts(array('controller'=> $this));

        //////////////////////////////////////////////////////
        // Beállítások változók
        $this->out( 'settings', $this->getAllValtozo() );
        $this->out( 'gets', $this->gets );
        $this->out( 'db', $this->db );

        $this->AdminUser = new AdminUser( array( 'db' => $this->db, 'view' => $this->view, 'settings' => $this->view->settings )  );
        $this->Users = new Users( array( 'db' => $this->db ) );

        /* Belépett felhasználó adatok*/
        // E-mail cím
        $this->out('_USER', $this->Users->user);
        // Felh. adatok
        $this->out('_USERDATA', $this->Users->get());
        // Admin jogosultságú felhasználó flag
        $this->out('is_admin_logged', ($this->view->_USERDATA['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN || $this->view->_USERDATA['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN) ? true : false);

        // Filemanager browser url
        if ($this->view->_USERDATA) {
          $owndir = 'src/uploads/byusers/'.$this->view->_USERDATA['data']['ID'];
          if ( !file_exists($owndir) ) {
            mkdir($owndir, 0755);
          }
          $_SESSION['RF']['subfolder'] = 'byusers/'.$this->view->_USERDATA['data']['ID'].'/';
      		define('FILE_BROWSER_IMAGE', JS.'tinymce/plugins/filemanager/dialog.php?lang=hu_HU');
        } else {
      	   define('FILE_BROWSER_IMAGE', JS.'tinymce/plugins/filemanager/dialog.php?type=0&editor=0&lang=hu_HU');
        }

        // Ha nem ajax requestről van szó
        if ($this->gets[0] != 'ajax')
        {
          // Only admin
          if ( !defined('PRODUCTIONSITE') )
          {
            $this->out( 'modules', $this->installer->listModules(array('only_active' => true)) );
          }

          $templates = new Template( VIEW . 'templates/' );
          $this->out( 'templates', $templates );


          // Globális GET Success üzenet
          if ( $_GET['msgkey'] ) {
            $this->out( $_GET['msgkey'], Helper::makeAlertMsg('pSuccess', $_GET[$_GET['msgkey']]) );
          }
        }

        // Admin cp
        if ( !defined('PRODUCTIONSITE') || PRODUCTIONSITE == false )
        {
          $this->out( 'badges', $this->getBadges());
        }

        $this->out( 'kozterulet_jellege', $this->kozterulet_jellege() );

        if(!$arg[hidePatern]){ $this->hidePatern = false; }
    }

    public function lang( $key, $sprinf_params = array() )
    {
      $key = \Lang::content($key);

      $params = $sprinf_params;

  		preg_match_all('/%(.*?)%/', $key, $match);

  		if(!empty($match[0])) {
  			foreach((array)$match[1] as $m) {
  				if(isset($params[$m]) && !empty($params[$m])) {
  					$key = str_replace('%'.$m.'%', $params[$m], $key);
  				}
  			}

  		}

      return $key;
    }

    public function getBadges()
    {
      $badges = array();
      $messages_all = 0;

      $uid = $this->view->_USERDATA['data']['ID'];

      /**
      * Ajánlat kérések
      **/

      // All

      // Admin - Requests


      if ($this->view->is_admin_logged)
      {
        $badges['offers']['admin']['progress'] = (int)$this->db->squery("SELECT r.ID FROM requests as r WHERE r.offerout = 0 and r.elutasitva = 0")->rowCount();
        $badges['offers']['admin']['progressed'] = (int)$this->db->squery("SELECT r.ID FROM requests as r WHERE r.offerout = 1 or r.elutasitva = 1")->rowCount();
        $badges['offers']['admin']['done'] = (int)$this->db->squery("SELECT r.ID FROM requests as r LEFT OUTER JOIN offers as o ON o.ID = r.admin_offer_id WHERE r.offerout = 1 and o.project_id IS NOT NULL")->rowCount();
        $badges['offers']['admin']['positiveprocess'] = (int)$this->db->squery("SELECT r.ID FROM requests as r LEFT OUTER JOIN offers as o ON o.ID = r.admin_offer_id WHERE r.offerout = 1 and o.accepted = 1 and o.project_id IS NULL")->rowCount();
        $messages_all = $this->db->squery("SELECT m.ID FROM messanger_messages as m WHERE m.user_to_id = 0 and m.user_from_id != 0 and m.admin_readed_at IS NULL")->rowCount();
        $badges['offers']['admin']['total'] = $badges['offers']['admin']['progress'] + (int)$badges['offers']['admin']['positiveprocess'];
      }
        else
      {
        $badges['offers']['outbox']= (int)$this->db->squery("SELECT r.ID FROM requests as r WHERE r.user_id = :uid", array('uid' => $uid))->rowCount();
        $badges['offers']['inbox']= (int)$this->db->squery("SELECT r.ID FROM requests_offerouts as r WHERE r.user_id = :uid", array('uid' => $uid))->rowCount();

        $messages_all = $this->db->squery("SELECT m.ID FROM messanger_messages as m WHERE m.user_from_relation = 'admin' and m.user_from_id != 0 and m.user_to_id = :myid and m.user_readed_at IS NULL", array('myid' => $uid))->rowCount();
      }


      /**
      * Projektek
      **/
      // Futó
      $q = "SELECT p.ID FROM projects as p WHERE p.closed = 0";
      $arg = array();
      if (!$this->view->is_admin_logged) {
        $q .= " and p.primary_user_id = :uid";
        $arg['uid'] = $uid;
      } else {
        $q .= " GROUP BY p.order_hashkey";
      }

      $badges['projects']['inprogress'] = $this->db->squery($q, $arg)->rowCount();

      // Lezárt
      $q = "SELECT p.ID FROM projects as p WHERE p.closed = 1";
      $arg = array();
      if (!$this->view->is_admin_logged) {
        $q .= " and p.primary_user_id = :uid";
        $arg['uid'] = $uid;
      } else {
        $q .= " GROUP BY p.order_hashkey";
      }
      $badges['projects']['closed'] = $this->db->squery($q, $arg)->rowCount();

      $badges['projects']['all'] = $badges['projects']['inprogress'] + $badges['projects']['closed'];

      /**
      * Dokumentumok
      **/
      $badges['docs']['dijbekero']['aktualis'] = 0;
      $badges['docs']['dijbekero']['lejart'] = 0;

      /**
      * Üzenetek
      **/
      $badges['messages']['all'] = $messages_all;

      return $badges;
    }

    function out( $viewKey, $output )
    {
      $this->view->$viewKey = $output;
    }

    function bodyHead($key = '')
    {
        $mode = false;
        $subfolder = '';

        $this->theme_wire = ($key != '') ? $key : '';

        if($this->getThemeFolder() != ''){
            $mode       = true;
            $subfolder  = $this->getThemeFolder().'/';
        }


        # Oldal címe
        if(self::$pageTitle != null){
          $this->view->title = self::$pageTitle . ' | ' . $this->view->settings['page_title'];
          $this->view->_PAGETITLE = self::$pageTitle;
        } else {
          $this->view->title = $this->view->settings['page_title'] . " &mdash; ".$this->view->settings['page_description'];
        }


        # Oldal léptetés
        $this->view->_PAGEPAGINATION = $this->getPagePagination();


        # Render HEADER
        if(!$this->hidePatern){
            $this->view->render(
              $subfolder.$this->theme_wire.'header'.( (isset($_GET['header'])) ? '-'.$_GET['header'] : '' ),
              $mode
            );
        }
        # Aloldal átadása a VIEW-nek
        $this->view->called = $this->fnTemp;
    }

    public function addPagePagination( $link = array() )
    {
      if (is_array($link[0])) {
        // Multiple
        foreach ((array)$link as $l) {
          $this->page_pagination[] = $l;
        }
      } else {
        // Simple
        $this->page_pagination[] = $link;
      }

    }

    public function getPagePagination( $html_format = true )
    {
      $html = '';

      if ($html_format) {
        $html .= '<div class="page-pagination">';
      }

      // Home
      $pg = $this->page_pagination;
      $this->page_pagination = array();
      $this->page_pagination[] = array(
        'title' => '<i class="fa fa-home"></i>',
        'link' => '/'
      );
      $this->page_pagination = array_merge($this->page_pagination, $pg);

      foreach ( $this->page_pagination as $p ) {
        if ($p['link']) {
          $html .= '<a href="'.$p['link'].'">'.$p['title'].'</a> / ';
        } else {
          $html .= '<span class="no-link">'.$p['title'].'</span> / ';
        }

      }

      if ($html_format) {
        $html = rtrim($html, ' / ');
        $html .= '</div>';
      }
      if ($html_format) {
        return $html;
      } else {
        return $this->page_pagination;
      }
    }

    function setTitle($title){
      $this->view->title = $title;
    }

    function valtozok($key)
    {
      $d = $this->db->query("SELECT bErtek FROM beallitasok WHERE bKulcs = '$key'");
      $dt = $d->fetch(PDO::FETCH_ASSOC);
      return $dt[bErtek];
    }

    function getAllValtozo()
    {
        $v = array();
        $d = $this->db->query("SELECT bErtek, bKulcs FROM beallitasok");
        $dt = $d->fetchAll(PDO::FETCH_ASSOC);

        foreach($dt as $d){
            $v[$d[bKulcs]] = $d[bErtek];
        }

        $protocol = ($_SERVER['HTTPS']) ? 'https://' : 'http://';

        $v['domain'] = $protocol.str_replace( array('https://www.','http://www.','http://','https://'), '', $v['page_url']);

        if (strpos($v['alert_email'],",") !== false)
        {
          $v['alert_email'] = explode(",",$v['alert_email']);
        }

        return $v;
    }

    function setValtozok($key,$val){
        $iq = "UPDATE beallitasok SET bErtek = '$val' WHERE bKulcs = '$key'";
        $this->db->query($iq);
    }

    protected function setThemeFolder($folder = ''){
        $this->theme_folder = $folder;
    }

    protected function getThemeFolder(){
        return $this->theme_folder;
    }

    public function kozterulet_jellege()
    {
       $arr = array(
            'akna',
            'akna-alsó',
            'akna-felső',
            'alagút',
            'alsórakpart',
            'arborétum',
            'autóút',
            'barakképület',
            'barlang',
            'bejáró',
            'bekötőút',
            'bánya',
            'bányatelep',
            'bástya',
            'bástyája',
            'csárda',
            'csónakházak',
            'domb',
            'dűlő',
            'dűlők',
            'dűlősor',
            'dűlőterület',
            'dűlőút',
            'egyetemváros',
            'egyéb',
            'elágazás',
            'emlékút',
            'erdészház',
            'erdészlak',
            'erdő',
            'erdősor',
            'fasor',
            'fasora',
            'felső',
            'forduló',
            'főmérnökség',
            'főtér',
            'főút',
            'föld',
            'gyár',
            'gyártelep',
            'gyárváros',
            'gyümölcsös',
            'gát',
            'gátsor',
            'gátőrház',
            'határsor',
            'határút',
            'hegy',
            'hegyhát',
            'hegyhát dűlő',
            'hegyhát',
            'köz',
            'hrsz',
            'hrsz.',
            'ház',
            'hídfő',
            'iskola',
            'játszótér',
            'kapu',
            'kastély',
            'kert',
            'kertsor',
            'kerület',
            'kilátó',
            'kioszk',
            'kocsiszín',
            'kolónia',
            'korzó',
            'kultúrpark',
            'kunyhó',
            'kör',
            'körtér',
            'körvasútsor',
            'körzet',
            'körönd',
            'körút',
            'köz',
            'kút',
            'kültelek',
            'lakóház',
            'lakókert',
            'lakónegyed',
            'lakópark',
            'lakótelep',
            'lejtő',
            'lejáró',
            'liget',
            'lépcső',
            'major',
            'malom',
            'menedékház',
            'munkásszálló',
            'mélyút',
            'műút',
            'oldal',
            'orom',
            'park',
            'parkja',
            'parkoló',
            'part',
            'pavilon',
            'piac',
            'pihenő',
            'pince',
            'pincesor',
            'postafiók',
            'puszta',
            'pálya',
            'pályaudvar',
            'rakpart',
            'repülőtér',
            'rész',
            'rét',
            'sarok',
            'sor',
            'sora',
            'sportpálya',
            'sporttelep',
            'stadion',
            'strandfürdő',
            'sugárút',
            'szer',
            'sziget',
            'szivattyútelep',
            'szállás',
            'szállások',
            'szél',
            'szőlő',
            'szőlőhegy',
            'szőlők',
            'sánc',
            'sávház',
            'sétány',
            'tag',
            'tanya',
            'tanyák',
            'telep',
            'temető',
            'tere',
            'tető',
            'turistaház',
            'téli kikötő',
            'tér',
            'tömb',
            'udvar',
            'utak',
            'utca',
            'utcája',
            'vadaskert',
            'vadászház',
            'vasúti megálló',
            'vasúti őrház',
            'vasútsor',
            'vasútállomás',
            'vezetőút',
            'villasor',
            'vágóhíd',
            'vár',
            'várköz',
            'város',
            'vízmű',
            'völgy',
            'zsilip',
            'zug',
            'állat és növ.kert',
            'állomás',
            'árnyék',
            'árok',
            'átjáró',
            'őrház',
            'őrházak',
            'őrházlak',
            'út',
            'útja',
            'útőrház',
            'üdülő',
            'üdülő-part',
            'üdülő-sor',
            'üdülő-telep',
            );

        asort($arr);
        uasort($arr, array('Controller', 'Hcmp'));

        return $arr;
    }

    /**
    * Magyar ékezetes betűk korrigálás/rewrite rendezéshez
    * */
    static function Hcmp($a, $b)
    {
      static $Hchr = array('á'=>'az', 'é'=>'ez', 'í'=>'iz', 'ó'=>'oz', 'ö'=>'ozz', 'ő'=>'ozz', 'ú'=>'uz', 'ü'=>'uzz', 'ű'=>'uzz', 'cs'=>'cz', 'zs'=>'zz',
       'ccs'=>'czcz', 'ggy'=>'gzgz', 'lly'=>'lzlz', 'nny'=>'nznz', 'ssz'=>'szsz', 'tty'=>'tztz', 'zzs'=>'zzzz', 'Á'=>'az', 'É'=>'ez', 'Í'=>'iz',
       'Ó'=>'oz', 'Ö'=>'ozz', 'Ő'=>'ozz', 'Ú'=>'uz', 'Ü'=>'uzz', 'Ű'=>'uzz', 'CS'=>'cz', 'ZZ'=>'zz', 'CCS'=>'czcz', 'GGY'=>'gzgz', 'LLY'=>'lzlz',
       'NNY'=>'nznz', 'SSZ'=>'szsz', 'TTY'=>'tztz', 'ZZS'=>'zzzz');
       $a = strtr($a,$Hchr);   $b = strtr($b,$Hchr);
       $a=strtolower($a); $b=strtolower($b);
       return strcmp($a, $b);
    }

    public function memory_usage()
    {
       echo '-Memory: ',round(memory_get_usage()/1048576,2),' MB used-';
    }
    public function get_speed()
    {
       echo "-Operation Speed:", (number_format($this->finish_time - $this->start_time, 4))," sec-";
    }

    function __destruct(){
        $mode       = false;
        $subfolder  = '';

        if($this->getThemeFolder() != ''){
            $mode       = true;
            $subfolder  = $this->getThemeFolder().'/';
        }

        if(!$this->hidePatern){
            # Render FOOTER
            $this->view->render($subfolder.$this->theme_wire.'footer',$mode);
        }
        $this->db = null;
       // $this->memory_usage();

        $this->finish_time = microtime(true);
        //$this->get_speed();
    }
}

?>
