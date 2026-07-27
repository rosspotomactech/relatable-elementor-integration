<?php
/**
 * Custom Elementor Form Action Base Class for Relatable CRM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Relatable_Elementor_Form_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	private $api_base_url = 'https://api.relatable.one/api/v1';

	public function get_name() {
		return 'relatable_crm';
	}

	public function get_label() {
		return esc_html__( 'Relatable CRM', 'relatable-elementor' );
	}

	/**
	 * Register Form Controls in Elementor Builder
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_relatable',
			[
				'label'     => esc_html__( 'Relatable CRM Field Mapping', 'relatable-elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'relatable_email_field',
			[
				'label'       => esc_html__( 'Email Field ID', 'relatable-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Mandatory: Standard Elementor Form Field ID for Email (used to search contacts).', 'relatable-elementor' ),
			]
		);

		$widget->add_control(
			'relatable_first_name_field',
			[
				'label' => esc_html__( 'First Name Field ID', 'relatable-elementor' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'relatable_last_name_field',
			[
				'label' => esc_html__( 'Last Name Field ID', 'relatable-elementor' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'relatable_phone_field',
			[
				'label' => esc_html__( 'Phone Field ID', 'relatable-elementor' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'relatable_location_field',
			[
				'label' => esc_html__( 'Location Field ID', 'relatable-elementor' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->add_control(
			'relatable_company_field',
			[
				'label' => esc_html__( 'Company Field ID', 'relatable-elementor' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * Execute submission logic upon form submit
	 */
	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );
		$api_key  = get_option( 'relatable_api_key' );

		if ( empty( $api_key ) ) {
			return;
		}

		$fields = $record->get( 'fields' );

		// Helper function to extract field values by Field ID
		$get_field_val = function( $setting_key ) use ( $settings, $fields ) {
			if ( empty( $settings[ $setting_key ] ) ) {
				return '';
			}
			$field_id = $settings[ $setting_key ];
			return isset( $fields[ $field_id ]['value'] ) ? sanitize_text_field( $fields[ $field_id ]['value'] ) : '';
		};

		$email = sanitize_email( $get_field_val( 'relatable_email_field' ) );
		if ( empty( $email ) ) {
			return;
		}

		$first_name = $get_field_val( 'relatable_first_name_field' );
		$last_name  = $get_field_val( 'relatable_last_name_field' );
		$phone      = $get_field_val( 'relatable_phone_field' );
		$location   = $get_field_val( 'relatable_location_field' );
		$company    = $get_field_val( 'relatable_company_field' );

		// 1. Search for existing person by email
		$existing_person_id = $this->find_person_id_by_email( $email, $api_key );

		// 2. Prepare Payload matching Relatable schema
		$payload = [
			'first_name'      => $first_name,
			'last_name'       => $last_name,
			'full_name'       => trim( "{$first_name} {$last_name}" ),
			'location'        => $location,
			'email_addresses' => [
				[
					'name'  => 'Work',
					'value' => $email,
				],
			],
		];

		if ( ! empty( $phone ) ) {
			$payload['phone_numbers'] = [
				[
					'name'  => 'Work',
					'value' => $phone,
				],
			];
		}

		if ( ! empty( $company ) ) {
			$payload['companies'] = [
				[
					'title' => $company,
				],
			];
		}

		// 3. Determine endpoint and HTTP method based on upsert status
		if ( $existing_person_id ) {
			// Update existing contact endpoint: PUT /people/{id}/api_update
			$endpoint = "{$this->api_base_url}/people/{$existing_person_id}/api_update";
			$method   = 'PUT';
		} else {
			// Create new contact endpoint: POST /people
			$endpoint = "{$this->api_base_url}/people";
			$method   = 'POST';
		}

		// 4. Send Remote API request
		wp_remote_request(
			$endpoint,
			[
				'method'  => $method,
				'headers' => [
					'Api-Key'      => $api_key,
					'Content-Type' => 'application/json',
				],
				'body'    => wp_json_encode( $payload ),
				'timeout' => 15,
			]
		);
	}

	/**
	 * Query Relatable API to check if a person exists by email
	 */
	private function find_person_id_by_email( $email, $api_key ) {
		$url = add_query_arg(
			[ 'query' => $email ],
			"{$this->api_base_url}/people"
		);

		$response = wp_remote_get(
			$url,
			[
				'headers' => [
					'Api-Key'      => $api_key,
					'Content-Type' => 'application/json',
				],
				'timeout' => 15,
			]
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! empty( $data ) && is_array( $data ) ) {
			$people = isset( $data['people'] ) ? $data['people'] : ( isset( $data[0] ) ? $data : [] );

			foreach ( $people as $person ) {
				if ( ! empty( $person['email_addresses'] ) && is_array( $person['email_addresses'] ) ) {
					foreach ( $person['email_addresses'] as $email_entry ) {
						if ( isset( $email_entry['value'] ) && strtolower( trim( $email_entry['value'] ) ) === strtolower( trim( $email ) ) ) {
							return $person['id'];
						}
					}
				}
			}
		}

		return false;
	}

	public function on_export( $element ) {
		unset(
			$element['settings']['relatable_email_field'],
			$element['settings']['relatable_first_name_field'],
			$element['settings']['relatable_last_name_field'],
			$element['settings']['relatable_phone_field'],
			$element['settings']['relatable_location_field'],
			$element['settings']['relatable_company_field']
		);
		return $element;
	}
}