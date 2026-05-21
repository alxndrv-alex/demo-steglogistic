<?php
class FD_REST_Response {
	private static string $unexpected_error_text = 'Unexpected error occurred. Most likely, no connection to the back-end API. Our team is already informed about this incident. Please try again later.';
	
	public static function get_response( object $data ): WP_REST_Response {
		$response   = new stdClass();
		$stats_code = $data->status_code ?? $data->data->status_code ?? false;
		
		if ( 200 !== intval( $stats_code ) || is_wp_error( $data ) ) {
			$response->success = false;

			if ( is_wp_error( $data ) ) {
				$response->error = self::$unexpected_error_text;
			} elseif ( ! empty( $data->errors ) ) {
				$response->error = self::get_error_text( $data->errors );
			} else {
				$response->error = $data->title;
			}
		} else {
			$response->success = true;
			$response = (object) array_merge( (array) $response, (array) $data );
			
			if ( isset( $response->taskKey ) ) {
				$response->task_key = $response->taskKey;
				unset( $response->taskKey );
			}
			
			if ( isset( $response->taskId ) ) {
				$response->task_id = $response->taskId;
				unset( $response->taskId );
			}
		}
		
		return new WP_REST_Response( $response, $stats_code );
	}
	
	private static function get_error_text( array|object $errors ): string {
		if ( is_object( $errors ) ) {
			$errors = array_values(get_object_vars( $errors ) );
		}
		
		if ( count( $errors ) > 1 ) {
			$error_text = "\n\n";
			
			foreach ( $errors as $k => $error ) {
				if ( is_array( $error ) ) {
					$error_text .= $k + 1 . ': ' . $error[0] . "\n\n";
				} else {
					$error_text .= $k + 1 . ': ' . $error . "\n\n";
				}
			}
		} else {
			$error_text = $errors[0][0];
		}
		
		return $error_text;
	}
}