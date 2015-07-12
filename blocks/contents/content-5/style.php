#<?php echo $id ?>{
	background : url(<?php echo $styles['bg_image'] ?>); 
	<?php if($styles['bg_parallax']):?>
	background-attachment : fixed;
	<?php endif;?>
	background-size : cover;
	color : <?php echo $styles['text_color']?>;
}
#<?php echo $id ?> .section-title{
	color : <?php echo $styles['title_color']?>;
}
#<?php echo $id ?> .desc{
	color : <?php echo $styles['text_color']?>;
}
#<?php echo $id ?> .btn{
	background: transparent;
	border: 3px solid <?php echo $styles['button_border_color']?>;
	color : <?php echo $styles['button_text_color']?>;
}

#<?php echo $id ?> .btn:hover{
	background: <?php echo $styles['button_border_color']?>;
	color : #222;
}
