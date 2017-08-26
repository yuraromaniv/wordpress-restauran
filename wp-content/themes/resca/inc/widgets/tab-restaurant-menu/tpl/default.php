<?php
$rand = time() . '-1-' . rand( 0, 100 );

$j     = $k = 1;
$w_tab = '';
$w_tab = 100 / count( $instance['tab'] );
if ( $w_tab ) {
	$w_tab = ' style="width:' . $w_tab . '%"';
}


echo '<div class="menus-container row">';
foreach ( $instance['tab'] as $i => $tab ) {
	
	echo ' <div class ="menu-block col-lg-3">';

	$sub_title = $icon = '';
	if ( $tab['sub-title'] ) {
		$sub_title = '<span class="sub-title">' . $tab['sub-title'] . '</span>';
	}
	if ( $tab['icon_image'] ) {
		$images_url = wp_get_attachment_image_src( $tab['icon_image'], 'full' );
		$icon       = '<img src="' . $images_url['0'] . '">';
	}
	echo '<p>' .$tab['title'].$icon.' </p>';

	if ( isset( $tab['id'] ) ) {
		$post_id = $tab['id'];
	}

	$menu_items = get_post_meta( $post_id, '_erm_menu_items', true );
	if ( !empty( $menu_items ) ) {
		$menu_items = preg_split( '/,/', $menu_items );
		if ( isset( $tab['columns'] ) && $tab['columns'] == '2' ) {
			$class_column = ' menu_content_two_column';
		}
		echo '<div class="restaurant-menu">';
		echo '<ul class="erm_menu_content layout-default' . $class_column . '">';
		foreach ( $menu_items as $item_count =>$item_id ) {
			$visible = get_post_meta( $item_id, '_erm_visible', true );
			if ( $visible != 1 ) {
				continue;
			}
			if($item_count > 3){
				break;
			}
			$post = get_post( $item_id );
			$type = get_post_meta( $item_id, '_erm_type', true );

			if ( $type == 'section' ) {
				echo '<li class="erm_section">';
				echo '<h2 class="erm_section_title">' . $post->post_title . '</h2>';
				echo '<div class="erm_section_desc">' . $post->post_content . '</div>';
				echo '</li>';
			} else {
				if ( $type == 'product' ) {
					$has_thumbnail = has_post_thumbnail( $item_id );
					$prices        = get_post_meta( $item_id, '_erm_prices', true );
					$class = '';
					if ( !empty( $prices ) && $prices[0]['name'] != '' ) {
						$class = ' erm_product_active';
					}
					echo '<li class="erm_section">';
					echo '<p class="erm_section_title">' . $post->post_title . '</p>';
					echo '<p class="erm_section_price">' . $prices[0]['value'] . '</p>';
					echo '<div class="erm_section_desc">' . $post->post_content . '</div>';
					echo '</li>';
				}
			}
		}

		echo '</ul>';
		echo '</div>';
	}
	echo '</div>';
	$k ++;
}
echo '</div>';
wp_reset_postdata();
?>
