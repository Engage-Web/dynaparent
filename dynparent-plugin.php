<?php
/*
Plugin Name: Dynaparent - Dynamic Parent Filter
Plugin URI: https://www.engageweb.co.uk/web-services/wordpress-plugin-development
Description: Dynamic filtering of Parent pages
Author: Engage Web, Steven Morris, Nick Arkell
Version: 2.1.0
Author URI: https://www.engageweb.co.uk
License: GPL2
Text Domain: dynaparent
*/

/*************************************************************/

add_action( 'wp_ajax_check_parent', 'dynaparent_ajax_check_parent' );

function dynaparent_ajax_check_parent() {

	$args = array( 's' => $_POST[ 'data' ], 'posts_per_page' => 2000000, 'post_type' => 'page', 'orderby' => 'post_title', 'order' => 'ASC' );

	$the_query = new WP_Query( $args );

	// The Loop

	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$postdata = get_post();
			$postdatasearch = strtolower( $_POST[ 'data' ] );
			$titlesearch = strtolower( get_the_title( $postdata ) );
			if ( str_contains( $titlesearch, $postdatasearch ) ) {
				$parentname[] = $postdata;
			}
		}
	}

	foreach ( $parentname as $parent ) {
		$parent_title = get_the_title( $parent->post_parent );
		if ( $parent_title != '' ) {
			$parent_title = $parent_title . ">><br>";
		}
		$statecheck = get_post( $parent->post_parent );
		$parent_state = get_the_title( $statecheck->post_parent );
		echo "<li class='dynaparentoption'><a class=\"parentlink\" style=\"cursor:pointer;\" parentname='" . $parent->post_title . "' parentid='" . $parent->ID . "'>" . $parent->post_title . "</a></li>\n";
	}

	echo '<script language="javascript" type="text/javascript">
					jQuery(".parentlink").bind(\'click\', function() {
					var parentid = jQuery(this).attr(\'parentid\');
					var parentname = jQuery(this).attr(\'parentname\');
					jQuery(".parentlink").css(\'font-weight\',400);
					jQuery(this).css(\'font-weight\',900);
					//jQuery(".parentlink").parent(\'li\').hide("slow");
					jQuery(this).parent(\'.dynaparentoption\').show();
					jQuery("#parentfilbox").val(parentname);
					jQuery("#parent_id").val(parentid);
					});
			</script>';
	die();
}

function dynaparent_register_head() {
	$url = plugins_url( '/dynaparentstyle.css', __FILE__ );
	echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action( 'admin_head', 'dynaparent_register_head' );

function dynapar_add_theme_menus() {
	
	// Only add meta box if Classic Editor

	$current_screen = get_current_screen();
	$gutenberg = $current_screen->is_block_editor();
	$oldgutenberg = false;
	if (function_exists('is_gutenberg_page')) {
		$oldgutenberg = is_gutenberg_page();
	}
	if (is_admin() && $gutenberg == false && $oldgutenberg == false) {
		add_meta_box( 'pageparentdiv', __( 'Page Attributes' ), 'dynapar_box', 'page', 'side', 'core' );
	}
}

add_action( 'current_screen', 'dynapar_add_theme_menus' );

function dynapar_box( $post ) {
	echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' .
	wp_create_nonce( 'taxonomy_theme' ) . '" />';
	$pluginurl = plugin_dir_path( __FILE__ );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-effects-core' );
	?>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
	</script>
	<?php

	// Get the current parent and menu order value

	global $post;
	$pluginurl = plugins_url( 'ajax-loader.gif', __FILE__ );
	$id = get_the_ID();
	$post_tmp = get_post( $id );
	$parent_page = get_the_title( $post->post_parent );
	$menu_order = $post->menu_order;

	// Populate template list

	$template_file = get_post_meta( get_the_ID(), '_wp_page_template', TRUE );
	$template_file = str_replace( 'page-templates/', '', $template_file );
	$template_file = str_replace( '.php', '', $template_file );
	$themeselect = '<select id="page_template" name="page_template"><option value="default">Default Template</option>';
	$templates = get_page_templates();
	$currtemp = get_post_meta( $post->ID, '_wp_page_template', true );
	foreach ( $templates as $template_name => $template_filename ) {
		if ( $currtemp == $template_filename ) {
			$select = 'selected="selected"';
		} else {
			$select = '';
		}
		$themeselect .= '<option value="' . $template_filename . '" ' . $select . '>' . $template_name . '</option>';
	}
	$themeselect .= '</select>';

	?>
	<p class="post-attributes-label-wrapper page-template-label-wrapper">
		<label class="post-attributes-label" for="page_template">Template</label>
	</p>
	<?php echo $themeselect;?>
	<p class="post-attributes-label-wrapper menu-order-label-wrapper">
		<label class="post-attributes-label" for="menu_order">Order</label>
	</p>
	<input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $menu_order; ?>">
	<p class="post-attributes-label-wrapper parent-id-label-wrapper">
		<label class="post-attributes-label" for="parent_id">Parent</label>
	</p>
	<p>Current Parent: <?php echo $parent_page; ?></p>
	<p>Search for a parent in the box below, click on a new parent from the results and make sure you click the 'Update' button to save this change.</p>
	<p>
		<input type="text" name="filterbox" id="parentfilbox" autocomplete="off" placeholder="Search for new parent..." style="width:100%">
		<br/>
		<span id="new-parent"></span><input type="hidden" name="parent_id" id="parent_id" readonly="readonly" value="<?php echo $post->post_parent; ?>"></p>
	<div id="resultparent">
		<p id="resultparenttitle"><strong>Select new parent...</strong></p>
		<p id="resultparentloader"></p>
		<ul id="resultparentresults">
		</ul>
	</div>
	<p class="post-attributes-help-text">Need help? Use the Help tab above the screen title.</p>
	<script language="javascript" type="text/javascript">
	jQuery(document).ready(function() {
		var timer;
		var checkparent = function () {
			jQuery.post(ajaxurl, {
				'action':'check_parent',
				'data':jQuery("#parentfilbox").val()
			}, function(response){
				jQuery('#resultparenttitle').fadeIn();
				jQuery('#resultparentresults').fadeIn();
				jQuery( "#resultparentresults" ).empty().append(response);
				jQuery("#resultparentloader").empty().append('');
			});
		};
		jQuery("#resultparentloader").empty().append( '<img src="<?php echo $pluginurl; ?>" alt="" class="loading">');
		timer && clearTimeout(timer);
		timer = setTimeout(checkparent, 600);
		jQuery("#parentfilbox").keyup(function() {
			jQuery("#resultparentloader").empty().append( '<img src="<?php echo $pluginurl; ?>" alt="" class="loading">');
			timer && clearTimeout(timer);
			timer = setTimeout(checkparent, 600);
		});		
		jQuery(".parentlink").click(function() {
			var parentid = jQuery(this).attr('parentid');
			var parentname = jQuery(this).attr('parentname');
			jQuery("#parentfilbox").val(parentname);
			jQuery("#new-parent").html(parentname);
			jQuery("#parent_id").val(parentid);
		});
	});
	</script>
	<?php
}
?>