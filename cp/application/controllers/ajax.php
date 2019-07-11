<?
use PortalManager\Template;
use PortalManager\OfferRequests;
use PortalManager\Projects;
use PortalManager\Documents;
use MessageManager\Messanger;

class ajax extends Controller
{
		function __construct(){
			parent::__construct();
		}

		function post(){
			extract($_POST);

			$ret = array(
				'success' => 0,
				'msg' => false
			);

			switch($type)
			{
				case 'Alerts':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					switch ( $mode ) {
						case 'watch':
							$unreaded = 0;
							if ($this->view->_USERDATA['data']['ID']) {
								$unreaded = (int)$this->ALERTS->getUnwatchedNum($this->view->_USERDATA['data']['ID']);
							}
							$ret['data']['unreaded'] = $unreaded;
						break;
					}

					echo json_encode($ret);
				break;
				case 'Messanger':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					// Messanger
					$this->MESSANGER = new Messanger(array(
						'controller' => $this
					));

					switch ( $mode ) {
							case 'sendMessage':
							$arg = array();
							$uid = (int)$this->view->_USERDATA['data']['ID'];

							try {
								$this->MESSANGER->addMessage( $uid, $text, $session );
								$this->setSuccess(__('Az üzenet sikeresen elküldve partnerének!'), $ret);
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}

						break;
						case 'messanger_messages':
							$arg = array();
							$uid = (int)$this->view->_USERDATA['data']['ID'];

							if (isset($session) && !empty($session))
							{
								$arg['load_session'] = $session;
							}

							$messages = $this->MESSANGER->loadMessages($uid, $arg);
							$unreaded = $messages['unreaded'];

							$data['uid'] = $uid;
							$data['unreaded'] = $unreaded;
							$data['sessions'] = $messages['sessions'];
							$data['messages'] = $messages['messages'];


							$ret['data'] = $data;
						break;
						case 'editComment':
							$arg = array();
							$uid = (int)$this->view->_USERDATA['data']['ID'];

							try {
								$this->MESSANGER->editMessangerComment($message['sessionid'], $message['my_relation'], $message['notice']);
								$this->setSuccess(__('Az üzenet megjegyzése sikeresen mentve lett!'), $ret);
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
						case 'archiver':
							$arg = array();
							$uid = (int)$this->view->_USERDATA['data']['ID'];

							try {
								$this->MESSANGER->archiveSession($message['sessionid'], $message['my_relation'], ($message['archived'] == '0' || $message['archived'] == 'false')?0:1);
								$this->setSuccess(__('Az üzenet archiv állapota módosítva lett!'), $ret);
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
					}

					echo json_encode($ret);
				break;
				case 'Documents':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					$this->docs = new Documents(array('db' => $this->db));

					switch ( $mode )
					{
						case 'getList':
							$arg = array();
							$arg['uid'] = $this->view->_USERDATA['data']['ID'];
							$arg['from_user'] = $arg['uid'];
							$arg['limit'] = 99999;
							if (isset($_POST['params']['not_in_project'])) {
								$arg['not_in_project'] = $_POST['params']['not_in_project'];
							}
							$docs = $this->docs->getList($arg);
							$ret['data'] = $docs['data'];
							$ret['pparams'] = $arg;
							$this->setSuccess(__('Dokumentumok betöltve'), $ret);
						break;
					}

					unset($this->docs);
					echo json_encode($ret);
				break;
				case 'Projects':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					$projects = new Projects(array('db' => $this->db));

					switch ( $mode )
					{
						case 'get':
							$listarg = array();
							$listarg['uid'] = $this->view->_USERDATA['data']['ID'];
							$listarg['getproject'] = $hashkey;

							try {
								$p = $projects->getList( $listarg );
								$ret['data'] = $p;
								$this->setSuccess(__('Project adatok betöltve.'), $ret);
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}

						break;
						case 'saveProject':
							try {
								$permission = $projects->validateProjectPermission( $project['hashkey'], $this->view->_USERDATA['data']['ID'] );

								if ( $permission )
								{
									$ret['data'] = $projects->userModifyProject( $project, $this->view->_USERDATA['data']['ID'] );
									$this->setSuccess(__('Projekt adatok mentésre kerültek.'), $ret);
								} else {
									$this->escape(__('Önnek nincs jogosultsága a projekt adatainak módosításához.'), $ret);
								}
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}

						break;
						case 'addDocument':
							try {
								$projects->addDocument( $project, $doc, $this->view->_USERDATA['data']['ID'] );
								$this->setSuccess(__('Dokumentum hozzáadásra került a projekthez.'), $ret);
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
					}
						unset($projects);
					echo json_encode($ret);
				break;
				case 'Requests':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					switch ( $mode )
					{
						case 'List':
							$requests = new OfferRequests( array('db' => $this->db) );
							$arg = array();
							if (isset($_POST['filter'])) {
								$arg = (array)$_POST['filter'];
							}
							$ret['data'] = $requests->getList( $arg );
						break;
						case 'sendServiceRequest':
							$requests = new OfferRequests( array('db' => $this->db) );
							try {
								$ret['t'] = $requests->sendServiceRequest( $request, $servicesus );
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
						case 'requestActions':
							$requests = new OfferRequests( array('db' => $this->db) );
							try {
								switch ($what) {
									case 'visit':
										$requests->setRequestData( $request, 'visited', 1);
										$requests->setRequestData( $request, 'visited_at', NOW);
										$this->setSuccess(sprintf(__('Megtekintett állapot módosítva lett: Látta (%s)'), NOW), $ret);
									break;
									case 'unvisit':
										$requests->setRequestData( $request, 'visited', 0);
										$requests->setRequestData( $request, 'visited_at', NULL);
										$this->setSuccess(__('Megtekintett állapot módosítva lett: Láttam levétele'), $ret);
									break;
									case 'elutasit':
										$requests->setRequestData( $request, 'elutasitva', 1);
										$requests->setRequestData( $request, 'offerout', 1);
										$requests->setRequestData( $request, 'visited', 1);
										$requests->setRequestData( $request, 'visited_at', NOW);
									$this->setSuccess(__('Az ajánlatkérést sikeresen elutasítottnak jelölte.'), $ret);
									break;
									default:
										$this->escape(sprintf(__('Nincs ilyen végrehajtható művelet: %s'), $what), $ret);
									break;
								}
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
					}
					echo json_encode($ret);
					return;

				break;

				case 'RequestOffers':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					if ( !$this->view->_USERDATA ) {
						$this->escape(__('Kérjük, hogy jelentkezzen be újra!'), $ret);
					}

					$uid = (int)$this->view->_USERDATA['data']['ID'];
					$user_group = $this->view->_USERDATA['data']['user_group'];

					switch ( $mode )
					{
						case 'acceptOffer':
							$requests = new OfferRequests( array('db' => $this->db) );
							$fid = ($relation != 'from') ? $touserid : $fromuserid ;
							$toid = ($relation == 'from') ? $touserid : $fromuserid ;
							try {
								$hash = $requests->acceptOffer( $uid, $fid, $toid, (int)$request, (int)$offer, $project, $relation );
								$ret['project_hashkey'] = $hash;
								$this->setSuccess(__('Az ajánlatot elfogadta. Projekt létrehozása sikeres.'), $ret);
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
						case 'sendOffer':
							$requests = new OfferRequests( array('db' => $this->db) );
							$this->setSuccess(__('Ajánlat kérés elfogadása sikeres.'), $ret);
							try {
								$requests->registerOffer($uid, $request, $offer);
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
						case 'List':
							$requests = new OfferRequests( array('db' => $this->db) );
							try {
								$arg = $filter;
								$requestoffers = $requests->getUserOfferRequests( $uid, $user_group, $arg );
								$ret['data'] = $requestoffers;
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
						case 'requestActions':
							$requests = new OfferRequests( array('db' => $this->db) );
							try {
								switch ($what) {
									case 'visit':
										$requests->setRequestOfferData( $request, 'recepient_visited_at', NOW);
										$this->setSuccess(sprintf(__('Megtekintett állapot módosítva lett: Látta (%s)'), NOW), $ret);
									break;
									case 'unvisit':
										$requests->setRequestOfferData( $request, 'recepient_visited_at', NULL);
										$this->setSuccess(__('Megtekintett állapot módosítva lett: Láttam levétele'), $ret);
									break;
									case 'decline':
										$requests->setRequestOfferData( $request, 'recepient_declined', 1);
										$requests->setRequestOfferData( $request, 'recepient_visited_at', NOW);
									$this->setSuccess(__('Az ajánlatkérést sikeresen elutasítottnak jelölte.'), $ret);
									break;
									default:
										$this->escape(sprintf(__('Nincs ilyen végrehajtható művelet: %s'), $what), $ret);
									break;
								}
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
					}

					echo json_encode($ret);
				break;
			}
		}

		private function setSuccess($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 1;
			return true;
		}
		private function escape($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 0;
			return true;
		}

		function get(){
			extract($_POST);

			$sub_page = '';

			switch($type){
				/**
				* ANGULAR ACTIONS
				**/
				case 'Sample':
					$key = $_POST['key'];

					$re = array(
						'error' => 0,
						'msg' => null,
						'data' 	=> array()
					);
					$re['pass'] = $_POST;


					echo json_encode( $re );
				break;
				/* END: ANGULAR ACTIONS */
			}

			$sub_page = ( $sub_page != '' ) ? '_'.$sub_page : '';
			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type.$sub_page, true);
		}

		public function modal()
		{
			$type = $this->view->gets[2];
			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type, true);
		}

		function __destruct(){
		}
	}

?>
