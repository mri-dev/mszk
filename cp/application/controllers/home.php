<?php
use PortalManager\Portal;

class home extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Adminisztráció';

			// Bejelentkezés ellenőrzése
    	$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

			// Ha nincs belépve, akkor átirányít a bejelentkezésre
			if ($this->view->adm->logged === false && $this->gets[0] != 'belepes' && $this->gets[0] != 'regisztracio') {
				Helper::reload('/belepes');
			}

			// Kijelentkeztető
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
