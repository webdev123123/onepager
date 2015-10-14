const $ = jQuery; //jshint ignore: line
const _ = require('underscore');
const assign = require('object-assign');
const AppDispatcher = require('./flux/AppDispatcher.js');
const Constants = require('./flux/AppConstants.js');
const SectionTransformer = require('./../shared/onepager/sectionTransformer.js');
const ShouldSync = require('../shared/lib/ShouldSync.js');
const Activity = require('../shared/lib/Activity.js');
const ODataStore = require('./../shared/onepager/ODataStore.js');
const BaseStore = require('./flux/BaseStore.js');
const AppActions = require('./flux/AppActions.js');
const SyncService = require('./AppSyncService.js');

require('./../shared/onepager/lib/_mixins.js');

import toolbelt from '../shared/lib/toolbelt.js';
import storage from '../shared/lib/storage.js';
import localState from './../shared/onepager/localState.js';

let {serializeSections, unserializeSections} = SectionTransformer;
let {stripClassesFromHTML} = SectionTransformer;

// data storage
let _blocks = ODataStore.blocks.sort(function (a, b) {
  return +(a.slug > b.slug) || +(a.slug === b.slug) - 1;
});

let _sections = SectionTransformer.unserializeSections(ODataStore.sections, _blocks);
let _menuState = {id: null, index: null, title: null};
let _savedSections = getSerializedSectionsAsJSON(_sections);
let AUTO_SAVE_DELAY = 500;
let _previewFrameLoaded = false;

let _collapseSidebar = localState.get('collapseSidebar', false);
let _activeSectionIndex = _sections[localState.get('activeSectionIndex')] ? localState.get('activeSectionIndex') : null;
let _sidebarTabState = _activeSectionIndex !== null ?
  localState.get('sidebarTabState', {active: 'op-sections'}) :
{active: 'op-sections'};


let shouldLiveSectionsSync = ShouldSync(_sections, 'sections');
let shouldSectionsSync = ShouldSync(_sections, 'sections');

let syncService = SyncService(ODataStore.pageId, Activity(AUTO_SAVE_DELAY), shouldSectionsSync);
let liveService = SyncService(null, Activity(AUTO_SAVE_DELAY), shouldLiveSectionsSync);

function collapseSidebar(collapse) {
  _collapseSidebar = collapse;
}

function getSerializedSectionsAsJSON(section) {
  return JSON.stringify(serializeSections(section));
}

function getBlockBySlug(slug) {
  return _.find(_blocks, {slug});
}

// function to activate a section
function setActiveSection(index) {
  _activeSectionIndex = index;
}

// function to add a section
function addSection(section) {
  let sectionIndex = _sections.length; //isn't it :p

  //its a row section to need to uni(quei)fy
  section = SectionTransformer.unifySection(section);
  _sections.push(section);
  setActiveSection(sectionIndex);

  liveService.updateSection(_sections, sectionIndex);

}


// function to update a section
function updateSection(sectionIndex, section) {
  //immutable please?
  _sections[sectionIndex] = section;

  liveService.updateSection(_sections, sectionIndex);
}

// function to duplicate a section
function duplicateSection(index) {
  let sectionIndex = _sections.length; //isn't it :p

  //its a row section to need to uni(quei)fy
  let section = SectionTransformer.unifySection(_sections[index], true);

  _sections = toolbelt.pushAt(toolbelt.copy(_sections), index + 1, section);

  liveService.updateSection(_sections, sectionIndex);

  setActiveSection(sectionIndex);
}


// function to remove section
function removeSectionStyle(id) {
  $("#onepager-preview iframe").contents().find(`#style-${id}`).remove();
}

function replaceSectionStyle(id, style) {
  let $preview = $("#onepager-preview iframe").contents();
  $preview.find(`#style-${id}`).remove();
  $preview.find("head").append(style);

  console.log(`section style of ${id} is replaced`);
}


function removeSection(index) {
  removeSectionStyle(_sections[index].id);

  //immutable please
  _sections.splice(index, 1);

  //bad pattern
  liveService.rawUpdate(_sections);

  setActiveSection(null);
}


function updateTitle(index, previousTitle, newTitle) {
  let section = toolbelt.copy(_sections[index]);
  section.title = newTitle;

  if ('untitled section' === previousTitle) {
    section.id = getUniqueSectionId(_sections, index, newTitle);
  }

  updateSection(index, section);
}

function getUniqueSectionId(sections, index, title) {
  let id = toolbelt.dasherize(title); //make es4 compatible

  while (!toolbelt.isUniquePropInArray(sections, index, 'id', id)) {
    id = id + 1;
  }

  return id;
}

function sectionSynced(index, res) {
  let section;

  _sections[index] = toolbelt.copy(_sections[index]);
  section = _sections[index];

  section.content = stripClassesFromHTML(section.livemode, res.content);
  replaceSectionStyle(section.id, res.style);
}


function updateSections(sections) {
  let blocks = ODataStore.blocks;

  _sections = unserializeSections(sections, blocks);
  _sections.map(function (section) {
    replaceSectionStyle(section.id, section.style);
  });
}

function emitChange(){
  AppStore.emitChange();
}

function editSection(index) {
  setActiveSection(index);
  AppStore.setTabState({active: 'op-contents'});
}

function reloadSections() {
  liveService
    .reloadSections(serializeSections(_sections))
    .then(function(sections){
      AppActions.updateSections(sections);
    });
}

function refreshSections(sections) {
  liveService.reloadSections(sections).then(function(updatedSections){
    AppActions.updateSections(updatedSections);
  });
}

function setPreviewFrameLoaded(){
  _previewFrameLoaded = true;
}


let dispatcherIndex = AppDispatcher.register(function (payload) {
  const actions = Constants.ActionTypes;
  const action = payload.action;

  switch (action.type) {
    case actions.ADD_SECTION:
      addSection(action.section);
      emitChange();
      break;

    case actions.EDIT_SECTION:
      editSection(action.index);
      emitChange();
      break;

    case actions.COLLAPSE_SIDEBAR:
      collapseSidebar(action.collapse);
      emitChange();
      break;

    case actions.ACTIVATE_SECTION:
      setActiveSection(action.index);
      emitChange();
      break;

    case actions.UPDATE_SECTION:
      updateSection(action.index, action.section);
      emitChange();
      break;

    case actions.REMOVE_SECTION:
      removeSection(action.index);
      emitChange();
      break;

    case actions.DUPLICATE_SECTION:
      duplicateSection(action.index);
      emitChange();
      break;

    case actions.SECTIONS_SYNCED:
      sectionSynced(action.index, action.res);
      emitChange();
      break;

    ///maybe do not need it
    case actions.RELOAD_SECTIONS:
      reloadSections();
      break;

    case actions.REFRESH_SECTIONS:
      refreshSections(action.sections);
      break;

    case actions.UPDATE_SECTIONS:
      updateSections(action.sections);
      emitChange();
      break;

    case actions.UPDATE_TITLE:
      updateTitle(action.index, action.previousTitle, action.newTitle);
      emitChange();
      break;

    case actions.PREVIEW_FRAME_LOADED:
      setPreviewFrameLoaded();
      emitChange();
      break;
  }
});

// Facebook style store creation.
let AppStore = assign({}, BaseStore, {

  // public methods used by Controller-View to operate on data
  getAll() {
    return {
      isDirty: this.isDirty(),
      blocks: _blocks,
      sections: _sections,
      menuState: _menuState,
      activeSection: _sections[_activeSectionIndex],
      collapseSidebar: _collapseSidebar,
      sidebarTabState: _sidebarTabState,
      activeSectionIndex: _activeSectionIndex,
      previewFrameLoaded: _previewFrameLoaded
    };
  },

  save(){
    let updated = syncService.rawUpdate(_sections);

    updated.then(this.setSectionsAsSavedSections);

    return updated;
  },

  isDirty(){
    return getSerializedSectionsAsJSON(_sections) !== _savedSections;
  },

  setSectionsAsSavedSections(){
    _savedSections = getSerializedSectionsAsJSON(_sections);
    emitChange();
  },

  get(index){
    return _sections[index];
  },

  getBlock(slug){
    return getBlockBySlug(slug);
  },

  setTabState(state){
    _sidebarTabState = state;
    emitChange();
  },

  setSections(sections){
    _sections = sections;
    emitChange();
  },

  setMenuState(id, title, index){
    _menuState = {id, title, index};
    emitChange();
  },

  reorder(sections, index){
    setActiveSection(index);
    this.setSections(sections);
    liveService.rawUpdate(_sections);
  },

  reloadBlocks(){
    //FIXME: its not a place for business logic
    let reloadBlocksPromise = syncService.reloadBlocks();
    reloadBlocksPromise.then((blocks)=> {
      _blocks = blocks;
      emitChange();
    });

    return reloadBlocksPromise;
  },

  rawUpdate(){
    liveService.rawUpdate(_sections);
  },

  // register store with dispatcher, allowing actions to flow through
  dispatcherIndex
});

module.exports = AppStore;
