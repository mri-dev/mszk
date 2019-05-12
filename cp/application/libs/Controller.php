<?
use DatabaseManager\Database;
use PortalManager\Template;
use PortalManager\AdminUser;
use PortalManager\Users;
use PortalManager\Installer;

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

        if(!$arg[hidePatern]){ $this->hidePatern = false; }
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
            $this->view->render($subfolder.$this->theme_wire.'header'.( (isset($_GET['header'])) ? '-'.$_GET['header'] : '' ),$mode);
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
