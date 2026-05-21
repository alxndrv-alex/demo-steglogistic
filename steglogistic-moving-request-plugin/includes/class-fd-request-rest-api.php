<?php

class FD_Request_Rest_API extends WP_REST_Controller {
	public function __construct() {
		$this->namespace = 'fd-api/v1';
		$this->rest_base = 'task';
	}

	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			"/$this->rest_base/create",
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'name'                 => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'email'                => array(
							'type'     => 'string',
							'required' => true,
							'format'   => 'email',
							'callback' => 'is_email',
						),
						'phone'                => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'termsOfServiceAgreed' => array(
							'type'     => 'boolean',
							'required' => true,
							'callback' => 'is_bool',
						),
						'optIn'                => array(
							'type'     => 'boolean',
							'required' => false,
							'callback' => 'is_bool',
						),
					),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/create",
			array(
				array(
					'methods'             => 'PUT',
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'taskId'          => array(
							'type'     => 'integer',
							'required' => true,
							'callback' => 'is_int',
						),
						'taskKey'         => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'furnitureAmount' => array(
							'type'     => 'string',
							'required' => false,
							'enum'     => array( 'Small', 'Medium', 'Large' ),
							'default'  => 'Small',
							'callback' => 'is_string',
						),
						'storageUnit'     => array(
							'type'     => 'boolean',
							'required' => false,
							'default'  => true,
							'callback' => 'is_bool',
						),
						'movingComment'   => array(
							'type'     => 'string',
							'required' => false,
							'callback' => 'is_string',
						),
						'jobDate'         => array(
							'type'     => 'string',
							'required' => false,
							'pattern'  => '(\d){4}-(\d){2}-(\d){2}',
							'callback' => 'is_string',
						),
						'fromAddress'     => array(
							'type'       => 'object',
							'required'   => true,
							'properties' => array(
								'postCode'        => array(
									'type'      => 'string',
									'required'  => true,
									'callback'  => 'is_string',
									'minLength' => 5,
									'maxLength' => 5,
								),
								'country'         => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'state'           => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'city'            => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'customPart'      => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'port'            => array(
									'type'     => 'string',
									'required' => false,
								),
								'size'            => array(
									'type'     => 'integer',
									'required' => true,
									'callback' => 'is_int',
								),
								'floor'           => array(
									'type'     => 'integer',
									'required' => true,
									'callback' => 'is_int',
								),
								'elevator'        => array(
									'type'     => 'string',
									'required' => true,
									'enum'     => array( 'NoElevator', 'Two', 'Four', 'Six', 'Eight' ),
								),
								'loadingDistance' => array(
									'type'     => 'string',
									'required' => true,
									'enum'     => array( 'm0_10', 'm10_20', 'm20_30', 'm30_50', 'm50_' ),
								),
								'type'            => array(
									'type'     => 'string',
									'required' => true,
									'enum'     => array(
										'Unknown',
										'Apartment',
										'TerracedHouse',
										'House',
										'Storehouse',
									),
								),
							),
						),
						'toAddress'       => array(
							'type'        => 'object',
							'required'    => false,
							'description' => 'Not used when jobType = 0',
							'properties'  => array(
								'postCode'        => array(
									'type'      => 'string',
									'required'  => true,
									'callback'  => 'is_string',
									'minLength' => 5,
									'maxLength' => 5,
								),
								'country'         => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'state'           => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'city'            => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'customPart'      => array(
									'type'     => 'string',
									'required' => true,
									'callback' => 'is_string',
								),
								'port'            => array(
									'type'     => 'string',
									'required' => false,
									'callback' => 'is_string',
								),
								'size'            => array(
									'type'     => 'integer',
									'required' => true,
									'callback' => 'is_int',
								),
								'floor'           => array(
									'type'     => 'integer',
									'required' => true,
									'callback' => 'is_int',
								),
								'elevator'        => array(
									'type'     => 'string',
									'required' => true,
									'enum'     => array( 'NoElevator', 'Two', 'Four', 'Six', 'Eight' ),
								),
								'loadingDistance' => array(
									'type'     => 'string',
									'required' => true,
									'enum'     => array( 'm0_10', 'm10_20', 'm20_30', 'm30_50', 'm50_' ),
								),
								'type'            => array(
									'type'     => 'string',
									'required' => true,
									'enum'     => array(
										'Unknown',
										'Apartment',
										'TerracedHouse',
										'House',
										'Storehouse',
									),
								),
							),
						),
						'cleaning'        => array(
							'type'     => 'boolean',
							'required' => false,
							'default'  => false,
							'callback' => 'is_bool',
						),
						'cleaningJobDate' => array(
							'type'     => 'string',
							'required' => false,
							'pattern'  => '(\d){4}-(\d){2}-(\d){2}',
							'callback' => 'is_string',
						),
						'cleaningComment' => array(
							'type'     => 'string',
							'required' => false,
							'callback' => 'is_string',
						),
						'extraServices'   => array(
							'type'     => 'array',
							'required' => false,
							'items'    => array(
								'type'       => 'object',
								'properties' => array(
									'extraServiceType' => array(
										'type'     => 'string',
										'required' => true,
										'enum'     => array( 'Assembling', 'Disassembling', 'Packing', 'Unpacking' ),
									),
								),
							),
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/accept-price",
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'accept_price' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'taskKey'        => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'name'           => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'email'          => array(
							'type'     => 'string',
							'required' => true,
							'format'   => 'email',
							'callback' => 'is_string',
						),
						'phone'          => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'invoiceAddress' => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'offerPrice'     => array(
							'type'     => 'number',
							'required' => true,
							'callback' => 'is_numeric',
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/feedback",
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'feedback' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'taskKey'                 => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'priceFeedback'           => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => array( 'Empty', 'VeryPoor', 'Poor', 'Average', 'Good', 'Excellent' ),
						),
						'customerServiceFeedback' => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => array( 'Empty', 'VeryPoor', 'Poor', 'Average', 'Good', 'Excellent' ),
						),
						'jobServiceFeedback'      => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => array( 'Empty', 'VeryPoor', 'Poor', 'Average', 'Good', 'Excellent' ),
						),
						'feedbackText'            => array(
							'type'     => 'string',
							'required' => false,
							'callback' => 'is_string',
						),
					),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/complain",
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'complain' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'taskKey'                 => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'priceFeedback'           => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => array( 'Empty', 'VeryPoor', 'Poor', 'Average', 'Good', 'Excellent' ),
						),
						'customerServiceFeedback' => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => array( 'Empty', 'VeryPoor', 'Poor', 'Average', 'Good', 'Excellent' ),
						),
						'jobServiceFeedback'      => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => array( 'Empty', 'VeryPoor', 'Poor', 'Average', 'Good', 'Excellent' ),
						),
						'complaint'               => array(
							'type'     => 'string',
							'required' => false,
							'callback' => 'is_string',
						),
						'files'                   => array(
							'type'     => 'object',
							'required' => false,
							'callback' => 'is_object',
						),
					),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/sign",
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'sign_task' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'taskKey'     => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'device'      => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => array( 'SameDevice', 'OtherDevice' ),
						),
						'redirectUrl' => array(
							'type'     => 'string',
							'format'   => 'uri',
							'required' => false,
							'callback' => 'is_string',
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/check-sign-status",
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'check_sign_status' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'signerName'     => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'taskKey'        => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'protectedRefId' => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/update-sign-qr",
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_sign_qr' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'taskKey'        => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
						'protectedRefId' => array(
							'type'     => 'string',
							'required' => true,
							'callback' => 'is_string',
						),
					),
				),
			)
		);
	}

	// Actions

	public function create_item( $request ): WP_REST_Response {
		$task = $request->get_body();

		return FD_Moving_Request::create_request( $task );
	}

	public function update_item( $request ): WP_REST_Response {
		$task = $request->get_body();

		return FD_Moving_Request::update_request( $task );
	}

	public function accept_price( $request ): WP_REST_Response|WP_Error {
		$args     = $request->get_body();
		$response = FD_Moving_Request::accept_price( $args );

		return rest_ensure_response( $response );
	}

	public function feedback( WP_REST_Request $request ): WP_REST_Response {
		$args = $request->get_body();

		return FD_Moving_Request::feedback( $args );
	}

	public function complain( $request ): WP_REST_Response {
		$args = $request->get_body();

		return FD_Moving_Request::complain( $args );
	}

	public function sign_task( $request ): WP_REST_Response {
		$args = $request->get_body();

		return FD_Moving_Request::sign_task( $args );
	}

	public function check_sign_status( $request ): WP_REST_Response {
		$args = $request->get_body();

		return FD_Moving_Request::check_sign_status( $args );
	}

	public function update_sign_qr( $request ): WP_REST_Response {
		$args = $request->get_body();

		return FD_Moving_Request::update_sign_qr( $args );
	}
}
