<?
use PortalManager\Pages;

class oldalak extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Oldalak / Adminisztráció';

      $this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

			$pages = new Pages( $this->view->gets[2], array( 'db' => $this->db )  );
			$pages->setAdmin( true );

			if(isset($_POST['add'])){
				try{
					$pages->add($_POST);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			switch($this->view->gets[1]){
				case 'szerkeszt':
					if(isset($_POST['save'])){
						try{
							$pages->save($_POST);
							Helper::reload();
						}catch(Exception $e){
							$this->view->err 	= true;
							$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
						}
					}
					$this->out( 'page', $pages->get( $this->view->gets[2]) );
				break;
				case 'torles':
					if(isset($_POST['delId'])){
						try{
							$pages->delete($this->view->gets[2]);
							Helper::reload('/oldalak');
						}catch(Exception $e){
							$this->view->err 	= true;
							$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
						}
					}
					$this->out( 'page', $pages->get( $this->view->gets[2]) );
				break;
			}

			// Oldal fa betöltés
			$page_tree 	= $pages->getTree();
			// Oldalak
			$this->out( 'pages', $page_tree );

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
