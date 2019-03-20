<?php
$layouts = onepager()->presetManager()->all();
$groups = array_unique(array_reduce($layouts, function ($carry, $layout) {
    return array_merge($carry, $layout['group']);
}, []));

function op_get_html_group_class($groups)
{
    return implode(' ', array_map(function ($group) {
        return sanitize_title($group);
    }, $groups));
}
?>
<style type="text/css">
  #op_create_page_from_layout_button .uk-spinner{
    float: right;
    margin-left: 20px;
    line-height: 38px;
  }
</style>
<div class="wrap" uk-filter="target: .layout-filter">
  <h1 class="uk-title">Dashboard</h1>
  
  <ul class="uk-subnav uk-subnav-pill" uk-margin>
    <li class="uk-active" uk-filter-control><a href="#">All</a></li>
    <?php foreach ($groups as $group):?>
      <li uk-filter-control="[data-group*='<?php echo sanitize_title($group)?>']"><a href="#"><?php echo $group?></a></li>
    <?php endforeach;?>
  </ul>

  <div class="layout-filter uk-child-width-1-2@s uk-child-width-1-4@m uk-flex-center" uk-grid>
  <?php foreach ($layouts as $layout): ?>
    <div data-group="<?php echo op_get_html_group_class($layout['group'])?>">
      <div class="uk-card uk-card-default uk-transition-toggle" tabindex="0">
        <div class="uk-card-media-top uk-inline">

          <img data-src="<?php echo $layout['screenshot'] ?>" alt="<?php echo $layout['name'] ?>" uk-img >
          <div class="uk-position-cover uk-overlay uk-overlay-primary uk-transition-fade"></div>
          <div class="uk-position-center uk-text-center uk-transition-fade">
            <p>
              <button 
                id="layout-import"
                class="uk-button uk-button-primary uk-border-pill"
                uk-toggle="target: #layout-selection-modal"
                data-name="<?php echo $layout['name'] ?>"
                data-image="<?php echo $layout['screenshot'] ?>"
                data-id="<?php echo $layout['id'] ?>"
                >Import</button>
            </p>
          </div>
        </div>
        
        <p class="uk-card-footer uk-padding-small uk-margin-remove"><?php echo $layout['name'] ?></p>
      </div>
    </div>
  <?php endforeach; ?>
  </div>

  <!-- This is the modal -->
  <div id="layout-selection-modal" class="uk-flex-top" uk-modal="bg-close:false">
      <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
        <h4 class="uk-modal-title"><?php _e('Get started with ', 'onepager');?> <strong class="uk-text-primary"></strong></h4>
        <div uk-grid>
            <div class="uk-width-1-3@m">
              <img id="layout-image" />
            </div>
            <div class="uk-width-2-3@m">
            <form class="uk-form-stacked" method="post" action="options.php">
              <div class="uk-margin">
                  <label class="uk-form-label" for="form-stacked-text"><?php echo _e('Page Title', 'onepager')?></label>
                  <div class="uk-form-controls">
                      <input require class="uk-input page-title" id="form-stacked-text" type="text" placeholder="<?php _e('Name of your page', 'onepager');?>" required>
                  </div>
              </div>
              <div class="uk-margin">
                <button 
                  type="submit" 
                  id="op_create_page_from_layout_button"
                  class="uk-button uk-button-primary"
                  name="op_create_page_from_layout_button">
                  <span uk-spinner style="display:none"></span>
                    <?php _e('Create', 'onepager');?>
                </button>
              </div>
            </form>
            </div>
        </div>
        <button class="uk-modal-close-default" type="button" uk-close></button>
      </div>
  </div>
</div>

<script>
  function addPage(data) {
    jQuery(".uk-spinner").css("display", "inline-block");

    $.post(ajaxurl, data, function (res) {
      if (res && res.success) {
        window.location = res.url;
        jQuery(".uk-spinner").css("display", "none");
      } else {
        alert("failed to insert layout ");
        jQuery(".uk-spinner").css("display", "none");
      }
    });
  }
  
</script>
<script>
UIkit.util.on('#layout-import', 'click', function (e) {
  e.preventDefault();

  $ = $ = jQuery;
  
  var name = $(this).data('name'),
      imagePath = $(this).data('image'),
      layoutId = $(this).data('id');
  // Set image
  $('#layout-image').attr('src', imagePath);
  // $('#layout-image').attr('alt', name);
  // Set name
  $('.uk-modal-title strong').text(name);
  // Set layout id 
  $('#layout-selection-modal .uk-button').val(layoutId);
});

UIkit.util.on('#op_create_page_from_layout_button', 'click', function (e) {
  e.preventDefault();

  $ = $ = jQuery;
  let pageTitle = $(".page-title").val();

  if(!pageTitle) {
    alert("Please give a title for your page")
    return;
  }

  addPage({
    action: 'onepager_add_page',
    pageTitle: pageTitle,
    layoutId: $('#layout-selection-modal .uk-button').val()
  })

});

</script>