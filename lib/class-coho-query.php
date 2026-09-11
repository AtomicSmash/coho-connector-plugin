<?php
/**
 * COHO SDK
 */

namespace AtomicSmash\CohoConnector;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if class has already been loaded
if ( ! class_exists( __NAMESPACE__ . '\COHO_Query' ) ) {

	/**
	 * COHO SDK
	 */
	class COHO_Query {

		/**
		 * The base URL for connecting to COHO.
		 *
		 * @var string The base URL for connecting to COHO.
		 */
		private string $base_url = 'https://api.coho.life';

		/**
		 * The API version to use.
		 *
		 * @var string The API version to use.
		 */
		private string $api_version = 'v1.1/public';

		/**
		 * The API key for authenticating with COHO.
		 *
		 * @var string The API key for authenticating with COHO.
		 */
		private string $api_key;

		/**
		 * Constructor.
		 *
		 * @param ?string $api_key The API key for authenticating with COHO.
		 *
		 * @throws RuntimeException If the API key is not set.
		 */
		public function __construct( ?string $api_key = null ) {
			if ( null === $api_key ) {
				if ( defined( 'COHO_API' ) ) {
					$this->api_key = COHO_API;
				} else {
					// Fallback: try environment variable
					$env_key = getenv( 'COHO_API' );
					if ( ! $env_key ) {
							throw new RuntimeException( 'COHO_API key not set.' );
					}
					$this->api_key = $env_key;
				}
			} else {
					$this->api_key = $api_key;
			}
		}

		/**
		 * Base method for making arbitrary requests to COHO.
		 *
		 * @param string              $method The request method e.g. GET|POST.
		 * @param string              $endpoint The endpoint to connect to in the API.
		 * @param array<string,mixed> $params Parameters for the request for adding to the URL for GET requests or adding to the request body for all other methods.
		 *
		 * @return array<string,mixed> The returned JSON response.
		 *
		 * @throws RuntimeException If there's an HTTP request error.
		 * @throws RuntimeException If the JSON returned from COHO is invalid.
		 */
		public function request( string $method, string $endpoint, array $params = array() ): array {
			$method = strtoupper( $method );
			$url    = rtrim( $this->base_url, '/' ) . '/' . ltrim( $endpoint, '/' );

			// Attach query parameters for GET
			if ( 'GET' === $method && ! empty( $params ) ) {
				$url .= '?' . http_build_query( $params );
			}

			$args = array(
				'method'  => $method,
				'headers' => array(
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->api_key,
				),
			);

			if ( 'GET' !== $method && ! empty( $params ) ) {
				$args['body'] = wp_json_encode( $params );
			}

			$response = wp_remote_request( $url, $args );

			if ( is_wp_error( $response ) ) {
				throw new RuntimeException( 'HTTP request error: ' . esc_html( $response->get_error_message() ) );
			}

			$response_body = wp_remote_retrieve_body( $response );
			$http_code     = wp_remote_retrieve_response_code( $response );

			$decoded = json_decode( $response_body, true );
			if ( null === $decoded && json_last_error() !== JSON_ERROR_NONE ) {
				throw new RuntimeException( 'Invalid JSON response from COHO API. HTTP code: ' . esc_html( (string) $http_code ) );
			}

			return array(
				'status'   => $http_code,
				'data'     => $decoded,
				'raw_body' => $response_body,
			);
		}

		/**
		 * Shorthand for GET requests
		 *
		 * @param string              $endpoint The endpoint to connect to in the API.
		 * @param array<string,mixed> $params Parameters for the request for adding to the URL.
		 *
		 * @return array<string,mixed> The returned JSON response.
		 */
		public function get( string $endpoint, array $params = array() ): array {
			return $this->request( 'GET', $endpoint, $params );
		}

		/**
		 * Shorthand for POST requests
		 *
		 * @param string              $endpoint The endpoint to connect to in the API.
		 * @param array<string,mixed> $params Parameters for the request for adding to the request body.
		 *
		 * @return array<string,mixed> The returned JSON response.
		 */
		public function post( string $endpoint, array $params = array() ): array {
			return $this->request( 'POST', $endpoint, $params );
		}

		/**
		 * Test authentication
		 *
		 * @return bool Whether the auth works or not.
		 */
		public function test_auth(): bool {
			return '200' === $this->request( 'GET', '/' . $this->api_version . '/test-auth' )['status'];
		}

		/**
		 * Get properties
		 *
		 * @param array<string,mixed> $params Parameters for the request.
		 *
		 * @return array<string,mixed> The returned JSON response.
		 */
		public function get_properties( array $params = array() ): array {
			return $this->request( 'GET', '/' . $this->api_version . '/properties', $params );
		}

		/**
		 * Get single property
		 *
		 * @param string              $property_reference The property reference for the property you want to get.
		 * @param array<string,mixed> $params Parameters for the request.
		 *
		 * @return array<string,mixed> The returned JSON response.
		 */
		public function get_property( string $property_reference, array $params = array() ): array {
			return $this->request( 'GET', '/' . $this->api_version . '/properties/' . $property_reference, $params );
		}

		/**
		 * Get tenancies
		 *
		 * @param array<string,mixed> $params Parameters for the request.
		 *
		 * @return array<string,mixed> The returned JSON response.
		 */
		public function get_tenancies( array $params = array() ): array {
			return $this->request( 'GET', '/' . $this->api_version . '/tenancies', $params );
		}

		/**
		 * Get single tenancy
		 *
		 * @param string              $tenancy_reference The property reference for the tenancy you want to get.
		 * @param array<string,mixed> $params Parameters for the request.
		 *
		 * @return array<string,mixed> The returned JSON response.
		 */
		public function get_tenancy( string $tenancy_reference, array $params = array() ): array {
			return $this->request( 'GET', '/' . $this->api_version . '/tenancies/' . $tenancy_reference, $params );
		}
	}
}
