<?php
namespace FreePBX\modules;
/*
 * Class stub for BMO Module class
 * In _Construct you may remove the database line if you don't use it
 * In getActionbar change extdisplay to align with whatever variable you use to decide if the page is in edit mode.
 *
 */

 /**
  * https://getbootstrap.com/docs/4.6/components/badge/
  */

class Gateway extends \DB_Helper implements \BMO  {
	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new Exception("Not given a FreePBX Object");
		}
		$this->FreePBX 	= $freepbx;
		$this->db 		= $freepbx->Database;
		$this->astman 	= $this->FreePBX->astman;
	}

	//Install method. use this or install.php using both may cause weird behavior
	//Runs on both fresh install and module upgrade: re-applies the PJSIP settings
	//on every existing gateway so extensions created by older module versions
	//(missing e.g. user_eq_phone) get backfilled instead of staying stale.
	public function install() {
		$gateways = $this->getAllGateway();
		foreach ($gateways as $gateway) {
			$this->applyGatewaySipConfig($gateway["extension"], $gateway["gateway"], $gateway["accountcode"]);
		}
		if (!empty($gateways)) {
			needreload();
		}
	}

	//Uninstall method. use this or install.php using both may cause weird behavior
	public function uninstall() {
		$gateways = $this->getAllGateway();
		foreach($gateways as $gateway){
			$sql 	= "UPDATE sip SET data = 'from-internal' WHERE id = :extension AND keyword = 'context'";
			$stm 	= $this->db->prepare($sql);
			$stm->execute(array(":extension" => $gateway["extension"]));
		}

		$sql		= "TRUNCATE TABLE gateway;";
		$this->db->prepare($sql)->execute();
	}

	//Not yet implemented
	public function backup() {}

	//not yet implimented
	public function restore($backup) {}

	//process form
	public function doConfigPageInit($page) {}

	// Set by showPage() when it redisplays add_gateway.php/edit_gateway.php
	// after a failed validate() (e.g. duplicate DID). That POST-back has no
	// "action" in the request (the form posts to plain "?display=gateway"),
	// so getActionBar() below has no way to know it should still show the
	// Submit/Cancel buttons unless we tell it explicitly.
	private $lastFailedFormAction = null;

	//This shows the submit buttons
	public function getActionBar($request) {
		$buttons = [];
		$action = !empty($request["action"]) ? $request["action"] : $this->lastFailedFormAction;
		if(!empty($action)){
			if($action === "help"){
				return $buttons;
			}
		}
		else{
			return $buttons;
		}
		switch($_GET['display']) {
			case 'gateway':
				$buttons = array(
					'submit' => array(
						'name' => 'submit',
						'id' => 'submit',
						'value' => _('Submit')
					),
					'Cancel' => array(
						'name' => 'cancel',
						'id' => 'cancel',
						'value' => _('Cancel')
					),
				);
			break;
		}
		return $buttons;
	}

	public function showPage(){
		$request = freepbxGetSanitizedRequest();
		$lang 	 = $_COOKIE["lang"];
		$jsloc 	 = "[]";
		if( file_exists(__DIR__."/i18n/$lang/LC_MESSAGES/gateway.json")){
			$jsloc = file_get_contents(__DIR__."/i18n/$lang/LC_MESSAGES/gateway.json");
		}
		$request["action"] = empty($request["action"]) ? "" : $request["action"];
		switch($request["action"]){
			case "add_gateway":
				$vars = array('title' => _("Add Gateway"));
				$vars["users"]		= $this->getUsers();	
				$vars["jsloc"] = $this->cleanJSLoc($jsloc);
				return load_view(__DIR__.'/views/add_gateway.php',$vars);
			case "edit_gateway":
				$vars = array('title' => _("Edit Gateway"));
				$vars["users"]		= $this->getUsers();
				$vars["gateway"]	= $this->getGateway($request["gateway"]);
				$vars["accountcode"]= $vars["gateway"]["accountcode"] ?? '';
				$vars["jsloc"] = $this->cleanJSLoc($jsloc);
				return load_view(__DIR__.'/views/edit_gateway.php',$vars);
			case "help":
				$vars = ["img" => "/admin/modules/gateway/"];
				$vars["jsloc"] = $this->cleanJSLoc($jsloc);
				return load_view(__DIR__.'/views/description.php', $vars);
			default:
				$vars = ['title' => _("Gateway List")];
				$vars["jsloc"] = $this->cleanJSLoc($jsloc);
				if(!empty($request["edit"]) && $request["edit"] === "no" ){
					$result = $this->addGateway($request);
					if(!$result["status"]){
						// Stay on the "Add" form and keep what was typed instead
						// of dropping the user back on the grid.
						$addVars 			= array('title' => _("Add Gateway"));
						$addVars["users"]	= $this->getUsers();
						$addVars["jsloc"]	= $this->cleanJSLoc($jsloc);
						$addVars["message"]	= $result["message"];
						$addVars["formdata"]	= $request;
						$this->lastFailedFormAction = "add_gateway";
						return load_view(__DIR__.'/views/add_gateway.php',$addVars);
					}
				}

				if(!empty($request["edit"]) && $request["edit"] === "yes" ){
					$result = $this->updateGateway($request);
					if(!$result["status"]){
						// Stay on the "Edit" form and redisplay what was typed
						// (not the stale DB row) instead of dropping the user
						// back on the grid.
						$editVars 				= array('title' => _("Edit Gateway"));
						$editVars["users"]		= $this->getUsers();
						$editVars["jsloc"]		= $this->cleanJSLoc($jsloc);
						$editVars["message"]	= $result["message"];
						$editVars["accountcode"]	= $request["accountcode"] ?? '';
						$editVars["gateway"]	= array(
							"extension"		=> $request["extension"] ?? '',
							"contact"		=> $request["contact"] ?? '',
							"description"	=> $request["description"] ?? '',
							"address"		=> $request["address"] ?? '',
							"city"			=> $request["city"] ?? '',
							"zip_code"		=> $request["zip"] ?? '',
							"country"		=> $request["country"] ?? '',
							"email"			=> $request["email"] ?? '',
							"gateway"		=> $request["gateway"] ?? '',
							"dids"			=> json_encode($request["dids"] ?? []),
							"call_limit"	=> $request["call_limit"] ?? 0,
							"accountcode"	=> $request["accountcode"] ?? '',
						);
						$this->lastFailedFormAction = "edit_gateway";
						return load_view(__DIR__.'/views/edit_gateway.php',$editVars);
					}
				}
				return load_view(__DIR__.'/views/grid.php',$vars);
		}

	}

	/**
	 * cleanJSLoc
	 *
	 * @param  string $jsloc
	 * @return string
	 */
	public function cleanJSLoc($jsloc){
		$jsloc_array = json_decode($jsloc, true);

		if (isset($jsloc_array['locale_data']['gateway'])) {
			foreach ($jsloc_array['locale_data']['gateway'] as $key => &$translation) {
				if (is_array($translation)) {		
					if (empty($translation[0]) && !empty($translation[1])) {
						array_shift($translation);
					} elseif (count($translation) === 1) {
						$translation[0] = $translation[0];
					}
				}
			}
		}
		return json_encode($jsloc_array, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * getUsers
	 *
	 * @return array
	 */
	public function getUsers(){
		$allGateway = $this->getAllGateway();
		$sql 		= "SELECT users.extension, users.name FROM users INNER JOIN sip ON (sip.id = users.extension AND sip.data LIKE 'PJSIP/%' AND users.extension NOT LIKE '99%' AND users.extension NOT LIKE '98%') ORDER BY users.extension;";
		$stm 		= $this->db->prepare($sql);
		$stm->execute();
		$ret 		= $stm->fetchAll(\PDO::FETCH_ASSOC);
		$extGateway = array_column($allGateway, 'extension');
		$result 	= array_filter($ret, function($item) use ($extGateway) {
			return !in_array($item['extension'], $extGateway);
		});
		return array_values($result);
	}

	private $aorsData = null;

	private function getAorsData() {
		if ($this->aorsData === null) {
			$response = $this->astman->send_request('Command', ['Command' => 'pjsip show aors']);
			$this->aorsData = (is_array($response) && !empty($response['data'])) ? $response['data'] : '';
		}
		return $this->aorsData;
	}

	public function getEndpointStatus($extension) {
		if (!$this->astman) {
			return 'unknown';
		}
		$data = $this->getAorsData();
		if (empty($data)) {
			return 'offline';
		}
		$pattern = '/Contact:\s+' . preg_quote($extension, '/') . '\/.*Avail/i';
		return preg_match($pattern, $data) ? 'online' : 'offline';
	}

	public function ajaxRequest($req, &$setting) {
		switch ($req) {
			case 'gatewayList':
			case 'delete':
				return true;
			default:
				return false;
			break;
		}
	}

	public function ajaxHandler(){
		$request = freepbxGetSanitizedRequest();
		switch ($request['command']) {
			case 'gatewayList':
				$gateways = $this->getAllGateway();
				foreach ($gateways as &$gw) {
					$gw['status'] = $this->getEndpointStatus($gw['extension']);
				}
				return $gateways;
			case 'delete':
				$result = $this->deleteGateway($request['gateway']);
				return $result['status'];
			default:
				return false;
			break;
		}
	}
	
	/**
	 * validate
	 *
	 * Validates format/charset of every field AND checks for duplicates
	 * (Gateway IP, Account Code, DID Base / DIDs) against the other gateways
	 * already stored, so two gateways can never silently share the same
	 * accountcode, IP, or DID.
	 *
	 * @param  array       $data             the submitted request
	 * @param  string|null $excludeExtension when editing, the extension of the
	 *                                       gateway being edited, so it isn't
	 *                                       compared against itself
	 * @return array
	 */
	public function validate($data, $excludeExtension = null){
		if( !preg_match('/^\d+$/', trim((string)($data["extension"] ?? '')))){
			return ["status" => "false", "message" => _("The extension is not numeric!")];
		}

		if(	!empty($data["contact"]) && !preg_match('/^[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+$/u', trim($data["contact"]))){
			return ["status" => "false", "message" => _("Contact Error: Wrong characters detected!")];
		}

		if( !empty($data["description"]) && !preg_match('/^[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+$/u', trim($data["description"]))){
			return ["status" => "false", "message" => _("Description Error: Wrong characters detected!")];
		}

		if(	!empty($data["address"]) && !preg_match('/^[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+$/u', trim($data["address"]))){
			return ["status" => "false", "message" => _("Address Error: Wrong characters detected!")];
		}

		if( !empty($data["city"]) && !preg_match('/^[a-zA-Z0-9éèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+$/u', trim($data["city"]))){
			return ["status" => "false", "message" => _("City Error: Wrong characters detected!")];
		}

		if( !empty($data["country"]) && !preg_match('/^[a-zA-ZéèêëàâùûüîïôçœæÉÈÊËÀÂÙÛÜÎÏÔÇŒÆ_\s\-]+$/u', trim($data["country"]))){
			return ["status" => "false", "message" => _("Country Error: Wrong characters detected!")];
		}

		if( !empty($data["zip"]) && !preg_match('/^\d{3,6}$/', trim($data["zip"]))){
			return ["status" => "false", "message" => _("ZIP Code must contain digits only (3 to 6, column is limited to 6 characters)!")];
		}

		// FILTER_VALIDATE_EMAIL alone accepts RFC5321 quoted local-parts
		// (e.g. "<script>"@x.com), so also reject anything outside a plain,
		// unquoted address before trusting the value.
		if(!empty($data["email"])){
			$email = trim($data["email"]);
			if(!preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~\-]+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
				return ["status" => "false", "message" => _("Invalid email.")];
			}
		}

		// Gateway IP: strict IPv4, with an optional ":port" (1-65535).
		$gw = trim($data["gateway"] ?? '');
		if(!preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?::(\d{1,5}))?$/', $gw, $m)){
			return ["status" => "false", "message" => _("Gateway Error: Expected an IPv4 address, with an optional :port (e.g. 200.25.46.30 or 200.25.46.30:5061)!")];
		}
		$gw_ip = $m[1];
		if(!filter_var($gw_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
			return ["status" => "false", "message" => _("Gateway Error: Invalid IPv4 address!")];
		}
		if(isset($m[2]) && $m[2] !== '' && ((int)$m[2] < 1 || (int)$m[2] > 65535)){
			return ["status" => "false", "message" => _("Gateway Error: Port must be between 1 and 65535!")];
		}

		if( !empty($data["accountcode"]) && !preg_match('/^[a-zA-Z0-9_\-]{1,40}$/', trim($data["accountcode"]))){
			return ["status" => "false", "message" => _("Account Code Error: only letters, digits, \"_\" and \"-\" are allowed (max 40 characters)!")];
		}

		if (!empty($data["call_limit"]) && !preg_match('/^\d+$/', trim((string)$data["call_limit"]))) {
			return ["status" => "false", "message" => _('Call Limit: must be a positive integer!')];
		}

		$submittedDids = [];
		foreach($data["dids"] as $did){
			$did = trim((string)$did);
			if(!preg_match('/^\d{1,15}$/', $did)){
				return ["status" => "false", "message" => _("Did: must contain digits only (max 15)!")];
			}
			if(in_array($did, $submittedDids, true)){
				return ["status" => "false", "message" => sprintf(_("Did Error: %s is entered more than once on this gateway!"), $did)];
			}
			$submittedDids[] = $did;
		}

		// Duplicate checks against every other existing gateway: same IP,
		// same Account Code, or an overlapping DID would silently break
		// routing/billing (this is what caused the accountcode mix-up).
		$others = array_filter($this->getAllGateway(), function($g) use ($excludeExtension){
			return $excludeExtension === null || (string)$g['extension'] !== (string)$excludeExtension;
		});

		foreach($others as $g){
			$otherIp = explode(":", $g['gateway'])[0];
			if($otherIp === $gw_ip){
				return ["status" => "false", "message" => sprintf(_("Gateway Error: IP address %s is already used by gateway %s!"), $gw_ip, $g['extension'])];
			}

			if(!empty($data["accountcode"]) && !empty($g['accountcode']) && trim($data["accountcode"]) === $g['accountcode']){
				return ["status" => "false", "message" => sprintf(_("Account Code Error: %s is already used by gateway %s!"), trim($data["accountcode"]), $g['extension'])];
			}

			$otherDids = json_decode($g['dids'], true);
			$otherDids = is_array($otherDids) ? array_map('strval', $otherDids) : [];
			foreach($submittedDids as $did){
				if(in_array($did, $otherDids, true)){
					return ["status" => "false", "message" => sprintf(_("Did Error: %s is already used by gateway %s!"), $did, $g['extension'])];
				}
			}
		}

		return ["status" => "true", "message" => ""];
	}

	public function getRightNav($request) {
		$html = load_view(__DIR__ . '/views/rnav.php', $request);
		return $html;
	}

	public function getSipValue($extension, $keyword) {
		$sql = "SELECT data FROM sip WHERE id = :extension AND keyword = :keyword LIMIT 1";
		$stm = $this->db->prepare($sql);
		$stm->execute([":extension" => $extension, ":keyword" => $keyword]);
		$row = $stm->fetch(\PDO::FETCH_ASSOC);
		return $row ? $row["data"] : "";
	}

	public function getAllGateway() {
		$sql = "SELECT * FROM gateway ORDER BY extension";
		$stm = $this->db->prepare($sql);
		$stm->execute();
		$ret = $stm->fetchAll(\PDO::FETCH_ASSOC);
		return $ret;
	}
	
	/**
	 * getGateway
	 *
	 * @param  string $gateway
	 * @return bool
	 */
	public function getGateway($gateway) {
		$sql = "SELECT * FROM gateway WHERE extension = :gateway LIMIT 1";
		$stm = $this->db->prepare($sql);
		$stm->execute([":gateway" => $gateway]);
		$ret = $stm->fetch(\PDO::FETCH_ASSOC);
		return $ret;
	}

	/**
	 * extensionIsAvailablePjsipUser
	 *
	 * True when $extension is a real PJSIP extension eligible to become a
	 * gateway (same criteria as getUsers()). Guards addGateway() against
	 * accepting an arbitrary extension number that isn't actually wired up
	 * to a PJSIP endpoint - the web form only ever offers valid choices via
	 * its <select>, but a direct API/POST call has no such guardrail.
	 *
	 * @param  string $extension
	 * @return bool
	 */
	private function extensionIsAvailablePjsipUser($extension) {
		$sql = "SELECT 1 FROM users INNER JOIN sip ON (sip.id = users.extension AND sip.data LIKE 'PJSIP/%' AND users.extension NOT LIKE '99%' AND users.extension NOT LIKE '98%') WHERE users.extension = :extension LIMIT 1";
		$stm = $this->db->prepare($sql);
		$stm->execute([":extension" => $extension]);
		return (bool) $stm->fetchColumn();
	}

	/**
	 * addGateway
	 *
	 * Validates and inserts a new gateway. Shared by the web UI form
	 * (showPage()) and the GraphQL API (Api/Gql/Gateway.php) so both go
	 * through the exact same validation / duplicate-checks / prepared
	 * statement insert path instead of two copies that could drift apart.
	 *
	 * @param  array $data same shape as the "Add" form POST: extension,
	 *                      contact, description, address, city, zip,
	 *                      country, email, gateway, accountcode,
	 *                      call_limit, dids (array)
	 * @return array ['status' => bool, 'message' => string, 'gateway' => array|null]
	 */
	public function addGateway($data){
		$data['dids'] = (isset($data['dids']) && is_array($data['dids'])) ? $data['dids'] : [];

		$validate = $this->validate($data);
		if($validate['status'] === 'false'){
			return ['status' => false, 'message' => $validate['message'], 'gateway' => null];
		}

		if(!$this->extensionIsAvailablePjsipUser($data['extension'])){
			return ['status' => false, 'message' => _("Extension is not an available PJSIP extension!"), 'gateway' => null];
		}

		if(!empty($this->getGateway($data['extension']))){
			return ['status' => false, 'message' => _("A gateway already exists on this extension!"), 'gateway' => null];
		}

		$gatewayAddress = strpos($data['gateway'], ":") ? $data['gateway'] : $data['gateway'].":5060";

		$sql  	= "INSERT INTO gateway (`extension`, `contact` ,`description` ,`address` ,`city` ,`zip_code`,`country`,`email`,`gateway`,`dids`,`call_limit`,`accountcode`) VALUES (:extension, :contact, :description, :address, :city, :zip_code, :country, :email, :gateway, :dids, :call_limit, :accountcode)";
		$stm 	= $this->db->prepare($sql);
		$stm->execute(array(
			':extension' 	=> $data["extension"],
			':contact' 		=> $data["contact"],
			':description' 	=> $data["description"] ?? '',
			':address' 		=> $data["address"] ?? '',
			':city' 		=> $data["city"] ?? '',
			':zip_code' 	=> $data["zip"] ?? '',
			':country' 		=> $data["country"] ?? '',
			':email' 		=> $data["email"] ?? '',
			':gateway' 		=> $gatewayAddress,
			':dids' 		=> json_encode($data["dids"]),
			':call_limit'	=> intval($data["call_limit"] ?? 0),
			':accountcode'	=> $data["accountcode"] ?? '',
		));

		$this->applyGatewaySipConfig($data['extension'], $gatewayAddress, $data['accountcode'] ?? '');
		needreload();

		return ['status' => true, 'message' => _("Gateway added successfully"), 'gateway' => $this->getGateway($data['extension'])];
	}

	/**
	 * updateGateway
	 *
	 * Validates and updates an existing gateway. Shared by the web UI form
	 * and the GraphQL API, see addGateway().
	 *
	 * @param  array $data same shape as addGateway(); 'extension' is
	 *                      required and identifies which gateway to update.
	 * @return array ['status' => bool, 'message' => string, 'gateway' => array|null]
	 */
	public function updateGateway($data){
		$data['dids'] = (isset($data['dids']) && is_array($data['dids'])) ? $data['dids'] : [];

		if(empty($data['extension']) || empty($this->getGateway($data['extension']))){
			return ['status' => false, 'message' => _("Gateway not found!"), 'gateway' => null];
		}

		$validate = $this->validate($data, $data['extension']);
		if($validate['status'] === 'false'){
			return ['status' => false, 'message' => $validate['message'], 'gateway' => null];
		}

		$gatewayAddress = strpos($data['gateway'], ":") ? $data['gateway'] : $data['gateway'].":5060";

		$sql    = "UPDATE gateway SET contact = :contact, description = :description, address = :address, city = :city, zip_code = :zip_code, country = :country, email = :email, gateway = :gateway, dids = :dids, call_limit = :call_limit, accountcode = :accountcode WHERE extension = :extension";
		$stm 	= $this->db->prepare($sql);
		$stm->execute(array(
			':extension' 	=> $data["extension"],
			':contact' 		=> $data["contact"],
			':description' 	=> $data["description"] ?? '',
			':address' 		=> $data["address"] ?? '',
			':city' 		=> $data["city"] ?? '',
			':zip_code' 	=> $data["zip"] ?? '',
			':country' 		=> $data["country"] ?? '',
			':email' 		=> $data["email"] ?? '',
			':gateway' 		=> $gatewayAddress,
			':dids' 		=> json_encode($data["dids"]),
			':call_limit'	=> intval($data["call_limit"] ?? 0),
			':accountcode'	=> $data["accountcode"] ?? '',
		));

		$this->applyGatewaySipConfig($data['extension'], $gatewayAddress, $data['accountcode'] ?? '');
		needreload();

		return ['status' => true, 'message' => _("Gateway updated successfully"), 'gateway' => $this->getGateway($data['extension'])];
	}

	/**
	 * deleteGateway
	 *
	 * Shared by the AJAX handler (grid delete button) and the GraphQL API.
	 *
	 * @param  string $extension
	 * @return array ['status' => bool, 'message' => string]
	 */
	public function deleteGateway($extension){
		if(empty($extension) || empty($this->getGateway($extension))){
			return ['status' => false, 'message' => _("Gateway not found!")];
		}

		$sql = "DELETE FROM gateway WHERE extension = :extension LIMIT 1";
		$stm = $this->db->prepare($sql);
		$stm->execute([":extension" => $extension]);

		$sql = "UPDATE sip SET data = 'from-internal' WHERE id = :extension AND keyword = 'context'";
		$stm = $this->db->prepare($sql);
		$stm->execute([":extension" => $extension]);

		needreload();

		return ['status' => true, 'message' => _("Gateway deleted successfully")];
	}

	/**
	 * applyGatewaySipConfig
	 *
	 * Upserts the PJSIP keywords a gateway extension needs (context, IP match,
	 * accountcode, billing amaflags, user_eq_phone). Idempotent, so it is safe
	 * to call from add, edit, and install/upgrade alike.
	 *
	 * @param  string $extension
	 * @param  string $gatewayAddress "ip[:port]" as stored in gateway.gateway
	 * @param  string $accountcode
	 * @return void
	 */
	private function applyGatewaySipConfig($extension, $gatewayAddress, $accountcode) {
		$gateway_ip = explode(":", $gatewayAddress)[0];

		$settings = array(
			'context'       => 'from-internal-gateway',
			'accountcode'   => $accountcode,
			'match'         => $gateway_ip,
			'amaflags'      => 'BILLING',
			'user_eq_phone' => 'yes',
		);

		$sql = "INSERT INTO sip (id, keyword, data) VALUES (:extension, :keyword, :data) ON DUPLICATE KEY UPDATE data = VALUES(data)";
		$stm = $this->db->prepare($sql);
		foreach ($settings as $keyword => $data) {
			$stm->execute(array(":extension" => $extension, ":keyword" => $keyword, ":data" => $data));
		}
	}

	public static function myDialplanHooks() {
		return 900;
	}

	public function doDialplanHook(&$ext, $engine, $priority) {
		if ($engine != "asterisk") { return; }
		$context = "lock-feature-code";

		$fca = $this->FreePBX->Featurecodeadmin;

		$featurecodes = $fca->printExtensions()["items"];
		foreach($featurecodes as $featurecode){
			$fc = $featurecode[1];
			if(!empty($fc)){
				$ext->add($context, $fc, '', new \ext_Hangup);
			}			
		}

		$context = "from-trunk-gateway";
		$fc = "_X.";
	   	$ext->add($context, $fc, '', new \ext_set('number','${PJSIP_HEADER(read,From)}')); 
		$ext->add($context, $fc, '', new \ext_noop('--- Read From ${number} -----'));                  
      	$ext->add($context, $fc, '', new \ext_set('number','${CUT(number,@,1)}'));                           
      	$ext->add($context, $fc, '', new \ext_set('number','${CUT(number,:,2)}'));                           
      	$ext->add($context, $fc, '', new \ext_set('CALLERID(num)','${number}')); 
		$ext->add($context, $fc, '', new \ext_set('CALLERID(dnid)','${number}'));
		$ext->add($context, $fc, '', new \ext_set('CHANNEL(amaflags)', 'BILLING'));
		$ext->add($context, $fc, '', new \ext_goto(1,'${EXTEN}','ext-gateway'));
		$ext->add($context, 'h', '', new \ext_Hangup);

		$gateways = $this->getAllGateway();

		$context = "from-internal-gateway";
		$ext->addInclude('from-internal-gateway','lock-feature-code');
		$ext->add($context, $fc, '', new \ext_set('GW_EXT','${CHANNEL(endpoint)}'));
		$ext->add($context, $fc, '', new \ext_set('GROUP()','gw_${GW_EXT}'));
		foreach($gateways as $gateway){
			$call_limit = intval($gateway['call_limit']);
			$grp = 'gw_'.$gateway['extension'];
			$cond = '$["${GW_EXT}" = "'.$gateway['extension'].'"]';
			if(!empty($gateway['accountcode'])){
				$ext->add($context, $fc, '', new \ext_execif(
					$cond, 'Set', 'CHANNEL(accountcode)='.$gateway['accountcode']
				));
				$ext->add($context, $fc, '', new \ext_execif(
					$cond, 'Set', 'CHANNEL(accountcode)='.$gateway['accountcode']
				));
			}
			$ext->add($context, $fc, '', new \ext_execif(
				$cond, 'Set', 'CHANNEL(amaflags)=BILLING'
			));
			if($call_limit > 0){
				$ext->add($context, $fc, '', new \ext_gotoif(
					'$["${GW_EXT}" = "'.$gateway['extension'].'" && ${GROUP_COUNT('.$grp.')} > '.$call_limit.']',
					'ext-gateway,gw-limit,1'
				));
			}
			foreach($gateways as $dstGateway){
				if($dstGateway['extension'] === $gateway['extension']){
					continue;
				}
				$dstDids = json_decode($dstGateway['dids'], true);
				foreach($dstDids as $dstDid){
					$ext->add($context, $fc, '', new \ext_execif(
						'$["${GW_EXT}" = "'.$gateway['extension'].'" && "${EXTEN}" = "'.$dstDid.'"]',
						'Set', 'CHANNEL(accountcode)='.$gateway['accountcode'].' - '.$dstGateway['accountcode']
					));
				}
			}
		}
		$ext->add($context, $fc, '', new \ext_set('number','${PJSIP_HEADER(read,From)}'));
		$ext->add($context, $fc, '', new \ext_set('number','${CUT(number,@,1)}'));
		$ext->add($context, $fc, '', new \ext_set('number','${CUT(number,:,2)}'));
		$ext->add($context, $fc, '', new \ext_set('CALLERID(dnid)','${number}'));
		$ext->add($context, $fc, '', new \ext_goto(1,'${EXTEN}','from-internal'));
		$ext->add($context, 'h', '', new \ext_Hangup);
		
		$context = "ext-gateway";
		$ext->add($context, 'gw-limit', '', new \ext_busy('30'));
		$ext->add($context, 'gw-offline', '', new \ext_busy('20'));
		foreach($gateways as $gateway){
			$dids = json_decode($gateway["dids"], true);
			$call_limit = intval($gateway["call_limit"]);
			$grp = 'gw_'.$gateway["extension"];
			foreach($dids as $index => $did){
				if($index === 0){
					$main_user = '"'.$did.'"';
				}
				$ext->add($context, "$did", "", new \ext_noop('--- Root DID '.$main_user.' DID ${EXTEN} -----'));
				$ext->add($context, "$did", '', new \ext_set('CHANNEL(amaflags)', 'BILLING'));
				$ext->add($context, "$did", '', new \ext_set('CHANNEL(accountcode)',$gateway["accountcode"]));
				foreach($gateways as $srcGateway){
					if($srcGateway["extension"] === $gateway["extension"]){
						continue;
					}
					$ext->add($context, "$did", '', new \ext_execif(
						'$["${CHANNEL(endpoint)}" = "'.$srcGateway["extension"].'"]',
						'Set', 'CHANNEL(accountcode)='.$srcGateway["accountcode"].' - '.$gateway["accountcode"]
					));
				}
				$ext->add($context, "$did", '', new \ext_set('mainuser',"$main_user"));
				$ext->add($context, "$did", '', new \ext_set('GW_CONTACT', '${PJSIP_DIAL_CONTACTS('.$gateway["extension"].')}'));
				$ext->add($context, "$did", '', new \ext_gotoif(
					'$["${GW_CONTACT}" = ""]',
					'ext-gateway,gw-offline,1'
				));
				$ext->add($context, "$did", '', new \ext_set('GW_ADDR', '${CUT(GW_CONTACT,@,2)}'));
				$ext->add($context, "$did", '', new \ext_set('GW_ADDR', '${CUT(GW_ADDR,\;,1)}'));
				if($call_limit > 0){
					$ext->add($context, "$did", '', new \ext_set('GROUP()', $grp));
					$ext->add($context, "$did", '', new \ext_set('GW_COUNT', '${GROUP_COUNT('.$grp.')}'));
					$ext->add($context, "$did", '', new \ext_gotoif('$[${GW_COUNT} > '.$call_limit.']', 'ext-gateway,gw-limit,1'));
				}
				$ext->add($context, "$did", '', new \ext_dial('PJSIP/${mainuser}/sip:${EXTEN}@${GW_ADDR},,Hhtrb(func-apply-sipheaders^s^1)'));
			}
		}
		$ext->addInclude('ext-gateway','bad-number');
		$ext->add($context, "h", '', new \ext_Hangup);	
	}
}