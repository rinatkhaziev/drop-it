<?php
/**
 *
 * Test case for Drop It
 *
 */
class Drop_It_UnitTestCase extends WP_UnitTestCase {
	public $di;

	/**
	 * Init
	 * @return [type] [description]
	 */
	function setup() {
		$this->di = new Drop_It;
		$this->di->register_drops();
		parent::setup();
	}

	function teardown() {
	}

	// Check if settings get set up on activation
	function test_default_settings() {
		$this->assertNotEmpty( $this->di->settings );
	}

	function test_available_drops() {
		$this->assertNotEmpty( $this->di->drops );
	}

	// Check if errors are handled properly
	function test_error_handling() {

	}

}