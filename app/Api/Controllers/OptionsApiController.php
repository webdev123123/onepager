<?php namespace App\Api\Controllers;

class OptionsApiController extends ApiController {
	public function saveOptions() {
		$page    = array_get( $_POST, 'page', false );
		$options = array_get( $_POST, 'options', [ ] ) ?: [ ]; // making sure its an array
		$options = $this->filterInput( $options );

		onepager()->optionsPanel( $page )->update( $options );
		onepager()->render()->mergeSectionsAndSettings();

		$sections = array_get( $_POST, 'sections', [ ] ) ?: [ ]; // making sure its an array

		if ( count( $sections ) ) {
			$sections = $this->filterInput( $sections );
			$sections = onepager()->render()->mergeSectionsBlocksSettings( $sections );
			$this->responseSuccess( compact( 'sections' ) );
		} else {
			$this->responseSuccess();
		}
	}
	public function savePageSettingsOption(){
		/**
		 * receive the requested data
		 * @page
		 * @pageID
		 * @pageOptions
		 * @sections
		 */
		$page    = array_get( $_POST, 'page', false );
		$pageID  = array_get($_POST, 'pageID', ''); 
		$pageOptions = array_get($_POST, 'options', [ ] ) ?: [ ]; 
		$pageOptions = $this->filterInput( $pageOptions );
		/**
		 * save data to db
		 */
		onepager()->optionsPanel( $page )->updatePageSettingsOption( $pageID, $pageOptions );
		/**
		 * update all pages with the sections
		 */
		onepager()->render()->mergeSectionsAndSettings();
		/**
		 * Need to check again this.
		 */
		onepager()->render()->mergeSectionsAndSettingsWithPage($pageID);

		$sections = array_get( $_POST, 'sections', [ ] ) ?: [ ]; // making sure its an array		

		if ( count( $sections ) ) {
			$sections = $this->filterInput( $sections );
			$sections = onepager()->render()->mergeSectionsBlocksSettings( $sections );
			$this->responseSuccess( compact( 'sections' ) );
		} else {
			$this->responseSuccess();
		}
	}

	public function pageSettingsOptionLive(){
		$pageId  = array_get($_POST, 'pageId', ''); 
		$pageOptions = array_get($_POST, 'options', [ ] ) ?: [ ]; 
		$pageOptions = $this->filterInput( $pageOptions );
		$sections = array_get( $_POST, 'sections', [ ] ) ?: [ ]; // making sure its an array
		
		$styleArr = onepager()->render()->syncPageStyles($sections, $pageId, $pageOptions);
		if($styleArr){
			$optionStyleArr = $styleArr;
			$this->responseSuccess(compact('optionStyleArr'));
		}else{
			$this->responseSuccess('Something wend wrong. Need to check ajax request data');
		}
	}
}
