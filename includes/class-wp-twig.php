<?php
/**
 * Simple Wrapper class for Twig template engine
 *
 */
require_once __DIR__ . "/vendor/twig/lib/Twig/Autoloader.php";
class WP_Twig {

	public $loader,
	$e,
	$twig_error;

	function __construct( $templates_dir_path = '', $env_cache = false ) {
/*		if ( !file_exists( $templates_dir_path ) )
			return;*/
		if ( $env_cache && !file_exists( $env_cache ) )
			$env_cache = false;

		// Catch basic exception thrown
		try {
			Twig_Autoloader::register();
		} catch( Exception $e ) {
			$this->process_exception( $e );
		}

		try {
			// Define template directory location
			$this->loader = new Twig_Loader_Filesystem( $templates_dir_path );
		} catch( Exception $e ) {
			$this->process_exception( $e );
		}

		try {
			// Initialize Twig environment
			$this->e = new Twig_Environment( $this->loader, array(
					'cache'       => $env_cache,
					'auto_reload' => true,
					'autoescape' => false
			) );

		} catch( Exception $e ) {
			$this->process_exception( $e );
		}
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
		try {
			$cached_tmpl = $this->e->render( $template . '.tpl', $data );
		} catch( Exception $e ) {
			$this->process_exception( $e );
		}

		// Do not cache while developing/debugging
		if ( !WP_DEBUG )
			wp_cache_add( $cache_key, $cached_tmpl, 'wp-twig' );

		echo $cached_tmpl;
	}

	function process_exception( $e ) {
		$this->twig_error = $e;
		if ( is_admin() ) {
			add_action( 'admin_notices', array( $this, 'admin_exception' ) );
		} else {
			return $this->twig_error->getMessage();
		}

	}

	function admin_exception() {
    ?>
    <div class="error">
        <p><?php echo $this->twig_error->getMessage(); ?></p>
        <p>Trace:<pre> <?php echo $this->twig_error->getTraceAsString(); ?></pre></p>
    </div>
    <?php
	}
}
