<?php
use PortalManager\Projects;

class uzenetek extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Üzenetek');

		  /*$this->addPagePagination(array(
				'link' => '/',
				'title' => parent::$pageTitle
			));*/

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));

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

		public function session()
		{
			$uid = (int)$this->view->_USERDATA['data']['ID'];
			if ($this->gets[2] != '') {
				$session = $this->gets[2];
				$projects = new Projects(array('db' => $this->db));
				$projectdata = $projects->getProjectData( $session, $uid);
				parent::$pageTitle = $projectdata['title'] . ' '.__('üzenetek');

				$this->addPagePagination(array(
					'link' => '/',
					'title' => $projectdata['title']
				));
			}
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
