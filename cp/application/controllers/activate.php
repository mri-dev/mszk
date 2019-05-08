<?php

class activate extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Fiók aktiválás';

			$this->out( 'bodyclass', 'activate');


			$key = base64_decode($this->view->gets[1]);
			$key = explode('=',$key);

			try{
				$this->Users->activate($key);
			}catch(Exception $e){
				$this->out( 'msg', $e->getMessage() );
				$this->out( 'err', true );
				//Helper::reload('/');
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

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
