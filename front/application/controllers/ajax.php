<?php
use PortalManager\Categories;
use PortalManager\OfferRequests;

class ajax extends Controller{
		function __construct()
		{
			header("Access-Control-Allow-Origin: *");
			parent::__construct();
		}

		public function files()
		{
			extract($_POST);
			$ret = array(
				'success' => 0,
				'msg' => false,
				'passed' => $_POST,
				'data' => false
			);

			switch ( $this->view->gets[2] )
			{
				case 'attachment':
					$ret['FILES'] = $_FILES;
					$files = $_FILES['file'];
					switch ($this->view->gets[3])
					{
						// Ajánlatkérés csatolmányok
						case 'offers':
							$prefix = (empty($prefix)) ? '' : $prefix.'_';
							if ($files && count($files['name']) > 0 ) {
								$fi = -1;
								foreach ((array)$files['name'] as $fname) {
									$fi++;
									$original_filename = $files['name'][$fi];
									$location = '/home/webprohu/mszk.web-pro.hu/cp/src/attachments/';
									$filename = $prefix.uniqid().'_'.\Helper::makeSafeUrl($files['name'][$fi]);
									$ret['uploaded'][] = array(
										'tmp' => $files['tmp_name'][$fi],
										'to' => $location.$filename
									);

									if( move_uploaded_file($files['tmp_name'][$fi], $location.$filename) )
									{
										// success upload
										$this->db->insert(
											'attachments',
											array(
												'user_id' => (int)$user_id,
												'agroup' => 'offers',
												'filename' => addslashes($original_filename),
												'filesize' => (float)$files['size'][$fi],
												'filepath' => '/src/attachments/'.$filename
											)
										);
										$attachment_id = $this->db->lastInsertId();
										if (!empty($request_id)) {
											$this->db->insert(
												'requests_xref_attachment',
												array(
													'request_id' => (int)$request_id,
													'attachment_id' => (int)$attachment_id
												)
											);
										}
									}
								}
							}
						break;
					}
				break;
			}

			echo json_encode($ret);
		}

		function post()
		{
			extract($_POST);
			$ret = array(
				'success' => 0,
				'msg' => false,
				'passed' => $_POST,
				'data' => false
			);
			switch($type)
			{
				case 'Ajanlatkeres':
					switch ($mode) {
						case 'getResources':
							// Marketing eszközök
							$categories = new Categories( array( 'db' => $this->db ) );
							$arg = array();
							$arg['group_slug'] = 'szolgaltatasok';
							$eszkozok	= $categories->getTree( false, $arg );
							$ret['data']['szolgaltatasok'] = $eszkozok->tree;
							$ret['success'] = 1;
						break;
						case 'send':
							$request = new OfferRequests(array('db' => $this->db));
							/**/
							try {
								$back = $request->sendRequest( $_POST['requester'], $_POST['config'] );
								$ret = array_merge( $ret, $back );
								$this->setSuccess( __('Sikeresen elküldte ajánlatkérését!'), $ret);
							} catch (\Exception $e) {
								$this->escape( $e->getMessage(), $ret);
							}
							/* */
						break;
					}

				break;
				default: break;
			}
			echo json_encode($ret);
		}

		function get()
		{
			extract($_POST);

			switch($type){
				case 'settings':
					$_POST['key'] = ($_POST['key'] != '') ? (array)$_POST['key'] : array();

					if ( empty($_POST['key']) ) {
						$ret['data'] = $this->view->settings;
					} else {
						$settings = array();

						foreach ( $_POST['key'] as $key ) {
							$settings[$key] = $this->view->settings[$key];
						}

						$ret['data'] = $settings;
					}

					$ret['pass'] = $_POST;
					echo json_encode($ret);
				break;
			}

			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type, true);
		}

		function template(){
			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$this->gets[2], true);
		}

		private function setSuccess($msg, &$ret){
			$ret[msg] = $msg;
			$ret[success]	= 1;
			return true;
		}

		private function escape($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 0;
			return true;
		}
		function __destruct(){ }
	}

?>
