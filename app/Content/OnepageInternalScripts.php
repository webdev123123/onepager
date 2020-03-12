<?php namespace App\Content;

class OnepageInternalScripts {
	public function __construct() {
		add_action( 'wp_head', [ $this, 'injectInternalScripts' ] );
		add_filter( 'body_class', [ $this, 'injectBodyClass' ] );
	}

	public function injectInternalScripts() {
		if ( ! $this->isOnepage() || $this->isBuildMode() ) {
			return;
		}

		$pageId   = $this->getCurrentPageId();
		$sections = $this->getAllValidSectionsFromPageId( $pageId );
		$pageOptionPanel = $this->getPageOptionPanelFromPageId( $pageId );

		$this->renderStylesFromSections( $sections );
		$this->renderStylesForPageSettings(  $sections, $pageId, $pageOptionPanel );

	}
	public function injectBodyClass($classes){
		$pageId   = $this->getCurrentPageId();
		$classes[] = 'txop-page-'.$pageId;
		return $classes;
	}

	/**
	 * @return mixed
	 */
	protected function isBuildMode() {
		return onepager()->content()->isBuildMode();
	}

	/**
	 * @return mixed
	 */
	protected function isOnepage() {
		return onepager()->content()->isOnepage();
	}

	/**
	 * @return mixed
	 */
	protected function getCurrentPageId() {
		return onepager()->content()->getCurrentPageId();
	}

	/**
	 * @param $pageId
	 *
	 * @return mixed
	 */
	protected function getAllValidSectionsFromPageId( $pageId ) {
		return onepager()->section()->getAllValid( $pageId );
	}
	/**
	 * @return page option panel data
	 */
	protected function getPageOptionPanelFromPageId($pageId){
		$pageOptionPanelData = onepager()->optionsPanel('onepager')->getAllSavedPageOptions($pageId);
		return $pageOptionPanelData;
	}

	/**
	 * @param $sections
	 */
	protected function renderStylesFromSections( $sections ) {
		onepager()->render()->styles( $sections );
	}
	/**
	 * @param $pageId
	 * @param $sections
	 * @param $pageOptionPanel
	 */
	protected function renderStylesForPageSettings( $sections, $pageId, $pageOptionPanel ) {
		onepager()->render()->pageStyles( $sections, $pageId, $pageOptionPanel );
	}
}
