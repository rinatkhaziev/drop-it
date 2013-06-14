<?php
/**
 * Simple Wrapper class for Twig templating engine
 *
 */
require_once __DIR__ . "/vendor/twig/lib/Twig/Autoloader.php";
class WP_Twig {

	public
	$loaders,
	$e,
	$twig_error;

	function __construct( $templates_dir_path = array(), $env_cache = false ) {
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
		// Twig renders first found template in array of dirs
		// Theme folder is the last element in this array
		// Tmp workaround: reverse array
		// @todo Figure out the best to provide ability to configure the behavior
		$templates_dir_path = array_reverse( $templates_dir_path );

		try {
			// Define template directory location
			foreach( $templates_dir_path as $index => $path ) {
				// If there's no dir, just skip to the next path
				if ( !file_exists( $path ) )
					continue;
				$this->loaders[] = new Twig_Loader_Filesystem( $path );
			}
		} catch( Exception $e ) {
			$this->process_exception( $e );
		}

		try {
			// Initialize Twig environment
			$this->e = new Twig_Environment( new Twig_Loader_Chain( (array) $this->loaders ), array(
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
	 *
	 * @param string  $template [description]
	 * @param array   $data     [description]
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
			wp_cache_add( $cache_key, $cached_tmpl, 'wp-twig', 300 );

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
