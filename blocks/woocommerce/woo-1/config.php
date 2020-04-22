<?php

return array(

	'slug'      => 'woo-1', // Must be unique and singular
	'groups'    => array('woocommerce'), // Blocks group for filter and plural

  // Fields - $contents available on view file to access the option
	'contents' => array(
		array(
			'name' => 'title',
			'value' => 'Our Store',
		),
		array(
			'name' => 'description',
			'type' => 'textarea',
			'value' => 'Select product from our store',
		),
		array(
			'name' => 'category',
			'label' => 'Product by category',
			'type' => 'woocategories',
		),
		array(
			'name' => 'num_products',
			'label' => 'Number Of Products',
			'value' => '6',
		),
		array(
			'name'		=> 'prod_options',
			'label'		=> 'Product Options',
			'type'		=> 'select',
			'value'		=> 'recent_prod',
			'options'	=> array(
				'recent_prod'		=> 'Recent Product',
				'featured_prod'		=> 'Featured Product',
				'best_selling'		=> 'Best Selling Product',
				'sale_prod'			=> 'Sale Product'
			)
		),
		array(
			'name' => 'columns',
			'label' => 'Columns',
			'type' => 'select',
			'value' => '3',
			'options' => [
				'2' => '2',
				'3' => '3',
				'4' => '4',
			],
		),
		array(
			'name' => 'text_limit',
			'label' => 'Product Desc Length',
			'value' => 20,
		),
		array(
			'name' => 'display_price',
			'label' => 'Display price',
			'type' => 'switch',
			'value' => 'yes',
		),
		array(
			'name' => 'add_to_cart',
			'label' => 'Add to cart button',
			'type' => 'switch',
			'value' => 'yes',
		),

	),

	// Settings - $settings available on view file to access the option
	'settings' => array(
		array('label' => 'Section Heading', 'type' => 'divider'),
		array(
	      'name'     => 'heading_type',
	      'label'    => 'Heading Type',
	      'type'     => 'select',
	      'value'    => 'h1',
	      'options'  => array(
	        'h1'   => 'h1',
	        'h2'   => 'h2',
	        'h3'   => 'h3',
	        'h4'   => 'h4',
	        'h5'   => 'h5',
	        'h6'   => 'h6',
	      ),
	    ),
    
		array(
			'name'   => 'section_title_size',
			'label'  => 'Title Size',
			'append' => 'px',
			'value'  => '60',
		),
		array(
			'name'     => 'title_transformation',
			'label'    => 'Title Transformation',
			'type'     => 'select',
			'value'    => 'inherit',
			'options'  => array(
				'inherit' => 'Default',
				'lowercase'   => 'Lowercase',
				'uppercase'   => 'Uppercase',
				'capitalize'  => 'Capitalized',
			),
		),

		array(
			'name'     => 'title_alignment',
			'label'    => 'Title Alignment',
			'type'     => 'select',
			'value'    => 'center',
			'options'  => array(
				'left'      => 'Left',
				'center'    => 'Center',
				'right'     => 'Right',
				'justify'   => 'Justify',
			),
		),

		array(
			'name' => 'desc_size',
			'label' => 'Desc Size',
			'append' => 'px',
			'value' => '20',
		),


		array(
			'name'     => 'title_animation',
			'label'    => 'Animation',
			'type'     => 'select',
			'value'    => '0',
			'options'  => array(
				'0'                     => 'None',
				'fade'                  => 'Fade',
				'scale-up'              => 'Scale Up',
				'scale-down'            => 'Scale Down',
				'slide-top-small'       => 'Slide Top',
				'slide-bottom-small'    => 'Slide Bottom',
				'slide-left-small'      => 'Slide Left',
				'slide-right-small'     => 'Slide Right',
			),
		),

		array('label' => 'Items', 'type' => 'divider'),

		array(
			'name' => 'item_title_size',
			'label' => 'Title Size',
			'append' => 'px',
			'value' => '18',
		),

		array(
			'name'     => 'item_title_transformation',
			'label'    => 'Transformation',
			'type'     => 'select',
			'value'    => 'inherit',
			'options'  => array(
				'inherit' => 'Default',
				'lowercase'   => 'Lowercase',
				'uppercase'   => 'Uppercase',
				'capitalize'  => 'Capitalized',
			),
		),


		array(
			'name' => 'item_desc_size',
			'label' => 'Item Desc Size',
			'append' => 'px',
			'value' => '14',
		),

		array(
			'name'     => 'item_animation',
			'label'    => 'Animation Item',
			'type'     => 'select',
			'value'    => 'fadeInUp',
			'options'  => array(
				'0'                     => 'None',
				'fade'                  => 'Fade',
				'scale-up'              => 'Scale Up',
				'scale-down'            => 'Scale Down',
				'slide-top-small'       => 'Slide Top',
				'slide-bottom-small'    => 'Slide Bottom',
				'slide-left-small'      => 'Slide Left',
				'slide-right-small'     => 'Slide Right',
			),
		),

	),

	// Fields - $styles available on view file to access the option
	'styles' => array(
		array(
			'name'    => 'bg_color',
			'label'   => 'Background Color',
			'type'    => 'colorpicker',
			'value'   => '#fff',
		),

		array('label' => 'Heading', 'type' => 'divider'),
		array(
			'name'  => 'section_title_color',
			'label' => 'Title Color',
			'type'  => 'colorpicker',
			'value' => '#323232',
		),

		array(
			'name'  => 'desc_color',
			'label' => 'Desc Color',
			'type'  => 'colorpicker',
			'value' => '#323232',
		),

		array('label' => 'Items', 'type' => 'divider'),
		array(
			'name'  => 'item_title_color',
			'label' => 'Item Title Color',
			'type'  => 'colorpicker',
			'value' => '#323232',
		),
		array(
			'name'  => 'item_desc_color',
			'label' => 'Item Desc Color',
			'type'  => 'colorpicker',
			'value' => '#323232',
		),
		array(
			'name'    => 'button_text_color',
			'label'   => 'Button Text Color',
			'type'    => 'colorpicker',
			'value'   => '#323232',
		),
	),


  // 'assets' => function( $path ){
  // Onepager::addStyle('blog-1', $path . '/style.css');
  // }
);
