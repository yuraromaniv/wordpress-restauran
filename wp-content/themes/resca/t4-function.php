<?php

 // add by Phong Lo
 
 add_action( 'wp_enqueue_scripts', 'thim_remove_unuse_script', 99998 );
 
function thim_remove_unuse_script() {
	if( is_front_page()){
		wp_dequeue_script( 'jquery-form' );
	}
	
}