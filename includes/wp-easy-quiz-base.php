<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

class wp_easy_quiz_base {
	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name;
	/**
	 * Plugin version.
	 *
	 * @var int
	 */
	protected $plugin_version;
	/**
	 * Basic constructor. Invoke hooks.
	 */
	public function __construct() {
		$this->plugin_name    = 'wpeasyquiz';
		$this->plugin_version = '1.0';

		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Include the required files
	 */
	public function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wp-easy-quiz-admin.php';
	}

	/**
	 * Initializing admin hooks
	 */
	private function define_admin_hooks() {
		$plugin_admin = new wp_easy_quiz_quiz_admin( $this->plugin_name, $this->plugin_version );
	}
}