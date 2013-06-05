<?php
/**
 * Simple Wrapper class for Twig template engine
 *
 */
require_once __DIR__ . "/twig/lib/Twig/Autoloader.php";
class WP_Twig {
	public $loader,
	$e;
	function __construct( $templates_dir_path = '', $env_cache = false ) {
		if ( !file_exists( $templates_dir_path ) )
			return;
		if ( $env_cache && !file_exists( $env_cache ) )
			$env_cache = false;

		Twig_Autoloader::register();

		// Define template directory location
		$this->loader = new Twig_Loader_Filesystem( $templates_dir_path );

		// Initialize Twig environment
		$this->e = new Twig_Environment( $this->loader, array(
				'cache'       => $env_cache,
				'auto_reload' => true
		) );
	}

	/**
	 * Twig uses file system to cache compiled templates
	 * This is not always possible depending on your setup
	 *
	 * So use wp_cache_ methods
	 * @param  string $template [description]
	 * @param  array  $data     [description]
	 * @return [type]           [description]
	 */
	function render( $template = '', $data = array() ) {

		$cache_key = "{$template}:" . md5( serialize( $data ) );
		if ( false !== $cached_tmpl = wp_cache_get( $cache_key, 'wp-twig' ) ) {
			echo $cached_tmpl;
			return;
		}

		$cached_tmpl = $this->e->render( $template, $data );
		wp_cache_add( $cache_key, $cached_tmpl, 'wp-twig' );

		echo $cached_tmpl;
	}
}
