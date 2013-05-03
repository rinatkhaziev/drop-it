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
		parent::setup();
		global $drop_it;
		$this->di = $drop_it;
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

	function test_create_drop() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'di-layout' ) );
		$payload = (object) array( 'type' => 'static_html', 'content' => 'test', 'post_id' => $post_id );
		$this->assertInternalType( 'int', $this->di->create_drop( $payload ) );
	}

	// Check if errors are handled properly
	function test_error_handling() {

	}
}

