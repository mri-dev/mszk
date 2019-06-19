<?php
use PortalManager\Documents;

class dokumentumok extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Dokumentumok');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));

			// Ha nincs belépve, akkor átirányít a bejelentkezésre
			if ( !$this->Users->user && $this->gets[0] != 'belepes' && $this->gets[0] != 'regisztracio') {
				Helper::reload('/belepes');
			}

      if ($this->gets[1] != '') {
        $doc_type = $this->gets[1];
        parent::$pageTitle = $doc_type . ' | ' . parent::$pageTitle;
      }

      if ($doc_type) {
        $this->addPagePagination(array(
  				'link' => '/'.__CLASS__.'/'.$doc_type,
  				'title' => $doc_type
  			));
      }

			$uid = $this->view->_USERDATA['data']['ID'];

			$docs = new Documents(array('db' => $this->db));
			$docs_folders = $docs->getAvaiableFolders( $uid );
			$this->out('folders', $docs_folders);

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
