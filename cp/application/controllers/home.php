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
				$requestoffers = $offerrequests->getUserOfferRequests( $uid, $user_group, $arg );
				$requests = $requestoffers;
				unset($requests['data']);

				foreach ((array)$requestoffers['data'] as $d) {
					$requests['data'][$d['request_hashkey']]['my_relation'] = $d['my_relation'];
					$requests['data'][$d['request_hashkey']]['offerout_at'] = $d['offerout_at'];
					$requests['data'][$d['request_hashkey']]['services'][$d['configval']] = $d['services_name'];

					if (!isset($requests['data'][$d['request_hashkey']]['total_cash'])) {
						foreach ((array)$d['cash'] as $cash)
						{
							$requests['data'][$d['request_hashkey']]['total_cash'] += $cash;
						}
					}

					$requests['data'][$d['request_hashkey']]['data'][] = $d;
				}

				unset($requestoffers);
				$dashboard['requests'] = $requests;
			}
			else
			{
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
