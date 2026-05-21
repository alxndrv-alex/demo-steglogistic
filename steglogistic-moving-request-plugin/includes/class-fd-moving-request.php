<?php
// phpcs:disable WordPress.NamingConventions.ValidVariableName

class FD_Moving_Request {
	private static string $API_HOST_PROD    = 'https://api.steg-logistic.se';
	private static string $API_HOST_DEV     = 'https://dev-api-steglogistic.fastdev-hosting.pro';
	private static string $API_HOST_STAGE   = 'https://stage-api-steglogistic.fastdev-hosting.pro';
	private static string $API_Endpoint_URL = '/api/customer/tasks/';
	private static string $post_type        = 'moving-request';

	// PUBLIC

	public static function create_request( string $request ): object {
		$request               = json_decode( $request );
		$request->RutDeduction = true;
		$method                = 'post';
		$result                = self::request( $method, $request );
		$response              = FD_REST_Response::get_response( $result );
		$data                  = $response->get_data();

		if ( $data->success ) {
			$data = (object) array_merge( (array) $request, (array) $data );
			self::create_post( $data );
		}

		return $response;
	}

	public static function update_request( string $request ): object {
		$request               = json_decode( $request );
		$request->RutDeduction = true;
		$method                = 'put';
		$result                = self::request( $method, $request );
		$response              = FD_REST_Response::get_response( $result );

		if ( $response->data->success ) {
			self::update_post( $request );
		}

		return $response;
	}

	public static function get_price( string $task_key ): object {
		$price = self::get_price_data( $task_key );

		if ( ! self::is_error( $price ) ) {
			$obj           = new stdClass();
			$obj->task_id  = self::get_task_id( $task_key );
			$obj->task_key = $task_key;
			$obj->data     = $price;
			$price         = $obj;
		}

		return FD_REST_Response::get_response( $price );
	}

	public static function accept_price( string $request ): object {
		$request = json_decode( $request );
		$method  = 'post';
		$result  = self::request( $method, $request, 'accept-price/' );

		return FD_REST_Response::get_response( $result );
	}

	public static function feedback( string $request ): object {
		$request = json_decode( $request );

		$method = 'post';
		$result = self::request( $method, $request, 'feedback/' );

		return FD_REST_Response::get_response( $result );
	}

	public static function complain( string $request ): object {
		$request = json_decode( $request );
		$method  = 'post';
		$result  = self::request( $method, $request, 'complain/' );

		return FD_REST_Response::get_response( $result );
	}

	public static function sign_task( string $request ): object {
		$request      = json_decode( $request );
		$method       = 'post';
		$price        = self::get_price_data( $request->taskKey );
		$signing_data = '';

		if ( ! self::is_error( $price ) ) {
			if ( isset( $price->offerPrice ) ) {
				$formattedOfferPrice = number_format( $price->offerPrice, 0, '', ' ' );
			} else {
				$formattedOfferPrice = '...';
			}
			if ( ! empty( $price->taxDeduction ) ) {
				$signing_data .= "Jag bekräftar härmed att jag godkänner den offert jag mottagit från Steg Logistic AB, med ett totalpris på {$formattedOfferPrice} kr efter RUT-avdrag.\r\n\r\n";
			} else {
				$signing_data .= "Jag bekräftar härmed att jag godkänner den offert jag mottagit från Steg Logistic AB, med ett totalpris på {$formattedOfferPrice} kr.\r\n\r\n";
			}

			$signing_data .= "Jag intygar att de angivna uppgifterna är korrekta samt att jag har tagit del av och godkänner Steg Logistic AB:s allmänna villkor.\r\n\r\n";
			$signing_data .= "Genom att signera med BankID bekräftar jag min bokning.\r\n";

			$request->signingData = base64_encode( $signing_data );

			$request->endUserInfo = $_SERVER['REMOTE_ADDR'];
			$request->redirectUrl = $request->redirectUrl ?? '';
		}

		$result = self::request( $method, $request, 'init-bank-id-sign/' );

		return FD_REST_Response::get_response( $result );
	}


	public static function check_sign_status( string $request ): object {
		$request = json_decode( $request );
		$method  = 'post';
		$auth    = self::authenticate();
		$result  = self::request( $method, $request, 'check-bank-id-sign-status/', $auth->token );

		return FD_REST_Response::get_response( $result );
	}

	public static function update_sign_qr( string $request ): object {
		$request = json_decode( $request );
		$method  = 'post';
		$auth    = self::authenticate();
		$result  = self::request( $method, $request, 'refresh-bank-id-qr-code/', $auth->token );

		return FD_REST_Response::get_response( $result );
	}

	// PRIVATE

	private static function request( string $method, object|false $args = false, string|false $endpoint_suffix = false, string|bool $token = false, bool $is_customer_task = true ): mixed {

		if ( $is_customer_task ) {
			$endpoint = self::get_api_domain() . ( $endpoint_suffix ? self::$API_Endpoint_URL . $endpoint_suffix : self::$API_Endpoint_URL );
		} else {
			$endpoint = self::get_api_domain() . ( $endpoint_suffix ?? '' );
		}

		$request = array();

		if ( 'GET' === strtoupper( $method ) && ! empty( $args ) ) {
			$endpoint .= '?' . http_build_query( (array) $args );
		} else {
			$request = array(
				'method'  => strtoupper( $method ),
				'timeout' => 600,
				'headers' => array(
					'Cache-Control' => 'no-cache',
					'Content-type'  => 'application/json',
				),
			);

			if ( $token ) {
				$request['headers']['Authorization'] = 'Bearer ' . $token;
			}

			if ( $args ) {
				$request['body'] = json_encode( $args );
			}
		}

		$data = wp_remote_request( $endpoint, $request );

		if ( ! is_wp_error( $data ) ) {
			$body = json_decode( $data['body'] );

			if ( ! is_object( $body ) ) {
				$body = new stdClass();
			}

			$body->status_code = $data['response']['code'];
		}

		if ( is_wp_error( $data ) || 200 !== $data['response']['code'] ) {
			fd_log( 'Method: ' . $method, 'api-error' );
			fd_log( 'Endpoint URL: ' . $endpoint, 'api-error' );
			fd_log( 'Request:', 'api-error' );
			fd_log( $request, 'api-error' );
			fd_log( $data, 'api-error' );
		}

		return ( is_wp_error( $data ) ? $data : $body );
	}

	private static function create_post( object $data ): void {
		$args = array(
			'post_title'  => $data->name,
			'post_type'   => self::$post_type,
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post( $args );

		if ( ! is_wp_error( $post_id ) ) {
			acf_update_metadata( $post_id, 'task_id', $data->task_id );
			acf_update_metadata( $post_id, 'task_key', $data->task_key );

			acf_update_metadata( $post_id, 'email', $data->email );
			acf_update_metadata( $post_id, 'phone', $data->phone );
			acf_update_metadata( $post_id, 'agreed_to_terms_of_service', intval( $data->termsOfServiceAgreed ) );
			acf_update_metadata( $post_id, 'opted_in', intval( $data->optIn ) );
		}
	}

	private static function update_post( object $data ): void {
		$post_id = self::get_existing_post_id( $data->taskId, $data->taskKey );

		if ( $post_id ) {
			$date  = strtotime( $data->jobDate );
			$from  = array(
				'postal_code'      => $data->fromAddress->postCode,
				'country'          => $data->fromAddress->country,
				'state'            => $data->fromAddress->state,
				'city'             => $data->fromAddress->city,
				'custom_part'      => $data->fromAddress->customPart,
				'port'             => $data->fromAddress->port,
				'size'             => $data->fromAddress->size,
				'floor'            => $data->fromAddress->floor,
				'elevator'         => $data->fromAddress->elevator,
				'loading_distance' => $data->fromAddress->loadingDistance,
				'type'             => $data->fromAddress->type,
			);
			$to    = array(
				'postal_code'      => $data->toAddress->postCode,
				'country'          => $data->toAddress->country,
				'state'            => $data->toAddress->state,
				'city'             => $data->toAddress->city,
				'custom_part'      => $data->toAddress->customPart,
				'port'             => $data->toAddress->port,
				'size'             => $data->toAddress->size,
				'floor'            => $data->toAddress->floor,
				'elevator'         => $data->toAddress->elevator,
				'loading_distance' => $data->toAddress->loadingDistance,
				'type'             => $data->toAddress->type,
			);
			$extra = array();

			foreach ( $data->extraServices as $service ) {
				$extra[ strtolower( $service->extraServiceType ) ] = true;
			}

			acf_update_metadata( $post_id, 'furniture_amount', $data->furnitureAmount );
			acf_update_metadata( $post_id, 'storage_unit_needed', $data->storageUnit ?? false );
			acf_update_metadata( $post_id, 'moving_comment', $data->MovingComment ?? '' );
			acf_update_metadata( $post_id, 'date', gmdate( 'Ymd', $date ) );

			update_field( 'from', $from, $post_id );
			update_field( 'to', $to, $post_id );

			acf_update_metadata( $post_id, 'cleaning', $data->cleaning ?? false );

			if ( $data->cleaning ) {
				acf_update_metadata( $post_id, 'cleaning_date', $data->cleaningJobDate );
			}

			update_field( 'extra_services', $extra, $post_id );
		}
	}

	private static function get_existing_post_id( int $task_id, string $task_key ): int|false {
		$args  = array(
			'post_type'      => self::$post_type,
			'post_status'    => array( 'publish', 'pending', 'draft' ),
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => 'task_key',
					'value' => $task_key,
				),
				array(
					'key'   => 'task_id',
					'value' => $task_id,
				),
				'relation' => 'AND',
			),
		);
		$posts = new WP_Query( $args );

		if ( $posts->have_posts() ) {
			return $posts->posts[0]->ID;
		} else {
			return false;
		}
	}

	private static function get_api_domain(): string {
		return match ( ENV_TYPE ) {
			'prod'  => self::$API_HOST_PROD,
			'stage' => self::$API_HOST_STAGE,
			'dev'   => self::$API_HOST_DEV,
		};
	}

	private static function get_price_data( string $task_key ): object {
		$args          = new stdClass();
		$task_key      = sanitize_text_field( $task_key );
		$args->taskKey = $task_key;

		return self::request( 'get', $args, 'accept-price/' );
	}

	private static function is_error( object $data ): bool {
		return ( 200 !== intval( $data->status_code ) || is_wp_error( $data ) );
	}

	// MISC

	public static function get_task_id( string $task_key ): int|false {
		$args          = new stdClass();
		$args->taskKey = $task_key;
		$result        = self::request( 'post', $args, 'validate/' );

		return ( 200 === $result->status_code ? $result->taskId : false );
	}

	// Temporary methods to set task price and pending status

	private static function tmp_request( string $method, string $endpoint_suffix, object|false $args = false, string|false $token = false ): mixed {
		$API_Endpoint_URL = 'https://dev-api-steglogistic.fastdev-hosting.pro/api/';

		$endpoint = $API_Endpoint_URL . $endpoint_suffix;
		$request  = array();

		if ( 'GET' === strtoupper( $method ) && ! empty( $args ) ) {
			$endpoint .= '?' . http_build_query( (array) $args );
		} else {
			$request = array(
				'method'  => strtoupper( $method ),
				'timeout' => 600,
				'headers' => array(
					'Cache-Control' => 'no-cache',
					'Content-type'  => 'application/json',
				),
			);

			if ( $token ) {
				$request['headers']['Authorization'] = 'Bearer ' . $token;
			}

			if ( $args ) {
				$request['body'] = json_encode( $args );
			}
		}

		$data = wp_remote_request( $endpoint, $request );

		if ( is_wp_error( $data ) || 200 !== $data['response']['code'] ) {
			fd_log( 'Method: ' . $method, 'api-error' );
			fd_log( 'Endpoint URL: ' . $endpoint, 'api-error' );
			fd_log( 'Request:', 'api-error' );
			fd_log( $request, 'api-error' );
			fd_log( $data, 'api-error' );
		}

		$body = json_decode( $data['body'] );

		if ( ! is_object( $body ) ) {
			$body = new stdClass();
		}

		$body->status_code = $data['response']['code'];

		return ( ! is_wp_error( $data ) ? $body : false );
	}

	private static function authenticate(): object {
		$args           = new stdClass();
		$args->email    = FD_SMR_PLUGIN_API_EMAIL ?? '';
		$args->password = FD_SMR_PLUGIN_API_PASSWORD ?? '';

		return self::request( 'post', $args, '/api/account/authenticate', is_customer_task: false );
	}
}
