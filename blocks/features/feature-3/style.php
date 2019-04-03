#<?php echo $id; ?>{
<?php if ( $styles['bg_image'] ) : ?>
	background-image:url(<?php echo $styles['bg_image']; ?>);
	background-repeat:no-repeat;
	
<?php endif; ?>
<?php if ( $styles['bg_color'] ) : ?>
	background-color : <?php echo $styles['bg_color']; ?>;
<?php endif; ?>
}

#<?php echo $id; ?> .uk-article .uk-heading-primary{
	font-size : <?php echo $settings['title_size']; ?>px;
	color : <?php echo $styles['title_color']; ?>;
}

#<?php echo $id; ?> .uk-article .uk-text-lead{
	font-size : <?php echo $settings['desc_size']; ?>px;
	color : <?php echo $styles['desc_color']; ?>;
}

#<?php echo $id; ?> .uk-article .uk-panel .uk-card-title,
#<?php echo $id; ?> .uk-article .uk-panel .uk-card-title a{
	font-size : <?php echo $settings['item_title_size']; ?>px;
	color:<?php echo $styles['item_title_color']; ?>;
}

#<?php echo $id; ?> .uk-article .uk-panel .uk-text-medium{
	font-size : <?php echo $settings['item_desc_size']; ?>px;
	color:<?php echo $styles['item_desc_color']; ?>;
}

<?php if ( $styles['icon_color'] ) : ?>	
	#<?php echo $id; ?> .op-media{
		color : <?php echo $styles['icon_color']; ?>;
	}
<?php endif; ?>


@media(max-width:768px){
	#<?php echo $id; ?> .uk-article .uk-heading-primary{
		font-size : <?php echo ( $settings['title_size'] / 1.5 ); ?>px;
	}
	#<?php echo $id; ?> .uk-card-media-left img{
	  width:50px;
	}
}
