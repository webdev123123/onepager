#<?php echo $id; ?>{
	<?php if($styles['bg_image']):?>
		background-image: url(<?php echo $styles['bg_image'];?>);
	<?php endif;?>
}

#<?php echo $id;?> .uk-heading-primary{
	font-size : <?php echo $settings['name_size'];?>px;
	color : <?php echo $styles['name_color'];?>;
}
#<?php echo $id?> .testimony{
	color : <?php echo $styles['testimoni_color'];?>;
}
#<?php echo $id;?> .uk-text-lead{
	font-size : <?php echo $settings['designation_size'];?>px;
	color : <?php echo $styles['designation_color'];?>;
}

#<?php echo $id; ?>.testimonial-1 .uk-dotnav>*>*{ 
	border-color:<?php echo $styles['dot_color']; ?>;
}
#<?php echo $id; ?>.testimonial-1 .uk-dotnav>.uk-active>*{
	border-color:<?php echo $styles['dot_color']; ?>;
	background-color : <?php echo $styles['dot_color']; ?>;

}

.testimonial-1:before{
	background: <?php echo $styles['overlay_color']?>;
}