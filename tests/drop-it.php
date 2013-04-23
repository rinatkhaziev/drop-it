<?php
/**
 *
 * Test case for Drop It
 *
 */
// Define plugin's root
define( 'DROP_IT_ROOT' , dirname( dirname( __FILE__ ) ) );

require_once DROP_IT_ROOT . 'drop-it.php';
class Drop_It_UnitTestCase extends WP_UnitTestCase {
	public $di;

	/**
	 * Init
	 * @return [type] [description]
	 */
	function setup() {
		global $drop_it;
		$this->di = $drop_it;
		parent::setup();
	}

	function teardown() {
	}

	// Check if settings get set up on activation
	function test_default_settings() {
		$this->assertNotEmpty( $this->di->settings );
	}

	function test_available_drops() {

	}

	// Check if errors are handled properly
	function test_error_handling() {

	}

}