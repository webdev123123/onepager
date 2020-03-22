<?php namespace ThemeXpert\View\Engines;

use Exception;

class PhpEngine implements EngineInterface {

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string $path
	 * @param  array  $data
	 *
	 * @return string
	 */
	public function get( $path, array $data = array() ) {
		return $this->evaluatePath( $path, $data );
	}
	public function getPageStyle( $path, array $data = array(), $pageId, $pageOptionPanel ) {
		return $this->evaluatePathForPage( $path, $data, $pageId, $pageOptionPanel  );
	}

	/**
	 * Get the evaluated contents of the view at the given path.
	 *
	 * @param  string $__path
	 * @param  array  $__data
	 *
	 * @return string
	 */
	protected function evaluatePath( $__path, $__data ) {
		$obLevel = ob_get_level();

		ob_start();

		extract( $__data );

		// We'll evaluate the contents of the view inside a try/catch block so we can
		// flush out any stray output that might get out before an error occurs or
		// an exception is thrown. This prevents any partial views from leaking.
		try {
			include $__path;
		} catch ( Exception $e ) {
			$this->handleViewException( $e, $obLevel );
		}

		return ltrim( ob_get_clean() );
	}
	/**
	 * Get the evaluated contents of the view at the given path.
	 *
	 * @param  string $__path
	 * @param  array  $__data
	 *
	 * @return string
	 */
	protected function evaluatePathForPage( $__path, $__data, $__pageId, $__pageOptionPanel ) {
		$data = $__data;
		$page_settins_data = '';
		$common_data = array_map(function($key) use ($__data, $page_settins_data){
			if($__data['settings']){
				if(array_key_exists($key, $__data['settings'])){
					return $__data['settings']['heading_type'];
				}
			}
		}, array_keys($__pageOptionPanel['general']));
		$page_general_settins = $__pageOptionPanel['general'];
		$obLevel = ob_get_level();

		ob_start();

		extract( $__data );
		?>
		.<?php echo 'txop-page-'.$__pageId .' #' .$id . ' .uk-heading-primary'; ?> {font-size:<?php echo $page_general_settins['section_title_size'] . 'px';?>;}
		<?php
		return ltrim( ob_get_clean() );
	}

	/**
	 * Handle a view exception.
	 *
	 * @param  \Exception $e
	 * @param  int        $obLevel
	 *
	 * @return void
	 *
	 * @throws $e
	 */
	protected function handleViewException( $e, $obLevel ) {
		while ( ob_get_level() > $obLevel ) {
			ob_end_clean();
		}

		throw $e;
	}

}
