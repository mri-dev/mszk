<?php
use PortalManager\Pages;

class page extends Controller{
		function __construct(){
			parent::__construct();

			$page = new Pages( false, array( 'db' => $this->db ) );

			if ( $this->view->gets[1] != '' ) {
				$this->out( 'page', $page->get($this->view->gets[1]) );

				if (!$this->view->page->getID()) {
						header("HTTP/1.0 404 Not Found");
				}
			} else {
				Helper::reload('/');
			}

			$this->out( 'bodyclass', 'singlepage' );

			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description', $page->getMeta('desc'));
			$SEO .= $this->view->addMeta('keywords','');
			$SEO .= $this->view->addMeta('revisit-after','3 days');

			// FB info
			$SEO .= $this->view->addOG('title',$page->getMeta('title'));
			$SEO .= $this->view->addOG('description',$page->getMeta('desc'));
			$SEO .= $this->view->addOG('type','article');
			$SEO .= $this->view->addOG('url', DOMAIN.ltrim($_SERVER['REQUEST_URI'],'/'));
			$SEO .= $this->view->addOG('image', $page->getMeta('image'));
			$SEO .= $this->view->addOG('site_name', $this->view->settings['page_title']);

			$this->view->SEOSERVICE = $SEO;

			parent::$pageTitle = $page->getTitle();
		}

		function __destruct(){
			if (!$this->view->page->getID()) {
				// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render('PageNotFound');		# CONTENT
				parent::__destruct();				# FOOTER
			} else {
				// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
			}
		}
	}

?>
