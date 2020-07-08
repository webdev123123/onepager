const _ = require('underscore');
const swal = require('sweetalert');
const React = require('react');
const Tab = require('./Tab.jsx');
const TabPane = require('../../../shared/components/TabPane.jsx');
const OptionActions = require('../../../Optionspanel/OptionActions.js');
const SectionTransformer = require('../../../shared/onepager/sectionTransformer.js');
const ODataStore = require('../../../shared/onepager/ODataStore.js');
const AppActions = require('../../flux/AppActions.js');
const AppStore = require('../../AppStore.js');
const SectionList = require('../section-list/SectionList.jsx');
const SectionControls = require('./SectionControls.jsx');
const Settings = require("./Settings.jsx");
const Menu = require("./Menu.jsx");
const $ = jQuery;
// const Footer = require('./../section-list/Footer.jsx');
const BlockCollection = require('../blocks/BlockCollection.jsx');
const PopupModal = require('../../../shared/components/PopupModal.jsx');

import notify from '../../../shared/plugins/notify.js';
import cx from "classnames";
import './assets/overlay.less';
const {opi18n:i18n} = onepager;
let Sidebar = React.createClass({
  // we need to optimize this with immutability
  // mixins: [PureMixin],

  collapseSidebar(){
    AppActions.collapseSidebar(!this.props.collapseSidebar)
  },

  getInitialState(){
    return {
      saving: false,
      collapse: false,
      isSettingsDirty: false,
      saveTemplateLoading:false,
      exportLoading:false,
      modalActiveTab:'',
      savedTemplates:''
    };
  },
  
  componentDidMount(){
    this._unsavedAlert();
    this._initNiceScroll();
    // $('body #onepager-preview').find('iframe#onepager-iframe').wrap("<div id='preview-frame-wrapper'></div>");
    this.setState({
      savedTemplates:this.props.savedTemplates
    })
  },
  componentWillReceiveProps (nextProps){
    this.setState({
      modalActiveTab:nextProps.modalActiveTabName,
      savedTemplates:nextProps.savedTemplates
    });
  },


  /**
   * handle section update
   */
  handleSave(){
    let updated = AppStore.save(); // return a promise
    this.setState({saving: true});

    updated.then(()=> {
      this.setState({saving: false});
    }, ()=> {
      this.setState({saving: false});
      swal(i18n.error.save);
    });
  },
  /**
   * handle global settings
   * Need to move this function 
   * to another part of this application
   */
  handleGlobalSettingsSave(){
    let serializedSections = SectionTransformer.serializeSections(this.props.sections);
    let isSectionsDirty = AppStore.isDirty();

    let updated = OptionActions.syncWithSections
      .triggerPromise(serializedSections, (sections)=> {
        AppStore.settingsChanged(sections, isSectionsDirty);
      });

    this.setState({saving: true});

    updated.then(()=> {
      this.setState({isSettingsDirty: false, saving: false});
    }, ()=> {
      this.setState({saving: false});
      swal(i18n.error.save);
    });
  },
  /**
   * handle page settings option panel
   * update the database
   */
  handlePageSettingsSave(){
    let serializedSections = SectionTransformer.serializeSections(this.props.sections);
    let isSectionsDirty = AppStore.isDirty(); // return a promise
    let updated = OptionActions.syncPageWithSections
      .triggerPromise(serializedSections, (sections)=> {
        AppStore.settingsChanged(sections, isSectionsDirty);
      });
    this.setState({saving: true});

    updated.then(()=> {
      this.setState({isSettingsDirty: false, saving: false});
    }, ()=> {
      this.setState({saving: false});
      swal(i18n.error.save);
    });
  },
  /**
   * handle page settings option panel
   * let know the builder 
   * when any changes happen
   */
  whenSettingsDirty(){
    //FIXME: why the! should I use a promise here?
    OptionActions
      .isDirty
      .triggerPromise()
      .then(isSettingsDirty=> this.setState({isSettingsDirty}));
  },
  /**
   * handle responsive iframe check
   */
  handleResponsiveToggle(){
    $('.op-footer-wrapper .save-option-panel').find('.save-option-lists').removeClass('open');
    $('.op-footer-wrapper .responsive-check-panel').find('.responsive-devices').toggleClass('open');
  },
  /**
   * handle page option panel
   * for save template and export
   */
  handleSaveOptionToggle(){
    $('.op-footer-wrapper .responsive-check-panel').find('.responsive-devices').removeClass('open');
    $('.op-footer-wrapper .save-option-panel').find('.save-option-lists').toggleClass('open');
  },
  /**
   * export page
   */
  handleExport(){
    if(this.props.isDirty){
      return; 
    }
    if(this.props.sections.length < 1){
      notify.error(i18n.error.section_add);
      return;
    }
    let pageTitle = ODataStore.pageInfo.title;
    var userTemplate = prompt(i18n.user_input.template_name, pageTitle);
    if(userTemplate == null){
      return;
    }
    let exported = AppStore.exportPage(); // return a promise
    this.setState({exportLoading: true});

    let trimmedTitle = ODataStore.pageInfo.title.replace(/\s+/g, '');

    var donwloadFileName = 'onepager' + trimmedTitle + ODataStore.pageId + Date.now(); 
		var name = trimmedTitle || 'template-' + 'pageId';
    var screenshot = name + ".jpg";

    exported.then( res => {
      this.exportDownloadAsJson({
        name: userTemplate,
        screenshot: screenshot,
        file:donwloadFileName,
        identifier: 'txonepager',
        type:'page',
        sections: res.sections
      })
      this.setState({exportLoading: false});
    }).catch( rej => {
      this.setState({exportLoading: false});
      swal(i18n.error.save + rej);
    });
  },
  /**
   * data hold all section data of this page
   * @param {data} 
   */
  exportDownloadAsJson(data) {
    var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent( JSON.stringify( data, null, 2 ) );
		var dlAnchorElem = document.getElementById( 'exportAnchorElem' );

    var downloadName = data.name ? data.name : data.file;

    dlAnchorElem.setAttribute( "href", dataStr );
		dlAnchorElem.setAttribute( "download", downloadName + ".json" );
		dlAnchorElem.click();
	},

  handleSaveTemplate(){
    if(this.props.isDirty){
      return; 
    }
    if(this.props.sections.length < 1){
      notify.error(i18n.error.section_add);
      return;
    }
    this.setState({
      saveTemplateLoading: false
    });
    this.handlePopupModal('tab-save-template');
  },
  /**
   * handle responsive iframe
   * @param {*} device 
   */
  handleResponsiveFrame(device){
    if( ($('body').hasClass('iframe-desktop')) || ($('body').hasClass('iframe-tablet')) || ($('body').hasClass('iframe-mobile')) ){
      $('body').removeClass('iframe-desktop iframe-tablet iframe-mobile');
    }
    let previewDevice = 'iframe-' + device;
    $('body').addClass(previewDevice);

  },


  _unsavedAlert(){
    jQuery(window).on('beforeunload', ()=> {
      if (this.state.isSettingsDirty) {
        AppStore.setTabState({active: 'op-settings'});

        return i18n.error.settings;
      }
    });
  },

  _initNiceScroll(){
//    let tabContents = React.findDOMNode(this.refs.tabContents);

//    $(function () {
//      $(tabContents).niceScroll({cursorcolor: '#ddd', cursorborder: '0'});
//    });
  },

  handleTabClick(id){
    AppStore.setTabState({active: id});
  },

  /**
   * handle the popup
   * to insert the block to page
   */
  handlePopupModal(tabName = 'tab-block'){
    // set active tab name
    let activeTabName = tabName;
    this.setState({modalActiveTab: activeTabName});
    // toggle the modal
    var modalElement = document.querySelector('#onepager-builder .popup-modal');
    modalElement.classList.toggle('open');
  },

  _renderTabs(){
    let handleTabClick = this.handleTabClick;
    let activeTab = this.props.sidebarTabState.active;

    return (
      <ul className='tx-nav tx-nav-tabs'>
        <Tab onClick={handleTabClick} id='op-sections' icon="cubes" title={i18n.tab.layout} active={activeTab}
             icon2="arrow-left" parent={true }/>
        <Tab onClick={handleTabClick} id='op-contents' icon='sliders' title={i18n.tab.contents} active={activeTab}/>
        <Tab onClick={handleTabClick} id='op-blocks' icon='cube' title={i18n.tab.blocks} active={activeTab}/>
        <Tab onClick={handleTabClick} id='op-menu' icon='link' title={i18n.tab.menu} active={activeTab}
             visibleOn="op-sections"/>
        <Tab onClick={handleTabClick} id='op-settings' icon='cog' title={i18n.tab.page_settings} active={activeTab}
             visibleOn="op-sections"/>
             {/* visibleOn=""/> */}
      </ul>
    );
  },

  render() {
    let {opi18n} = onepager;
    let {sections, blocks, activeSectionIndex, activeSection, pageSettingOptions, savedTemplates, pagePresets} = this.props;
    let sectionEditable = activeSectionIndex !== null;
    let activeTab = this.props.sidebarTabState.active;
    let sectionSettings = activeSection ? _.pick(activeSection, ['settings', 'contents', 'styles']) : {};

    let handleTabClick = this.handleTabClick;
    let handlePopupModal = this.handlePopupModal;

    let update = (key, fields)=> {
      let section = _.copy(sections[activeSectionIndex]);
      section[key] = fields;
      AppActions.updateSection(activeSectionIndex, section);
    };
    
    /**
     * live page update
     */
    let pagUpdate = (key, fields)=> {
      AppActions.updatePageSettigs(key, fields);
    };
    
    let {status, title} = ODataStore.pageInfo;
    let isSettingsDirty = this.state.isSettingsDirty;
    let {isDirty} = this.props;
    let buildModeUrl = ODataStore.disableBuildModeUrl;
    let previewLink = ODataStore.preview_link;
    let dashboardUrl = ODataStore.dashboardUrl;
    let getUpdatePlugins = ODataStore.getUpdatePlugins;
    let pluginUpdateUrl = ODataStore.pluginUpdateUrl;
    let onepagerProLoaded = ODataStore.onepagerProLoaded;
    let proUpgradeLink = ODataStore.proUpgradeLink;

    let saveButtonIcon = cx({
      "fa fa-refresh fa-spin": this.state.saving,
      "fa fa-check": !this.state.saving
    });

    let classes = cx({
      "fa fa-chevron-left": !this.props.collapseSidebar,
      "fa fa-chevron-right": this.props.collapseSidebar
    });
    
    let pageOptionClasses = cx({
      "": !isDirty,
      "dirty": isDirty,
    });

    let overlayClasses = cx({
      "saving-overlay": this.state.saving
    });

    let saveTemplateClasses = cx({
      // "fa fa-refresh fa-spin fa-fw": this.state.saveTemplateLoading,
      "fa fa-save fa-fw": this.state.saveTemplateLoading,
      "fa fa-save fa-fw": !this.state.saveTemplateLoading
    });

    let exportClasses = cx({
      "fa fa-refresh fa-spin fa-fw": this.state.exportLoading,
      "fa fa-download fa-fw": !this.state.exportLoading
    });



    return (
      <div className="builder-wrapper">

        <div className="op-sidebar op-ui clearfix">
          <header className="op-header-wrapper">
            <nav className="uk-navbar uk-navbar-container">
              <div className="uk-navbar-left"><a className="uk-logo op-btn-with-logo uk-light" href="#">{title}</a></div>
              
              <div className="uk-navbar-right">
                {
                  getUpdatePlugins? 
                  <a className="new-update-status" href={pluginUpdateUrl}>
                    {opi18n.new_update_available}
                  </a>
                  : null
                }
                <a href={previewLink} target="_blank" className="page-preview-link">
                  {opi18n.preview}
                </a>
                {/* <a href={buildModeUrl} className="uk-button uk-button-text uk-button-small uk-margin-right uk-light">
                  <span className="fa fa-close"></span>
                </a> */}
              </div>
            </nav>
          </header>
          
          <main className="op-content-wrapper" ref='tabContents'>
            {this._renderTabs()}

            <div className='tab-content'>
              <div className={overlayClasses}/>

              <TabPane id='op-sections' active={activeTab}>
                <SectionList
                  openBlocks={handleTabClick.bind(this, 'op-blocks')}
                  openPopup={handlePopupModal.bind(this, 'tab-block')}
                  activeSectionIndex={activeSectionIndex}
                  blocks={blocks}
                  sections={sections}/>
              </TabPane>

              <TabPane id="op-blocks" active={activeTab}>
                <BlockCollection blocks={blocks}/>
              </TabPane>

              <TabPane id='op-menu' active={activeTab}>
                <Menu sections={sections}/>
              </TabPane>

              <TabPane id='op-contents' active={activeTab}>
                {sectionEditable ?
                  <SectionControls
                    update={update}
                    title={sections[activeSectionIndex].title}
                    sectionSettings={sectionSettings}
                    sectionIndex={activeSectionIndex}/> :

                  <h2>{i18n.dropdown.section}</h2>
                }
              </TabPane>

              <TabPane id='op-settings' active={activeTab}>
                <Settings pagUpdate={pagUpdate} whenSettingsDirty={this.whenSettingsDirty}/>
              </TabPane>

              {/* {activeTab === "op-sections" ? <Footer /> : null } */}

            </div>
          </main>
          
          <footer className="op-footer-wrapper uk-position-bottom">
            {! onepagerProLoaded ? 
            <div className="upgrade-to-pro">
              <p>{opi18n.unlock_pro_description}</p>
              <a href={proUpgradeLink} target="_blank">
                {opi18n.upgrade_to_pro}
              </a>
            </div>
            : null}
            
            <nav className="uk-navbar uk-navbar-container">
              <div className="uk-navbar-left">
                <a href={dashboardUrl}>{opi18n.exit}</a>
              </div>
              <div className="responsive-check-panel">
                <a href="#" onClick={this.handleResponsiveToggle}><i className="fa fa-desktop responsive-check-button"></i></a> 
                <ul className="responsive-devices">
                  <li onClick={() => this.handleResponsiveFrame('desktop')}><i className="fa-fw fa fa-desktop"></i> {opi18n.res_desktop}</li>
                  <li onClick={() => this.handleResponsiveFrame('tablet')}><i className="fa-fw fa fa-tablet"></i> {opi18n.res_tablet}</li>
                  <li onClick={() => this.handleResponsiveFrame('mobile')}><i className="fa-fw fa fa-mobile-phone"></i> {opi18n.res_mobile}</li>
                </ul>
              </div>
              <div className="uk-navbar-right">
                {
                  activeTab === 'op-settings' ?
                    // <button disabled={!isSettingsDirty} onClick={this.handleGlobalSettingsSave}
                    <button disabled={!isSettingsDirty} onClick={this.handlePageSettingsSave}
                            className='uk-button uk-button-primary uk-button-small'>
                      <span className={saveButtonIcon}></span> {opi18n.update}
                    </button> :
                    <button disabled={!isDirty} onClick={this.handleSave} className='uk-button uk-button-primary uk-button-small'>
                      <span className={saveButtonIcon}></span> {opi18n.update}
                    </button>
                }
                <div className="save-option-panel">
                  <a href="#" onClick={this.handleSaveOptionToggle}><i className="fa fa-arrow-up"></i></a> 
                  <ul className="save-option-lists">
                    <li onClick={this.handleSaveTemplate} className={pageOptionClasses}><i className={saveTemplateClasses}></i> {opi18n.save_as_template}</li>
                    <li onClick={this.handleExport} className={pageOptionClasses}><i className={exportClasses}></i> {opi18n.export} </li>
                  </ul>
                  <a id="exportAnchorElem"></a>
                </div>
              </div>
            </nav>
          </footer>
          
          <div className="op-sidebar-control" onClick={this.collapseSidebar}>
            <span className={classes}></span>
          </div>
        </div>
        
        <div className="popup-modal">
          {/* <PopupModal blocks={blocks} savedTemplates={savedTemplates} pagePresets={pagePresets}/> */}
          <PopupModal active={this.state.modalActiveTab} blocks={blocks} savedTemplates={savedTemplates}/>
        </div>

      </div>

    );
  }
});

module.exports = Sidebar;
