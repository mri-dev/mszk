<?php

class activate extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Fiók aktiválás';

			$this->out( 'bodyclass', 'activate');


			if ($_GET['success'] == 1) {
				$this->view->err    = true;
				$this->view->bmsg   = Helper::makeAlertMsg('pSuccess', __('Sikeresen regisztrálta fiókját. Hamarosan kap egy aktiváló e-mailt az Ön által megadott e-mail címére!') );
			}

			// Regisztráció
			if(isset($_POST['register'])){
				try{
						$ret = (isset($_GET['return'])) ? $_GET['return'] : '/regisztracio/?success=1';
						$this->Users->add($_POST);
						Helper::reload( $ret );
				}catch(Exception $e){
						$this->view->err    = true;
						$this->view->bmsg   = Helper::makeAlertMsg('pError', $e->getMessage());
						$this->view->code 	= $e->getCode();
				}
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
