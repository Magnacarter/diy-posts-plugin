<?php
/**
 * Add the DIY box to the content and style it
 *
 */

$settings         = get_option( 'diy-settings' );

$background_color = ( isset( $settings['background_color'] ) && ! empty( $settings['background_color'] ) ) ? $settings['background_color'] : '#fff';

?>

<style type="text/css">

	.diy-posts-container {
		margin-bottom: 40px;
		padding: 30px;
		height: auto;
		width: auto;
		font-size: inherit;
		font-family: inherit;
		background-color: <?php echo esc_attr( $background_color ) ?>;
	}

	.diy-posts-container h2 {
		color: inherit;
	}

	.diy-posts-container ul {
		padding-left: 20px;
	}

	.diy-posts-container ul li {
		padding-bottom: 5px;
		color: inherit;
	}

</style>

<?php

/**
 * Filter the WordPress content and add our DIY custom fields 
 * above the post content
 *
 * @param $content
 *
 * @filter the_content
 *
 * @return $content
 */
function diy_filter_content( $content ) {

	global $post;

	$post_id = $post->ID;

	$custom_fields = get_post_custom( $post_id );

	$time   = ( isset( $custom_fields['diy_time'] ) ) ? $custom_fields['diy_time'] : '';

	$cost   = ( isset( $custom_fields['diy_cost'] ) ) ? $custom_fields['diy_cost'] : '';

	$rating = ( isset( $custom_fields['diy_rating'] ) ) ? $custom_fields['diy_rating'] : '';

	$cfs = array();

	$cfs["Required Time"] = $time;

	$cfs["Job Cost"] = $cost; 

	$cfs["Skill Level"] = $rating;

	$list = array();

	// Build array from user inputs
	foreach ( $cfs as $message => $cf ) :

		if ( $cf!== "null" && $cf !== null && $cf !== "" ) {

			$list[$message] = $cf;

		}

	endforeach;

	if ( ! is_single() ) {

		return;

	}

	// Add DIY post meta to top of content
	// Check all three user inputs to see if they're all empty. If not, render the diy box.
	if ( $list['Required Time'][0] != "" || $list['Job Cost'][0] != "" || $list['Skill Level'][0] != "null" ) {

		ob_start();

	    ?>

	        <div class="diy-posts-container">

	        	<h2>DIY Job Requirements</h2>

	        	<ul>

	        		<?php foreach ( $list as $key => $value ) : ?>

	        			<!-- if a key doesn't have a value, exclude from the li's -->
	        			<?php if ( $value[0] != "" && $value[0] !== "null" ) : ?>

	        				<li><?php echo esc_html( $key ) ?>: <?php echo esc_html( $value[0] ) ?></li>

	        			<?php endif ?>

	        		<?php endforeach ?>

	        	</ul>

	        </div>

	     <?php

	    $diy_box = ob_get_clean();

	    $diy_box .= $content;

		return $diy_box;

	} else {

		return $content;

	}

}
add_filter( 'the_content', 'diy_filter_content' );
