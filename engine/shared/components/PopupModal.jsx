const React = require('react');
const BlockCollection = require('../../App/components/blocks/BlockCollection.jsx');
const PresetCollections = require('../../App/components/presets/PresetCollections.jsx');
const SavedTemplates = require('../../App/components/templates/SavedTemplates.jsx');
const ImportTemplate = require('../../App/components/templates/ImportTemplate.jsx');
const AppStore = require('../../App/AppStore');
let PopupModal = React.createClass({
    getInitialState(){
        return {
          active: 'tab-block',
          assetUrl: '',
        };
    },

    componentDidMount(){
        let assetUrl = AppStore.getAssetUrl();
        this.setState({
            assetUrl: assetUrl
        })
    },
    handleClosePopup(){
        var modalElement = document.querySelector('#onepager-builder .popup-modal');
        modalElement.classList.remove('open');
    },
    handleTab(e){
        var tabElement = e.target.getAttribute('id');
        this.setState({
            active: tabElement
        })
    },
    render() {
        console.log("rendering popup");
        console.log('popup state', this.state);
        const opAssetUrl = this.state.assetUrl;
        const OPLOGO = opAssetUrl + '/images/logo.svg';
        let blocks = this.props.blocks;
        // let pagePresets = this.props.pagePresets;
        let savedTemplates = this.props.savedTemplates;
    
        return (
            <div className="popup-modal-wrapper">
                <div className="topbar">
                    <div className="logo">
                        <object type="image/svg+xml" data={OPLOGO}>
                          Your browser does not support SVG
                        </object>
                        <b>Library</b>
                    </div>
                    <div className="tab">
                        <ul className="tx-nav tx-nav-tabs">
                            <li onClick={this.handleTab} id="tab-block" className={'tab-block' === this.state.active ? 'tab-block active' : null} > Blocks </li>
                            {/* <li onClick={this.handleTab} id="tab-page" className={'tab-page' === this.state.active ? 'tab-page active' : null} >Pages</li> */}
                            <li onClick={this.handleTab} id="tab-my-template" className={'tab-my-template' === this.state.active ? 'tab-my-template active' : null}>My Templates</li>
                        </ul>
                    </div>
                    <div className="right">
                        <button onClick={this.handleTab} id="tab-upload" className={'tab-upload' === this.state.active ? 'tab-upload' : null }>Upload</button>
                        <span className="close-btn" onClick={this.handleClosePopup}>
                            <i className="fa fa-times"></i>
                        </span>
                    </div>
                </div>
                <div className="block-collection-wrapper">
                    <div className="tab-content">
                        <div id="tab-block" className={'tab-block' === this.state.active ? 'tab-pane tab-block active' : 'tab-pane'}>
                            <BlockCollection blocks={blocks}/>
                        </div>
                        <div id="tab-page" className={'tab-page' === this.state.active ? 'tab-pane tab-page active' : 'tab-pane'}>
                            {/* <PresetCollections pagePresets={pagePresets}/> */}
                        </div>
                        <div id="tab-my-template" className={'tab-my-template' === this.state.active ? 'tab-pane tab-my-template active' : 'tab-pane'}>
                            <SavedTemplates templates={savedTemplates}/>
                        </div>
                        <div id="tab-upload" className={'tab-upload' === this.state.active ? 'tab-pane tab-upload active' : 'tab-pane'}>
                            <ImportTemplate />
                        </div>
                        <div className="bottom-bar">
                            <h4>We are developing more blocks. Stay tuned.</h4>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
});
module.exports = PopupModal;
