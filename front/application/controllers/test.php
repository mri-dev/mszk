<?php

use PortalManager\OfferRequests;

class test extends Controller {
		function __construct(){
			parent::__construct();
			parent::$pageTitle = '';

      $request = new OfferRequests(array('db' => $this->db));
      $saved = $request->collectOfferData('29d65d74ae1434dbf430c60a2e3e425e', 'hashkey');
      $this->view->configuration = $saved;

			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description', 'MOZA Cementlap tervező program.');
			$SEO .= $this->view->addMeta('keywords', 'tervező, egyedi, cementlap, csempe');
			$SEO .= $this->view->addMeta('revisit-after','3 days');

			// FB info
			$SEO .= $this->view->addOG('title', $this->view->settings['page_title'] . ' - '.$this->view->settings['page_description']);
			$SEO .= $this->view->addOG('description', $this->view->settings['about_us']);
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url', CURRENT_URI );
			$SEO .= $this->view->addOG('image', $this->view->settings['domain'].'/cp'.$this->view->settings['logo']);
			$SEO .= $this->view->addOG('site_name', $this->view->settings['page_title']);
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
