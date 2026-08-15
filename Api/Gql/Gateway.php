<?php

namespace FreePBX\modules\gateway\Api\Gql;

use GraphQLRelay\Relay;
use GraphQL\Type\Definition\Type;
use FreePBX\modules\Api\Gql\Base;

/**
 * GraphQL API for the Gateway module.
 *
 * Every read goes through Gateway::getAllGateway()/getGateway() and every
 * write goes through Gateway::addGateway()/updateGateway()/deleteGateway().
 * Those are the exact same methods the "Gateway" admin page uses, so the
 * API can never bypass the module's validation (strict per-field formats,
 * IPv4 checks, XSS-safe charsets) or its duplicate checks (Gateway IP,
 * Account Code, DID) - there is no separate/parallel logic here that could
 * drift out of sync and reopen a hole. All database access in those shared
 * methods uses PDO prepared statements, so this API surface introduces no
 * new SQL injection risk. Output fields are strongly typed (String/Int/
 * Boolean/ID) by the GraphQL schema itself.
 */
class Gateway extends Base {
	protected $module = 'gateway';
	protected $description = 'Allow any extension to be used as a gateway connected via a SIP trunk to another FreePBX server or gateway.';

	/**
	 * getScopes
	 *
	 * @return array
	 */
	public static function getScopes() {
		return [
			'read:gateway' => [
				'description' => _('Read Gateways'),
			],
			'write:gateway' => [
				'description' => _('Write Gateways'),
			]
		];
	}

	/**
	 * mutationCallback
	 *
	 * @return callable|void
	 */
	public function mutationCallback() {
		if($this->checkWriteScope("gateway")) {
			return function() {
				return [
					'addGateway' => Relay::mutationWithClientMutationId([
						'name' => 'addGateway',
						'description' => _('Add a new gateway'),
						'inputFields' => $this->getAddInputFields(),
						'outputFields' => $this->getMutationOutputFields(),
						'mutateAndGetPayload' => function ($input) {
							$data = $this->resolveGatewayInput($input, null);
							$result = $this->freepbx->Gateway->addGateway($data);
							return [
								'gateway' => $result['gateway'],
								'status' => $result['status'],
								'message' => $result['message'],
							];
						}
					]),
					'updateGateway' => Relay::mutationWithClientMutationId([
						'name' => 'updateGateway',
						'description' => _('Update an existing gateway. Fields left out keep their current value.'),
						'inputFields' => $this->getUpdateInputFields(),
						'outputFields' => $this->getMutationOutputFields(),
						'mutateAndGetPayload' => function ($input) {
							$extension = (string)($input['extension'] ?? '');
							$existing = $extension !== '' ? $this->freepbx->Gateway->getGateway($extension) : null;
							if(empty($existing)){
								return ['gateway' => null, 'status' => false, 'message' => _('Gateway not found!')];
							}
							$data = $this->resolveGatewayInput($input, $existing);
							$result = $this->freepbx->Gateway->updateGateway($data);
							return [
								'gateway' => $result['gateway'],
								'status' => $result['status'],
								'message' => $result['message'],
							];
						}
					]),
					'removeGateway' => Relay::mutationWithClientMutationId([
						'name' => 'removeGateway',
						'description' => _('Remove an existing gateway'),
						'inputFields' => [
							'extension' => [
								'type' => Type::nonNull(Type::id()),
								'description' => _('Extension of the gateway to remove'),
							]
						],
						'outputFields' => [
							'deletedId' => [
								'type' => Type::id(),
								'description' => _('Extension of the gateway that was removed'),
								'resolve' => function ($payload) {
									return $payload['extension'];
								}
							],
							'status' => [
								'type' => Type::boolean(),
								'description' => _('Status of the request'),
							],
							'message' => [
								'type' => Type::string(),
								'description' => _('Message for the request'),
							],
						],
						'mutateAndGetPayload' => function ($input) {
							$result = $this->freepbx->Gateway->deleteGateway($input['extension']);
							return [
								'extension' => $input['extension'],
								'status' => $result['status'],
								'message' => $result['message'],
							];
						}
					]),
				];
			};
		}
	}

	/**
	 * queryCallback
	 *
	 * @return callable|void
	 */
	public function queryCallback() {
		if($this->checkReadScope("gateway")) {
			return function() {
				return [
					'allGateways' => [
						'type' => $this->typeContainer->get('gateway')->getConnectionType(),
						'description' => $this->description,
						'args' => Relay::connectionArgs(),
						'resolve' => function($root, $args) {
							return Relay::connectionFromArray($this->freepbx->Gateway->getAllGateway(), $args);
						},
					],
					'gateway' => [
						'type' => $this->typeContainer->get('gateway')->getObject(),
						'description' => _('Fetch a single gateway by extension'),
						'args' => [
							'id' => [
								'type' => Type::nonNull(Type::id()),
								'description' => _('Extension of the gateway'),
							]
						],
						'resolve' => function($root, $args) {
							$row = $this->freepbx->Gateway->getGateway($args['id']);
							return !empty($row) ? $row : null;
						}
					],
				];
			};
		}
	}

	/**
	 * initializeTypes
	 *
	 * @return void
	 */
	public function initializeTypes() {
		$gateway = $this->typeContainer->create('gateway');
		$gateway->setDescription($this->description);

		$gateway->addInterfaceCallback(function() {
			return [$this->getNodeDefinition()['nodeInterface']];
		});

		$gateway->setGetNodeCallback(function($id) {
			$row = $this->freepbx->Gateway->getGateway($id);
			return !empty($row) ? $row : null;
		});

		$gateway->addFieldCallback(function() {
			return [
				'id' => [
					'type' => Type::nonNull(Type::id()),
					'description' => _('Extension used as the gateway id'),
					'resolve' => function($row) {
						return $row['extension'];
					}
				],
				'extension' => [
					'type' => Type::nonNull(Type::string()),
					'description' => _('Extension as Gateway'),
				],
				'contact' => [
					'type' => Type::string(),
					'description' => _('Name of the contact'),
				],
				'description' => [
					'type' => Type::string(),
					'description' => _('Description of the gateway'),
				],
				'address' => [
					'type' => Type::string(),
					'description' => _('Address of the contact'),
				],
				'city' => [
					'type' => Type::string(),
					'description' => _('City of the contact'),
				],
				'zipCode' => [
					'type' => Type::string(),
					'description' => _('ZIP code of the contact'),
					'resolve' => function($row) {
						return $row['zip_code'] ?? '';
					}
				],
				'country' => [
					'type' => Type::string(),
					'description' => _('Country of the contact'),
				],
				'email' => [
					'type' => Type::string(),
					'description' => _('Email of the contact'),
				],
				'gatewayAddress' => [
					'type' => Type::string(),
					'description' => _('IP address (with optional :port) of the remote gateway'),
					'resolve' => function($row) {
						return $row['gateway'] ?? '';
					}
				],
				'accountcode' => [
					'type' => Type::string(),
					'description' => _('Account code applied to calls through this gateway'),
				],
				'callLimit' => [
					'type' => Type::int(),
					'description' => _('Maximum number of simultaneous calls (0 = unlimited)'),
					'resolve' => function($row) {
						return intval($row['call_limit'] ?? 0);
					}
				],
				'dids' => [
					'type' => Type::listOf(Type::string()),
					'description' => _('DIDs attached to this gateway (first one is the DID base)'),
					'resolve' => function($row) {
						$dids = json_decode($row['dids'] ?? '[]', true);
						return is_array($dids) ? array_values($dids) : [];
					}
				],
				'status' => [
					'type' => Type::string(),
					'description' => _('Live PJSIP connectivity status: online, offline or unknown'),
					'resolve' => function($row) {
						return $this->freepbx->Gateway->getEndpointStatus($row['extension']);
					}
				],
			];
		});

		$gateway->setConnectionResolveNode(function ($edge) {
			return $edge['node'];
		});

		$gateway->setConnectionFields(function() {
			return [
				'totalCount' => [
					'type' => Type::int(),
					'resolve' => function($value) {
						return count($this->freepbx->Gateway->getAllGateway());
					}
				],
				'gateways' => [
					'type' => Type::listOf($this->typeContainer->get('gateway')->getObject()),
					'description' => $this->description,
					'resolve' => function($root, $args) {
						return array_map(function($row) {
							return $row['node'];
						}, $root['edges']);
					}
				]
			];
		});
	}

	/**
	 * getMutationOutputFields
	 *
	 * Shared output shape for addGateway/updateGateway mutations.
	 *
	 * @return array
	 */
	private function getMutationOutputFields() {
		return [
			'gateway' => [
				'type' => $this->typeContainer->get('gateway')->getObject(),
				'description' => _('The gateway after the change'),
				'resolve' => function ($payload) {
					return $payload['gateway'];
				}
			],
			'status' => [
				'type' => Type::boolean(),
				'description' => _('Status of the request'),
			],
			'message' => [
				'type' => Type::string(),
				'description' => _('Message for the request'),
			],
		];
	}

	/**
	 * getAddInputFields
	 *
	 * @return array
	 */
	private function getAddInputFields() {
		return [
			'extension' => [
				'type' => Type::nonNull(Type::id()),
				'description' => _('Extension to use as the gateway. Must be an existing PJSIP extension not already used by another gateway.'),
			],
			'contact' => [
				'type' => Type::nonNull(Type::string()),
				'description' => _('Name of the contact'),
			],
			'description' => [
				'type' => Type::string(),
				'description' => _('Description of the gateway'),
			],
			'address' => [
				'type' => Type::string(),
				'description' => _('Address of the contact'),
			],
			'city' => [
				'type' => Type::string(),
				'description' => _('City of the contact'),
			],
			'zip' => [
				'type' => Type::string(),
				'description' => _('ZIP code of the contact (digits only, 3 to 6 characters)'),
			],
			'country' => [
				'type' => Type::string(),
				'description' => _('Country of the contact'),
			],
			'email' => [
				'type' => Type::string(),
				'description' => _('Email of the contact'),
			],
			'gatewayAddress' => [
				'type' => Type::nonNull(Type::string()),
				'description' => _('IPv4 address of the remote gateway, with an optional :port (e.g. 200.25.46.30 or 200.25.46.30:5061)'),
			],
			'accountcode' => [
				'type' => Type::string(),
				'description' => _('Account code applied to calls through this gateway (letters, digits, "_" and "-" only)'),
			],
			'callLimit' => [
				'type' => Type::int(),
				'description' => _('Maximum number of simultaneous calls (0 = unlimited)'),
			],
			'dids' => [
				'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
				'description' => _('DIDs attached to this gateway. The first entry is the DID base (usually the same as the extension).'),
			],
		];
	}

	/**
	 * getUpdateInputFields
	 *
	 * Same as getAddInputFields() but nothing besides "extension" is
	 * required - any field left out keeps its current stored value.
	 *
	 * @return array
	 */
	private function getUpdateInputFields() {
		$fields = $this->getAddInputFields();
		$fields['extension'] = [
			'type' => Type::nonNull(Type::id()),
			'description' => _('Extension of the gateway to update'),
		];
		$fields['gatewayAddress'] = [
			'type' => Type::string(),
			'description' => _('IPv4 address of the remote gateway, with an optional :port (e.g. 200.25.46.30 or 200.25.46.30:5061)'),
		];
		$fields['dids'] = [
			'type' => Type::listOf(Type::nonNull(Type::string())),
			'description' => _('DIDs attached to this gateway. The first entry is the DID base. Omit to keep the current list.'),
		];
		return $fields;
	}

	/**
	 * resolveGatewayInput
	 *
	 * Maps the camelCase GraphQL input onto the internal array shape
	 * expected by Gateway::addGateway()/updateGateway() (itself shared
	 * with the web form), falling back to the existing DB row for any
	 * field omitted from the input - so updateGateway is a real partial
	 * update instead of blanking out everything that wasn't passed.
	 *
	 * @param  array      $input    the GraphQL mutation input
	 * @param  array|null $existing the current DB row (null when adding)
	 * @return array
	 */
	private function resolveGatewayInput($input, $existing) {
		$existing = $existing ?? [];
		$existingDids = isset($existing['dids']) ? json_decode($existing['dids'], true) : [];
		$existingDids = is_array($existingDids) ? $existingDids : [];

		return [
			'extension'   => $input['extension'] ?? ($existing['extension'] ?? ''),
			'contact'     => $input['contact'] ?? ($existing['contact'] ?? ''),
			'description' => $input['description'] ?? ($existing['description'] ?? ''),
			'address'     => $input['address'] ?? ($existing['address'] ?? ''),
			'city'        => $input['city'] ?? ($existing['city'] ?? ''),
			'zip'         => $input['zip'] ?? ($existing['zip_code'] ?? ''),
			'country'     => $input['country'] ?? ($existing['country'] ?? ''),
			'email'       => $input['email'] ?? ($existing['email'] ?? ''),
			'gateway'     => $input['gatewayAddress'] ?? ($existing['gateway'] ?? ''),
			'accountcode' => $input['accountcode'] ?? ($existing['accountcode'] ?? ''),
			'call_limit'  => $input['callLimit'] ?? ($existing['call_limit'] ?? 0),
			'dids'        => $input['dids'] ?? $existingDids,
		];
	}
}
