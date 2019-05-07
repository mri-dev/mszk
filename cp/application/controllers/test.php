<?php
use MailManager\MailTemplates;
use PortalManager\Template;

class test extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Adminisztráció';

      $arg = array(
  			'user_nev' 		=> trim($data['nev']),
  			'user_jelszo' 	=> trim($origin_pw),
  			'user_email' 	=> $email,
  			'settings' 		=> $this->db->settings,
  			'activateKey' 	=> $activateKey
  		);
      $arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('register_user_group_user', $arg);

      echo '<div style="background: #f4f8ff;">'.(new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg ).'</div>';

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
