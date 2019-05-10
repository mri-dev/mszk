<?php
class adminconsole extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Adminisztráció');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));

			// Ha nincs belépve, akkor átirányít a bejelentkezésre
			if ( !$this->Users->user && $this->gets[0] != 'belepes' && $this->gets[0] != 'regisztracio') {
				Helper::reload('/belepes');
			}

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

    /**
    * /adminconsole/felhasznalok - Felhasználók Controller
    **/
    public function felhasznalok()
    {
      parent::$pageTitle = __('Felhasználók');
      $this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));

    }
    /**
    * /adminconsole/list - Dinamikus listák Controller
    **/
    public function lists()
    {
      parent::$pageTitle = __('Dinamikus listák');
      $this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));

    }

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
