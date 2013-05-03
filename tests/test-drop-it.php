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
		foreach( $this->di->drops as $drop_slug => $drop_instance ) {
			$this->assertInstanceOf( 'Drop_It_Drop', $drop_instance );
		}
	}

	function test_create_drop() {
		// Test successful creation
		$post_id = $this->factory->post->create( array( 'post_type' => 'di-layout' ) );
		$payload = (object) array( 'type' => 'static_html', 'content' => 'test', 'post_id' => $post_id );
		$drop_result = $this->di->create_drop( $payload );
		$this->assertInternalType( 'int', $drop_result );
		$this->assertGreaterThan( 0, $drop_result );

		// Test unexpected drop type
		$payload->type = 'unexpected';
		$this->assertFalse( $this->di->create_drop( $payload ) );
	}

	// Check if errors are handled properly
	function test_error_handling() {

	}
}

