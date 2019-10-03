<?php
use PortalManager\Projects;
use PortalManager\Documents;
use MessageManager\Messanger;
use PortalManager\OfferRequests;

class home extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Gépház');
		/*	$this->addPagePagination(array(
				'link' => '/',
				'title' => parent::$pageTitle
			));*/
			$this->addPagePagination(array(
				'link' => '/',
				'title' => parent::$pageTitle
			));

			// Ha nincs belépve, akkor átirányít a bejelentkezésre
			if ( !$this->Users->user && $this->gets[0] != 'belepes' && $this->gets[0] != 'regisztracio') {
				Helper::reload('/belepes');
			}

			// Kijelentkeztető
			if($this->gets[1] == 'exit'){
				$this->Users->logout();
			}

			$uid = $this->view->_USERDATA['data']['ID'];
			$user_group = $this->view->_USERDATA['data']['user_group'];

			$docs = new Documents(array('db' => $this->db));
			$projects = new Projects(array('db' => $this->db));
			$messanger = new Messanger(array(
				'controller' => $this
			));

			// Díjbekérők - Lejárt
			$folderhash = $docs->findFolderHashkey('dijbekero', $uid);
			$folderinfo = $docs->getFolderData($folderhash);

			$dashboard['dijbekero']['expired'] = $docs->getList(array(
				'uid' => $uid,
				'limit' => 10,
				'folder' => $folderinfo['ID'],
				'order' => 'd.expire_at DESC',
				'expire_qry' => '<= now()',
				'teljesites_qry' => 'IS NULL'
			));

			// Díjbekérők - Összes
			$folderhash = $docs->findFolderHashkey('dijbekero', $uid);
			$folderinfo = $docs->getFolderData($folderhash);

			$dashboard['dijbekero']['all'] = $docs->getList(array(
				'uid' => $uid,
				'limit' => 1,
				'folder' => $folderinfo['ID'],
				'order' => 'd.expire_at DESC'
			));

			// Projektek
			$listarg = array();
			$listarg['uid'] = $uid;
			$listarg['closed'] = 0;
			$dashboard['projects'] = $projects->getList( $listarg );


			if ( !$this->view->is_admin_logged )
			{
				// Messangers
				$arg = array();
				$messages = $messanger->loadMessages($uid, $arg);
				$dashboard['messanger'] = $messages;

				// Ajánlatkérések
				$offerrequests = new OfferRequests(array('db' => $this->db));
				$arg = array(
					'format' => 'list'
				);
				$request_OUTBOX = $offerrequests->getUserOfferRequests( $uid, $user_group, 'from', $arg );
				$dashboard['requests_out'] = $request_OUTBOX;

				$request_INBOX = $offerrequests->getUserOfferRequests( $uid, $user_group, 'to', $arg );
				$dashboard['requests_in'] = $request_INBOX;
			}
			else
			{
				// még nem feldolgozott kéréseim
				$notoffered_requests_arg = array();
				$notoffered_requests_qry = "SELECT ID FROM requests WHERE elutasitva = 0 and offerout = 0";
				if ( !$this->view->is_admin_logged ) {
					$notoffered_requests_qry .= " and user_id = :uid";
					$notoffered_requests_arg['uid'] = $uid;
				}
				$notoffered_requests = $this->db->squery($notoffered_requests_qry, $notoffered_requests_arg)->rowCount();
				$this->out('notoffered_requests', $notoffered_requests);

				$offerrequests = new OfferRequests(array('db' => $this->db));

				if ($notoffered_requests != 0)
				{
					$arg = array('offerout' => 0, 'elutasitva' => 0, 'servicetree' => true, 'shortlist' => true);
					if ( !$this->view->is_admin_logged ) {
						$arg['user'] = $uid;
					}
					$requests = $offerrequests->getList( $arg );
					$this->out('requests', $requests);
				}

				// Számla
				$folderhash = $docs->findFolderHashkey('szamla', $uid);
				$folderinfo = $docs->getFolderData($folderhash);

				$dashboard['szamla']['all'] = $docs->getList(array(
					'uid' => $uid,
					'limit' => 10,
					'folder' => $folderinfo['ID']
				));

			}
			$this->out('dashboard', $dashboard);


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
