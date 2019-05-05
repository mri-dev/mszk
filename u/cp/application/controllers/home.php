<?
use PortalManager\Portal;

class home extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Adminisztráció';

      if(isset($_POST['login'])){
          try{
              $this->AdminUser->login($_POST);
              Helper::reload($_GET['return']);
          }catch(Exception $e){
              $this->view->err    = true;
              $this->view->bmsg   = Helper::makeAlertMsg('pError', $e->getMessage());
          }
      }

			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();
		
			if($this->gets[1] == 'exit'){
				$this->AdminUser->logout();
			}

			$portal = new Portal( array( 'db' => $this->db ) );


			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description','');
			$SEO .= $this->view->addMeta('keywords','');
			$SEO .= $this->view->addMeta('revisit-after','3 days');

			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',DOMAIN);
			$SEO .= $this->view->addOG('image',DOMAIN.substr(IMG,1).'noimg.jpg');
			$SEO .= $this->view->addOG('site_name',TITLE);

			$this->view->SEOSERVICE = $SEO;
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
