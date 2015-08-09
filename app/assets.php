<?php

function onepager_localize_script_data($pageId)
{
  $onepager = onepager();

  $ajaxUrl = $onepager->api()->getAjaxUrl();
  $nav_arr = $onepager->content()->getMenus();
  $cat_arr = $onepager->content()->getCategories();
  $pages_arr = $onepager->content()->getPages();
  $blocks = array_values((array)$onepager->blockManager()->all());
  $groupOrder = $onepager->blockManager()->getGroupOrder();

  $sections = array_map(function ($section) {
    $section = onepager()->render()->sectionBlockDataMerge($section);
    $section['content'] = onepager()->render()->section($section);
    $section['style'] = onepager()->render()->style($section);

    return $section;
  }, onepager()->section()->getAllValid($pageId));

  $footer_markup = get_editor_section_list_footer();
  $disableUrl = getOpBuildModeUrl(getCurrentPageURL(), false);

  return array(
    'ajaxUrl'     => $ajaxUrl,
    'optionPanel' => onepager()->optionsPanel("onepager")->getOptions(),
    'options'     => get_option('onepager'),
    'page'        => 'onepager',
    'blocks'      => $blocks,
    'pageId'      => $pageId,
    'sections'    => $sections,
    'menus'       => $nav_arr,
    'pages'       => $pages_arr,
    'categories'  => $cat_arr,
    'groupOrder'  => $groupOrder,
    'footer'      => $footer_markup,
    'disable'     => $disableUrl,
    'presets'     => Onepager::getPresets(),
    'config'      => getOnepagerConfig()
  );
}

function onepager_enqueue_scripts()
{
  if(!onepager()->content()->isOnepage()) return;

  $q = onepager()->asset();

  // TX namespaced assets to avoid multiple assets loading from other ThemeXpert product
  $q->style('tx-bootstrap', asset('assets/css/bootstrap.css'));
  $q->style('tx-animatecss', asset('assets/css/animate.css'));
  $q->style('tx-fontawesome', asset('assets/css/font-awesome.css'));

  $q->script('tx-bootstrap', asset('assets/js/bootstrap.js'), ['jquery']);
  $q->script('tx-wow', asset('assets/js/wow.js'), array('jquery'));
  $q->script('tx-nicescroll', asset('assets/js/jquery.nicescroll.js'), array('jquery'));
  $q->script('lithium', asset('assets/lithium.js'), array('jquery'));

  $q->style('lithium', asset('assets/css/lithium.css'));


  if (onepager()->content()->isBuildMode()) {
    if (function_exists('wp_enqueue_media')) {
      wp_enqueue_media();
    }

    $q->style('tx-colorpicker', asset("assets/css/bootstrap-colorpicker.css"));
    $q->script('tx-iconselector', asset('assets/js/icon-selector-bootstrap.min.js'), ['jquery']);
    $q->script('tx-colorpicker', asset('assets/js/bootstrap-colorpicker.js'), ['jquery']);
    $q->script('tx-bootstrap-switch', asset('assets/js/bootstrap-switch.js'), ['jquery']);
    $q->script('tx-toastr', asset('assets/js/toastr.js'), ['jquery']);

    $q->script('onepager', asset('assets/app.bundle.js'), ['jquery']);

    $q->localizeScript('onepager', onepager_localize_script_data(onepager()->content()->getCurrentPageId()), 'onepager');
  }

  if (is_super_admin()) {
    $q->style('lithium-ui', asset('assets/css/lithium-builder.css'));
  }
}

function enqueueOnepagerAdminAssets()
{
  $q = onepager()->asset();

  if (function_exists('wp_enqueue_media')) {
    wp_enqueue_media();
  }

  $q->style('tx-bootstrap', asset('assets/css/bootstrap.css'));
  $q->style('tx-animatecss', asset('assets/css/animate.css'));
  $q->style('tx-fontawesome', asset('assets/css/font-awesome.css'));
  $q->style('tx-colorpicker', asset("assets/css/bootstrap-colorpicker.css"));
  $q->style('lithium-ui', asset('assets/css/lithium-builder.css'));

  $q->script('tx-bootstrap', asset('assets/js/bootstrap.js'), ['jquery']);

  $q->script('tx-iconselector', asset('assets/js/icon-selector-bootstrap.min.js'), ['jquery']);
  $q->script('tx-colorpicker', asset('assets/js/bootstrap-colorpicker.js'), ['jquery']);
  $q->script('tx-toastr', asset('assets/js/toastr.js'), ['jquery']);

  $q->script('admin-bundle', asset('assets/optionspanel.bundle.js'), ['jquery']);

  $q->enqueue();
}

function dequeue_default_template_stylesheet(){
  if(get_theme_support('onepager')) return;

  if(!onepager()->content()->isOnepage()) return;

  global $wp_styles;

  $wp_styles->remove(get_default_template_stylesheet_handle());
}

function onepager_dequeue_conflicting_scripts(){
  if(!onepager()->content()->isOnepage()) return;

  onepager_support_nextgen_scroll_gallery();
}

//NextGEN Scroll Gallery
function onepager_support_nextgen_scroll_gallery(){
  wp_dequeue_script('mootools');
  wp_dequeue_script('powertools');
  wp_dequeue_script('scrollGallery');
}

add_action('wp_enqueue_scripts', 'dequeue_default_template_stylesheet', 999);
add_action('wp_print_scripts', 'onepager_dequeue_conflicting_scripts', 100);

//live edit mode
add_action('wp_enqueue_scripts', 'onepager_enqueue_scripts');
