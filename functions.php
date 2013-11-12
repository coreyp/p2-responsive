<?php

// make site private and redirect to registration page

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function admin_redirect() {
	if ( !is_user_logged_in() && !is_page( array( 'register' ) ) && !is_login_page() ) {
	// if ( !is_user_logged_in() && !is_login_page() ) {
		wp_redirect( home_url('/wordpress/wp-login.php?action=register') );
	exit;
	}
	elseif ( is_user_logged_in() && !current_user_can('delete_posts') && !is_page( array( 'new-registry-request' ) ) && !is_login_page() ) {
	// elseif ( !current_user_can('delete_posts') && !is_page( array( 'register', 'new-registry-request' ) ) && !is_login_page() ) {
		wp_redirect( home_url('/room/new-registry-request/') );
	exit;
	} 
}
add_action('get_header', 'admin_redirect');

// deregister styles for new registry request page, per http://www.advancedcustomfields.com/resources/tutorials/creating-a-front-end-form/

add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
 
function my_deregister_styles() {
	wp_deregister_style( 'wp-admin' );
}

// create hooks for new post form, modified, per http://www.advancedcustomfields.com/resources/tutorials/using-acf_form-to-create-a-new-post/

function my_pre_save_post( $post_id )
{
    // check if this is to be a new post
    if( $post_id != 'new' )
    {
        return $post_id;
    }
 
 	// set title for new post
 	// $newtitle = the_field('requester');

    // Create a new post
    $post = array(
        'post_status' => 'draft',
        'post_title' => 'New Request',
        'post_type' => 'requests',
        'post_content' => '<p>Estimated cost: <strong>$[acf field="est_cost"] </strong></p><p>Estimated time commitment: <strong>[acf field="est_time"] hour(s) </strong></p><p>[acf field="request_description"]</p><p><a href="#" class="stripe-connect light-blue"><span>Volunteer</span></a> [ssd amount="3500"]</p>'
    );  
 
    // insert the post
    $post_id = wp_insert_post( $post ); 
 
    // update $_POST['return']
    // $_POST['return'] = add_query_arg( array('post_id' => $post_id), $_POST['return'] );    
 
    // return the new ID
    return $post_id;

}

add_filter('acf/pre_save_post' , 'my_pre_save_post' );

// remove rich post editor 
add_filter('user_can_richedit' , create_function('' , 'return false;') , 50); 

// original p2 child theme functions below

// Modernizr for HTML5 and media query support for older browsers
// FitVids.js for responsive embedded videos
add_action('wp_enqueue_scripts', 'p2_responsive_js');
function p2_responsive_js() {
	wp_enqueue_script('Modernizr', get_template_directory_uri() .'../../p2-responsive/js/modernizr.min.js', '', '2.0.6');
	wp_enqueue_script('FitVids', get_template_directory_uri() .'../../p2-responsive/js/jquery.fitvids.js', array('jquery'), '1.0');
}


// Enable Figure + FigCaption for images and remove width style from caption so it responds well. 
function mytheme_caption( $attr, $content = null ) {
    $output = apply_filters( 'img_caption_shortcode', '', $attr, $content );
    if ( $output != '' )
        return $output;

    extract( shortcode_atts ( array(
    'id' => '',
    'align' => 'alignnone',
    'width'=> '',
    'caption' => ''
    ), $attr ) );

    if ( 1 > (int) $width || empty( $caption ) )
        return $content;

    if ( $id ) $id = 'id="' . $id . '" ';

    return '<figure ' . $id . 'class="wp-caption ' . $align . '" >'
. do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $caption . '</figcaption></figure>';
}

add_shortcode( 'wp_caption', 'mytheme_caption' );
add_shortcode( 'caption', 'mytheme_caption' ); 

// Rewriten p2_comments with span.actions at the bottom
function p2_responsive_comments( $comment, $args ) {
	$GLOBALS['comment'] = $comment;

	if ( !is_single() && get_comment_type() != 'comment' )
		return;

	$depth          = prologue_get_comment_depth( get_comment_ID() );
	$can_edit_post  = current_user_can( 'edit_post', $comment->comment_post_ID );

	$reply_link     = prologue_get_comment_reply_link(
		array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => ' | ', 'reply_text' => __( 'Reply', 'p2' ) ),
		$comment->comment_ID, $comment->comment_post_ID );

	$content_class  = 'commentcontent';
	if ( $can_edit_post )
		$content_class .= ' comment-edit';

	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<?php do_action( 'p2_comment' ); ?>

		<?php echo get_avatar( $comment, 32 ); ?>
		<h4>
			<?php echo get_comment_author_link(); ?>
			<span class="meta">
				<?php echo p2_date_time_with_microformat( 'comment' ); ?>
				
			</span>
		</h4>
		<div id="commentcontent-<?php comment_ID(); ?>" class="<?php echo esc_attr( $content_class ); ?>"><?php
				echo apply_filters( 'comment_text', $comment->comment_content );

				if ( $comment->comment_approved == '0' ): ?>
					<p><em><?php esc_html_e( 'Your comment is awaiting moderation.', 'p2' ); ?></em></p>
				<?php endif; ?>
				
				<span class="actions">
					<a href="<?php echo esc_url( get_comment_link() ); ?>"><?php _e( 'Permalink', 'p2' ); ?></a>
					<?php
					echo $reply_link;

					if ( $can_edit_post )
						edit_comment_link( __( 'Edit', 'p2' ), ' | ' );

					?>
				</span>
		</div>
		
	<?php
}

?>