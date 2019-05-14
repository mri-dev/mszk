<?php
class account extends Controller
{
	function __construct(){
		parent::__construct();
		parent::$pageTitle = 'Fiók';

		$this->addPagePagination(array(
			'link' => false,
			'title' => __('Adminisztráció')
		));

		$this->addPagePagination(array(
			'link' => '/adminconsole/felhasznalok',
			'title' => __('Felhasználók')
		));

  	if (isset($_POST['createUserByAdmin'])) {
			try {
				$this->Users->createByAdmin($_POST);
				$return = '';
				if(isset($_GET['ret'])) {
					$return = $_GET['ret'];
				}
				Helper::reload($return);

			} catch (\Exception $e) {
				$this->view->err 	= true;
				$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
			}
		}

		if (isset($_POST['saveUserByAdmin'])) {
			try {
				$this->Users->saveByAdmin($_GET['ID'],$_POST);
				$return = '';
				if(isset($_GET['ret'])) {
					$return = $_GET['ret'];
				}
				Helper::reload($return);
			} catch (\Exception $e) {
				$this->view->err 	= true;
				$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
			}
		}

		// Szerkesztés
		if ($_GET['t'] == 'edit')
		{
			$data = $this->Users->get(array('user' => $_GET['ID'], 'userby' => 'ID'));

			$this->addPagePagination(array(
				'link' => false,
				'title' => $data['data']['nev']
			));
			parent::$pageTitle = '<strong>'.$data['data']['nev'] .'</strong> | '.__('Fiók szerkesztése');

			$this->out('data', $data['data']);
		}

		$this->out('user_groupes',$this->Users->getUserGroupes());



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
