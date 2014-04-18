<?php
/**
 * Test case for Drop It
 *
 */
class Drop_It_UnitTestCase extends WP_UnitTestCase {
	public $di;

	/**
	 * Init
	 *
	 * @return [type] [description]
	 */
	function setup() {
		parent::setup();
		$this->di = new Drop_It;
		$this->di->register_drops();
	}

	function teardown() {
	}

	function test_available_drops() {
		$this->assertInternalType( 'array', $this->di->drops, 'message');
		$this->assertNotEmpty( $this->di->drops );
	}

	function test_is_subclass() {
		foreach ( $this->di->drops as $drop_slug => $drop_instance ) {
			$this->assertInstanceOf( 'Drop_It_Drop', $drop_instance );
		}
	}

	function test_create_drop() {
		// Test successful creation of static drop
		$post_id = $this->factory->post->create( array( 'post_type' => 'di-zone' ) );
		$payload = (object) array( 'type' => 'static_html', 'content' => 'test', 'post_id' => $post_id );
		$drop_result = json_decode( $this->di->create_drop( $payload ) );
		$this->assertInternalType( 'object', $drop_result );
		$this->assertGreaterThan( 0, $drop_result->meta_id );
	}

	function test_create_drop_failure() {
		// Test failed creation of static drop
		$post_id = $this->factory->post->create( array( 'post_type' => 'di-zone' ) );
		$payload = (object) array( 'type' => 'unexpected', 'content' => 'test', 'post_id' => $post_id );
		$this->assertFalse( $this->di->create_drop( $payload ) );
	}

	// Check if errors are handled properly
	function test_error_handling() {
		$this->assertTrue( true, 'message');
	}
	/**
	 * [test_ajax_search description]
	 *
	 * @return [type] [description]
	 */
	function test_ajax_search() {
		// Successful search
		$titles = array( 'test', 'rest', 'blast', 'fast' );
		$post_id = $this->factory->post->create_many( '20',  array( 'post_type' => 'di-zone', 'post_title' => array_rand( $titles ) ) );
		$_GET['term'] = 'test';
		$_GET['exclude'] = '1,2,3,4,5,6,7';

		$this->assertNotEmpty( $this->di->_ajax_search() );
	}
}
