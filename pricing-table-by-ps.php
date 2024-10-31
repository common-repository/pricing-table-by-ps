<?php 
/**
 * Plugin Name: Pricing Table by PS
 * Plugin URI: http://pluginlyspeaking.com/plugins/pricing-table/
 * Description: Build your pricing table or comparison table, select a pre-built layout and display it thanks to a shortcode.
 * Author: PluginlySpeaking
 * Version: 1.0
 * Author URI: http://www.pluginlyspeaking.com
 * License: GPL2
 */

add_action( 'wp_enqueue_scripts', 'pspt_add_script' );

function pspt_add_script() {
	wp_enqueue_style( 'pspt_css', plugins_url('css/pspt.css', __FILE__));
	wp_enqueue_script('jquery');	
}

// Enqueue admin styles
add_action( 'admin_enqueue_scripts', 'pspt_add_admin_style' );
function pspt_add_admin_style() {
	wp_enqueue_style( 'pspt_admin_css', plugins_url('css/pspt_admin.css', __FILE__));
}

// Check for the PRO version
add_action( 'admin_init', 'pspt_free_pro_check' );
function pspt_free_pro_check() {
    if (is_plugin_active('pluginlyspeaking-pricingtablebyps-pro/pluginlyspeaking-pricingtablebyps-pro.php')) {

        function my_admin_notice(){
        echo '<div class="updated">
                <p><strong>Pricing Table PRO</strong> version is activated.</p>
				<p><strong>Pricing Table FREE</strong> version has been desactivated.</p>
              </div>';
        }
        add_action('admin_notices', 'my_admin_notice');

        deactivate_plugins(__FILE__);
    }
}

function pspt_create_type() {
  register_post_type( 'pspt_type',
    array(
      'labels' => array(
        'name' => 'Pricing Table',
        'singular_name' => 'Pricing Table'
      ),
      'public' => true,
      'has_archive' => false,
      'hierarchical' => false,
      'supports'           => array( 'title' ),
      'menu_icon'    => 'dashicons-plus',
    )
  );
}

add_action( 'init', 'pspt_create_type' );


function pspt_admin_css() {
    global $post_type;
    $post_types = array( 
                        'pspt_type',
                  );
    if(in_array($post_type, $post_types))
    echo '<style type="text/css">#edit-slug-box, #post-preview, #view-post-btn{display: none;}</style>';
}

function pspt_remove_view_link( $action ) {

    unset ($action['view']);
    return $action;
}

add_filter( 'post_row_actions', 'pspt_remove_view_link' );
add_action( 'admin_head-post-new.php', 'pspt_admin_css' );
add_action( 'admin_head-post.php', 'pspt_admin_css' );

function pspt_check($cible,$test){
  if($test == $cible){return ' checked="checked" ';}
}

add_action('add_meta_boxes','pspt_init_settings_metabox');

function pspt_init_settings_metabox(){
  add_meta_box('pspt_settings_metabox', 'Settings', 'pspt_add_settings_metabox', 'pspt_type', 'side', 'high');
}

function pspt_add_settings_metabox($post){
	
	$prefix = '_pspt_';
	
	$plugin_output = get_post_meta($post->ID, $prefix.'plugin_output',true);
	$enable_highlight = get_post_meta($post->ID, $prefix.'enable_highlight',true);	
	?>
	<table class="pspt_table">
		<tr>
			<td colspan="2"><label for="plugin_output">How to display your table ? </label>
				<select name="plugin_output" class="pspt_select_100">
					<option <?php selected( $plugin_output, "table_comparison"); ?> id="pspt_table_comparison" value="table_comparison">Table Comparison</option>
					<option <?php selected( $plugin_output, "pricing_table");  ?> id="pspt_pricing_table" value="pricing_table">Pricing Table</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="enable_highlight">Enable Highlighting : </label></td>
			<td><input type="radio" id="enable_highlight_yes" name="enable_highlight" value="yes" <?php echo (empty($enable_highlight)) ? 'checked="checked"' : pspt_check($enable_highlight,'yes'); ?>> Yes <input type="radio" id="enable_highlight_no" name="enable_highlight" value="no" <?php echo (empty($enable_highlight)) ? '' : pspt_check($enable_highlight,'no'); ?>> No<br></td>
		</tr>
	</table>
	
	<script type="text/javascript">
		$=jQuery.noConflict();
		jQuery(document).ready( function($) {
			if($('#pspt_table_comparison').is(':selected')) {
				$('#pspt_layout_table_comparison').show();
				$('#pspt_layout_pricing_table').hide();
			} 
			if($('#pspt_pricing_table').is(':selected')) {
				$('#pspt_layout_table_comparison').hide();
				$('#pspt_layout_pricing_table').show();
			}
			
			$('select[name=plugin_output]').live('change', function(){
				if($('#pspt_table_comparison').is(':selected')) {
				$('#pspt_layout_table_comparison').show();
				$('#pspt_layout_pricing_table').hide();
				} 
				if($('#pspt_pricing_table').is(':selected')) {
					$('#pspt_layout_table_comparison').hide();
					$('#pspt_layout_pricing_table').show();
				}
			});
		});
	</script>
	
	<?php 
	
}

add_action('add_meta_boxes','pspt_init_advert_metabox');

function pspt_init_advert_metabox(){
  add_meta_box('pspt_advert_metabox', 'Upgrade to PRO Version', 'pspt_add_advert_metabox', 'pspt_type', 'side', 'low');
}

function pspt_add_advert_metabox($post){	
	?>
	
	<ul style="list-style-type:disc;padding-left:20px;">
		<li>Unlimited nb of column</li>
		<li>More than 30+ layouts</li>
		<li>Show some tooltips or pre-built icon</li>
		<li>Use your theme's font</li>
		<li>Device restriction</li>
		<li>User restriction</li>
		<li>And more...</li>
	</ul>
	<a style="text-decoration: none;display:inline-block; background:#33b690; padding:8px 25px 8px; border-bottom:3px solid #33a583; border-radius:3px; color:white;" target="_blank" href="http://pluginlyspeaking.com/plugins/pricing-table/">See all PRO features</a>
	<span style="display:block;margin-top:14px; font-size:13px; color:#0073AA; line-height:20px;">
		<span class="dashicons dashicons-tickets"></span> Code <strong>PT10OFF</strong> (10% OFF)
	</span>
	<?php 
	
}

add_action('add_meta_boxes','pspt_init_layout_metabox');

function pspt_init_layout_metabox(){
  add_meta_box('pspt_layout_metabox', 'Select your Table Layout', 'pspt_add_layout_metabox', 'pspt_type', 'normal');
}

function pspt_add_layout_metabox($post){
	
	$prefix = '_pspt_';
	$table_comparison_type_layout = get_post_meta($post->ID, $prefix.'table_comparison_type_layout',true);	
	$table_comparison_layout = get_post_meta($post->ID, $prefix.'table_comparison_layout',true);	
	$pricing_table_type_layout = get_post_meta($post->ID, $prefix.'pricing_table_type_layout',true);	
	$pricing_table_layout = get_post_meta($post->ID, $prefix.'pricing_table_layout',true);	
	
	?>
	
	<div id="pspt_layout_table_comparison">
		<label id="pspt_type_layout_table_comparison" class="pspt_label_title" for="table_comparison_type_layout">Select your layout category  </label><br />
		<select id="pspt_type_layout_table_comparison_select" name="table_comparison_type_layout" class="pspt_select_25">
			<option <?php selected( $table_comparison_type_layout, "full_colored"); ?> id="pspt_type_layout_table_comparison_1" value="full_colored">Full Colored</option>
			<option <?php selected( $table_comparison_type_layout, "standard");  ?> id="pspt_type_layout_table_comparison_2" value="standard">Standard</option>
			<option <?php selected( $table_comparison_type_layout, "light_colored");  ?> id="pspt_type_layout_table_comparison_3" value="light_colored">Light Colored</option>
			<option <?php selected( $table_comparison_type_layout, "other");  ?> id="pspt_type_layout_table_comparison_4" value="other">Others</option>
		</select>
		
		<h5 class="pspt_admin_title">Choose your layout</h5>
		
		<ul id="pspt_layout_table_comparison_list_1" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			<li>
				<input type="radio" id="pspt_layout_table_comparison_11" name="table_comparison_layout" value="pspt_layout_table_comparison_11" <?php echo (empty($table_comparison_layout)) ? 'checked="checked"' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_11'); ?>>
				<label for="pspt_layout_table_comparison_11">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_11.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_12" name="table_comparison_layout" value="pspt_layout_table_comparison_12" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_12'); ?>>
				<label for="pspt_layout_table_comparison_12">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_12.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_13" name="table_comparison_layout" value="pspt_layout_table_comparison_13" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_13'); ?>>
				<label for="pspt_layout_table_comparison_13">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_13.PNG', __FILE__); ?>" > <br />
				</label>
			</li>
		</ul>
		
		<ul id="pspt_layout_table_comparison_list_2" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			<li>
				<input type="radio" id="pspt_layout_table_comparison_24" name="table_comparison_layout" value="pspt_layout_table_comparison_24" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_24'); ?>>
				<label for="pspt_layout_table_comparison_24">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_24.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_25" name="table_comparison_layout" value="pspt_layout_table_comparison_25" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_25'); ?>>
				<label for="pspt_layout_table_comparison_25">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_25.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_26" name="table_comparison_layout" value="pspt_layout_table_comparison_26" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_26'); ?>>
				<label for="pspt_layout_table_comparison_26">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_26.PNG', __FILE__); ?>" > <br />
				</label>
			</li>
		</ul>
		
		<ul id="pspt_layout_table_comparison_list_3" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			<li>
				<input type="radio" id="pspt_layout_table_comparison_37" name="table_comparison_layout" value="pspt_layout_table_comparison_37" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_37'); ?>>
				<label for="pspt_layout_table_comparison_37">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_37.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_38" name="table_comparison_layout" value="pspt_layout_table_comparison_38" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_38'); ?>>
				<label for="pspt_layout_table_comparison_38">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_38.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_39" name="table_comparison_layout" value="pspt_layout_table_comparison_39" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_39'); ?>>
				<label for="pspt_layout_table_comparison_39">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_39.PNG', __FILE__); ?>" > <br />
				</label>
			</li>
		</ul>
		
		<ul id="pspt_layout_table_comparison_list_4" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			<li>
				<input type="radio" id="pspt_layout_table_comparison_44" name="table_comparison_layout" value="pspt_layout_table_comparison_44" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_44'); ?>>
				<label for="pspt_layout_table_comparison_44">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_44.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_42" name="table_comparison_layout" value="pspt_layout_table_comparison_42" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_42'); ?>>
				<label for="pspt_layout_table_comparison_42">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_42.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_table_comparison_45" name="table_comparison_layout" value="pspt_layout_table_comparison_45" <?php echo (empty($table_comparison_layout)) ? '' : pspt_check($table_comparison_layout,'pspt_layout_table_comparison_45'); ?>>
				<label for="pspt_layout_table_comparison_45">
					<img src="<?php echo plugins_url('img/table_comparison_layout/layout_45.PNG', __FILE__); ?>" > 
				</label>
			</li>		
		</ul>
	</div>
	
	<div id="pspt_layout_pricing_table">
		<label id="pspt_type_layout_pricing_table" class="pspt_label_title" for="pricing_table_type_layout">Select your layout category  </label><br />
		<select id="pspt_type_layout_pricing_table_select" name="pricing_table_type_layout" class="pspt_select_25">
			<option <?php selected( $pricing_table_type_layout, "full_colored"); ?> id="pspt_type_layout_pricing_table_1" value="full_colored">Full Colored</option>
			<option <?php selected( $pricing_table_type_layout, "standard");  ?> id="pspt_type_layout_pricing_table_2" value="standard">Standard</option>
			<option <?php selected( $pricing_table_type_layout, "light_colored");  ?> id="pspt_type_layout_pricing_table_3" value="light_colored">Light Colored</option>
			<option <?php selected( $pricing_table_type_layout, "other");  ?> id="pspt_type_layout_pricing_table_4" value="other">Others</option>
		</select>
		
		<h5 class="pspt_admin_title">Choose your layout</h5>
		
		<ul id="pspt_layout_pricing_table_list_1" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			<li>
				<input type="radio" id="pspt_layout_pricing_table_11" name="pricing_table_layout" value="pspt_layout_pricing_table_11" <?php echo (empty($pricing_table_layout)) ? 'checked="checked"' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_11'); ?>>
				<label for="pspt_layout_pricing_table_11">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_11.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_12" name="pricing_table_layout" value="pspt_layout_pricing_table_12" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_12'); ?>>
				<label for="pspt_layout_pricing_table_12">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_12.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_13" name="pricing_table_layout" value="pspt_layout_pricing_table_13" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_13'); ?>>
				<label for="pspt_layout_pricing_table_13">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_13.PNG', __FILE__); ?>" > <br />
				</label>
			</li>
		</ul>
		
		<ul id="pspt_layout_pricing_table_list_2" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			<li>
				<input type="radio" id="pspt_layout_pricing_table_24" name="pricing_table_layout" value="pspt_layout_pricing_table_24" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_24'); ?>>
				<label for="pspt_layout_pricing_table_24">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_24.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_25" name="pricing_table_layout" value="pspt_layout_pricing_table_25" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_25'); ?>>
				<label for="pspt_layout_pricing_table_25">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_25.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_26" name="pricing_table_layout" value="pspt_layout_pricing_table_26" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_26'); ?>>
				<label for="pspt_layout_pricing_table_26">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_26.PNG', __FILE__); ?>" > <br />
				</label>
			</li>
		</ul>
		
		<ul id="pspt_layout_pricing_table_list_3" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			<li>
				<input type="radio" id="pspt_layout_pricing_table_37" name="pricing_table_layout" value="pspt_layout_pricing_table_37" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_37'); ?>>
				<label for="pspt_layout_pricing_table_37">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_37.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_38" name="pricing_table_layout" value="pspt_layout_pricing_table_38" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_38'); ?>>
				<label for="pspt_layout_pricing_table_38">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_38.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_39" name="pricing_table_layout" value="pspt_layout_pricing_table_39" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_39'); ?>>
				<label for="pspt_layout_pricing_table_39">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_39.PNG', __FILE__); ?>" > <br />
				</label>
			</li>
		</ul>
		
		<ul id="pspt_layout_pricing_table_list_4" class="pspt_w_li_31 pspt_ul_layout" style="display: none;">
			
			<li>
				<input type="radio" id="pspt_layout_pricing_table_44" name="pricing_table_layout" value="pspt_layout_pricing_table_44" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_44'); ?>>
				<label for="pspt_layout_pricing_table_44">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_44.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_42" name="pricing_table_layout" value="pspt_layout_pricing_table_42" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_42'); ?>>
				<label for="pspt_layout_pricing_table_42">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_42.PNG', __FILE__); ?>" > 
				</label>
			</li>
			<li>
				<input type="radio" id="pspt_layout_pricing_table_45" name="pricing_table_layout" value="pspt_layout_pricing_table_45" <?php echo (empty($pricing_table_layout)) ? '' : pspt_check($pricing_table_layout,'pspt_layout_pricing_table_45'); ?>>
				<label for="pspt_layout_pricing_table_45">
					<img src="<?php echo plugins_url('img/pricing_table_layout/layout_45.PNG', __FILE__); ?>" > 
				</label>
			</li>
		</ul>
	</div>
	
	<script type="text/javascript">
		$=jQuery.noConflict();
		jQuery(document).ready( function($) {
			
			if($('#pspt_type_layout_table_comparison_1').is(':selected')) {
				$('#pspt_layout_table_comparison_list_1').show();
				$('#pspt_layout_table_comparison_list_2').hide();
				$('#pspt_layout_table_comparison_list_3').hide();
				$('#pspt_layout_table_comparison_list_4').hide();
			}
			if($('#pspt_type_layout_table_comparison_2').is(':selected')) {
				$('#pspt_layout_table_comparison_list_1').hide();					
				$('#pspt_layout_table_comparison_list_2').show();
				$('#pspt_layout_table_comparison_list_3').hide();
				$('#pspt_layout_table_comparison_list_4').hide();
			}
			if($('#pspt_type_layout_table_comparison_3').is(':selected')) {
				$('#pspt_layout_table_comparison_list_1').hide();
				$('#pspt_layout_table_comparison_list_2').hide();
				$('#pspt_layout_table_comparison_list_3').show();
				$('#pspt_layout_table_comparison_list_4').hide();
			}
			if($('#pspt_type_layout_table_comparison_4').is(':selected')) {
				$('#pspt_layout_table_comparison_list_1').hide();
				$('#pspt_layout_table_comparison_list_2').hide();
				$('#pspt_layout_table_comparison_list_3').hide();
				$('#pspt_layout_table_comparison_list_4').show();
			}
			
			if($('#pspt_type_layout_pricing_table_1').is(':selected')) {
				$('#pspt_layout_pricing_table_list_1').show();
				$('#pspt_layout_pricing_table_list_2').hide();
				$('#pspt_layout_pricing_table_list_3').hide();
				$('#pspt_layout_pricing_table_list_4').hide();
			}
			if($('#pspt_type_layout_pricing_table_2').is(':selected')) {
				$('#pspt_layout_pricing_table_list_1').hide();					
				$('#pspt_layout_pricing_table_list_2').show();
				$('#pspt_layout_pricing_table_list_3').hide();
				$('#pspt_layout_pricing_table_list_4').hide();
			}
			if($('#pspt_type_layout_pricing_table_3').is(':selected')) {
				$('#pspt_layout_pricing_table_list_1').hide();
				$('#pspt_layout_pricing_table_list_2').hide();
				$('#pspt_layout_pricing_table_list_3').show();
				$('#pspt_layout_pricing_table_list_4').hide();
			}
			if($('#pspt_type_layout_pricing_table_4').is(':selected')) {
				$('#pspt_layout_pricing_table_list_1').hide();
				$('#pspt_layout_pricing_table_list_2').hide();
				$('#pspt_layout_pricing_table_list_3').hide();
				$('#pspt_layout_pricing_table_list_4').show();
			}
			
			$('select[name=table_comparison_type_layout]').live('change', function(){
				if($('#pspt_type_layout_table_comparison_1').is(':selected')) {
					$('#pspt_layout_table_comparison_list_1').show();
					$('#pspt_layout_table_comparison_list_2').hide();
					$('#pspt_layout_table_comparison_list_3').hide();
					$('#pspt_layout_table_comparison_list_4').hide();
				}
				if($('#pspt_type_layout_table_comparison_2').is(':selected')) {
					$('#pspt_layout_table_comparison_list_1').hide();					
					$('#pspt_layout_table_comparison_list_2').show();
					$('#pspt_layout_table_comparison_list_3').hide();
					$('#pspt_layout_table_comparison_list_4').hide();
				}
				if($('#pspt_type_layout_table_comparison_3').is(':selected')) {
					$('#pspt_layout_table_comparison_list_1').hide();
					$('#pspt_layout_table_comparison_list_2').hide();
					$('#pspt_layout_table_comparison_list_3').show();
					$('#pspt_layout_table_comparison_list_4').hide();
				}
				if($('#pspt_type_layout_table_comparison_4').is(':selected')) {
					$('#pspt_layout_table_comparison_list_1').hide();
					$('#pspt_layout_table_comparison_list_2').hide();
					$('#pspt_layout_table_comparison_list_3').hide();
					$('#pspt_layout_table_comparison_list_4').show();
				}
			});
			
			$('select[name=pricing_table_type_layout]').live('change', function(){
				if($('#pspt_type_layout_pricing_table_1').is(':selected')) {
					$('#pspt_layout_pricing_table_list_1').show();
					$('#pspt_layout_pricing_table_list_2').hide();
					$('#pspt_layout_pricing_table_list_3').hide();
					$('#pspt_layout_pricing_table_list_4').hide();
				}
				if($('#pspt_type_layout_pricing_table_2').is(':selected')) {
					$('#pspt_layout_pricing_table_list_1').hide();					
					$('#pspt_layout_pricing_table_list_2').show();
					$('#pspt_layout_pricing_table_list_3').hide();
					$('#pspt_layout_pricing_table_list_4').hide();
				}
				if($('#pspt_type_layout_pricing_table_3').is(':selected')) {
					$('#pspt_layout_pricing_table_list_1').hide();
					$('#pspt_layout_pricing_table_list_2').hide();
					$('#pspt_layout_pricing_table_list_3').show();
					$('#pspt_layout_pricing_table_list_4').hide();
				}
				if($('#pspt_type_layout_pricing_table_4').is(':selected')) {
					$('#pspt_layout_pricing_table_list_1').hide();
					$('#pspt_layout_pricing_table_list_2').hide();
					$('#pspt_layout_pricing_table_list_3').hide();
					$('#pspt_layout_pricing_table_list_4').show();
				}
			});
		});
	</script>
	
	<?php 
	
}

add_action('add_meta_boxes','pspt_init_advanced_tools_metabox');

function pspt_init_advanced_tools_metabox(){
  add_meta_box('pspt_advanced_tools_metabox', 'Advanced Pricing Table tools', 'pspt_add_advanced_tools_metabox', 'pspt_type', 'normal');
  add_filter( "postbox_classes_pspt_type_pspt_advanced_tools_metabox", 'pspt_minify_my_metabox' );
}

function pspt_add_advanced_tools_metabox($post){
	?>
	
	<table id="advanced_tools_table">
		<tr>
			<th>Shortcode</th>
			<th>Description</th>
			<th>Example</th>
			<th>More Info</th>
		</tr>
		<tr>
			<td>
				<p>[pspt_url url="" text="" target=""]</p>
			</td>
			<td>
				<p>Display a standard link.</p>
			</td>
			<td>
				<p>[pspt_url url="http://www.google.com" text="Click Me" target="blank"]</p>
			</td>
			<td>
				<p><strong>Field url :</strong> Start with "http" to leave your website.</p>
				<p><strong>Field target :</strong> Use "blank" for a new tab. Use "self" to replace the current page.</p>
			</td>
		</tr>
		<tr>
			<td>
				<p>[pspt_image src="" alt="" height="" width =""]</p>
			</td>
			<td>
				<p>Display an image.</p>
			</td>
			<td>
				<p>[pspt_image src="http://your_source/image.jpg" alt="opt_alt" height="100" width ="100"]</p>
			</td>
			<td>
				<p><strong>Field height and width :</strong> Do not specify "px".</p>
			</td>
		</tr>
		<tr>
			<td>
				<p>[pspt_text text="" size="" color=""]</p>
			</td>
			<td>
				<p>Display a text with some easy css.</p>
			</td>
			<td>
				<p>[pspt_text text="New Title" size="12" color="red"]</p>
			</td>
			<td>
				<p><strong>Field size :</strong> Do not specify "pt".</p>
				<p><strong>Field color :</strong> It can be a word, hexa or rgb color.</p>
			</td>
		</tr>
		<tr>
			<td>
				<p>[pspt_button text="" url="" target=""]</p>
			</td>
			<td>
				<p>Display a customized button for each layout.</p>
			</td>
			<td>
				<p>[pspt_button text="Purchase" url="http://www.mysite.com/purchase_url" target="blank"]</p>
			</td>
			<td>
				<p><strong>Field text :</strong> Write the visible text.</p>
				<p><strong>Field url :</strong> Start with "http" to leave your website.</p>
				<p><strong>Field target :</strong> Use "blank" for a new tab. Use "self" to replace the current page.</p>
			</td>
		</tr>
	</table>
	
	<?php
}

function pspt_minify_my_metabox( $classes ) {
  array_push( $classes, 'closed' );
  return $classes;
}

add_action('add_meta_boxes','pspt_init_table_metabox');

function pspt_init_table_metabox(){
  add_meta_box('pspt_table_metabox', 'Build your table', 'pspt_add_table_metabox', 'pspt_type', 'normal');
}

function pspt_add_table_metabox($post){
	
	global $wpdb;
	$prefix = '_pspt_';

	$hidden_nb_col = get_post_meta($post->ID, $prefix.'hidden_nb_col',true);
	$pspt_table_col_0 = get_post_meta($post->ID, $prefix.'pspt_table_col_0',true);
	$radio_highlight = get_post_meta($post->ID, $prefix.'radio_highlight',true);			
	if ($hidden_nb_col == "")
		$hidden_nb_col = 2;
	for ($i = 1; $i <= $hidden_nb_col; $i++) {
		${"pspt_table_col_" . $i} = get_post_meta($post->ID, $prefix.'pspt_table_col_'.$i.'',true);
	}
	$i = 0;
	$j = 0;

	echo '<table id="pspt_build_table">';
	?>
	<tr class="pspt_row pspt_first_row">
		<td class="pspt_td_button nb_column">
			<a class="pspt_del_col"></a>
		</td>
		<td class="pspt_td_button nb_column">
			<a class="pspt_del_col"></a>
		</td>
		<?php
		for ($i = 1; $i <= $hidden_nb_col; $i++) {
			?>
			<td class="pspt_td_button nb_column">
				<a class="pspt_del_col" href="javascript:void(0);" ><img title="Delete a column" src="<?php echo plugins_url('img/table_column_delete.png', __FILE__); ?>" width="32" height="32" /></a>
			</td>
			<?php
		}
		?>
	</tr>
	
	
	<?php
	if($pspt_table_col_1 != "" && count( $pspt_table_col_1 ) > 0)
	{
		?>
		<tr class="pspt_row pspt_second_row">
			<td class="pspt_td_button"></td>
			<td class="pspt_td_title"><p>Highlight</p></td>
			<?php
			for ($i = 1; $i <= $hidden_nb_col; $i++) {
				?>
				<td class="pspt_td_thin">
					<input type="radio" id="" class="" name="radio_highlight" value="pspt_highlight_<?php echo $i; ?>" <?php echo (empty($radio_highlight)) ? '' : pspt_check($radio_highlight,'pspt_highlight_' . $i . ''); ?> />
				</td>
				<?php
			}
			?>
		</tr>
		<?php
		
		$row_counter = 1;
		foreach ($pspt_table_col_1 as $k => $thing) {
			?>
			<tr class="pspt_row pspt_other_row">
				<?php
				if($row_counter == 1)
				{
				?>
					<td class="pspt_td_button"></td>
					<td class="pspt_td_title"><p>Plan Name</p></td>
				<?php	
				}
				
				if($row_counter == 2)
				{
				?>
					<td class="pspt_td_button"></td>
					<td class="pspt_td_title"><p>Price</p></td>
				<?php	
				}
				
				if($row_counter == 3)
				{
				?>
					<td class="pspt_td_button"></td>
					<td class="pspt_td_title"><p>Subtitle</p></td>
				<?php	
				}
				
				if($row_counter > 3)
				{
				?>
					<td class="pspt_td_button">
						<a class="pspt_del_row" href="javascript:void(0);"><img title="Delete a row" src="<?php echo plugins_url('img/table_row_delete.png', __FILE__); ?>" width="32" height="32" /></a>
					</td>
				<?php	
				}
				
				for ($i = 1; $i <= $hidden_nb_col; $i++) {
					${"pspt_table_col_" . $i . "_c"} = ${"pspt_table_col_" . $i}[$k];
					if($row_counter > 3 && $i == 1)
					{
						$pspt_table_col_0_c = $pspt_table_col_0[$k-3];
						?>
							<td class="pspt_td_title">
								<input type="text" id="" class="" name="pspt_table_col_0[]" placeholder="Feature Name*" value="<?php echo $pspt_table_col_0_c; ?>" />
							</td>
						<?php	
					}
					?>
						<td class="pspt_td_thin">
							<input type="text" id="" class="" name="<?php echo "pspt_table_col_".$i."[]";?>" value="<?php echo ${"pspt_table_col_" . $i . "_c"}; ?>" />
						</td>
					<?php
							
				}
				?>
			</tr>
			<?php
			$row_counter++;
		}			
	}else{
		?>
	
		<tr class="pspt_row pspt_second_row">
			<td class="pspt_td_button">
				
			</td>
			<td class="pspt_td_title">
				<p>Highlight</p>
			</td>
			<td class="pspt_td_thin">
				<input type="radio" id="" class="" name="radio_highlight" value="pspt_highlight_1" <?php echo (empty($radio_highlight)) ? 'checked="checked"' : pspt_check($radio_highlight,'pspt_highlight_1'); ?> />
			</td>
			<td class="pspt_td_thin">
				<input type="radio" id="" class="" name="radio_highlight" value="pspt_highlight_2" <?php echo (empty($radio_highlight)) ? '' : pspt_check($radio_highlight,'pspt_highlight_2'); ?> />
			</td>
		</tr>
		<tr class="pspt_row pspt_other_row">
			<td class="pspt_td_button">
				
			</td>
			<td class="pspt_td_title">
				<p>Plan Name</p>
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_1[]" />
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_2[]" />
			</td>
		</tr>
		<tr class="pspt_row pspt_other_row">
			<td class="pspt_td_button">
				
			</td>
			<td class="pspt_td_title">
				<p>Price</p>
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_1[]" />
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_2[]" />
			</td>
		</tr>
		<tr class="pspt_row pspt_other_row">
			<td class="pspt_td_button">
				
			</td>
			<td class="pspt_td_title">
				<p>Subtitle</p>
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_1[]" />
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_2[]" />
			</td>
		</tr>
		<tr class="pspt_row pspt_other_row">
			<td class="pspt_td_button">
				<a class="pspt_del_row" href="javascript:void(0);"><img title="Delete a row" src="<?php echo plugins_url('img/table_row_delete.png', __FILE__); ?>" width="32" height="32" /></a>
			</td>
			<td class="pspt_td_title">
				<input type="text" id="" class="" name="pspt_table_col_0[]" placeholder="Feature Name*" />
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_1[]" />
			</td>
			<td class="pspt_td_thin">
				<input type="text" id="" class="" name="pspt_table_col_2[]" />
			</td>
		</tr>
	
	<?php	
	} ?>
	
	<?php
	echo '</table>';
	?>
	
	<input id="pspt_hidden_nb_col" name="hidden_nb_col" type="hidden" value="<?php echo $hidden_nb_col; ?>"/>
	
	<p class="pspt_desc">* The Feature Name is only used for the Table Comparison.</p>
	<p class="pspt_desc_center">The FREE version is limited to a maximum of 3 Plans.</p>
	
	
	<!-- lien ajout -->
	<p class="pspt_link_add">
	<a id="pspt_add_row" style="margin-top: 10px; position: relative; display: inline-block;" href="javascript:void(0);"><img title="Add a row" src="<?php echo plugins_url('img/table_row_add.png', __FILE__); ?>" width="32" height="32" /></a>
	<a id="pspt_add_column" style="margin-top: 10px; position: relative; display: inline-block;" href="javascript:void(0);"><img title="Add a column" src="<?php echo plugins_url('img/table_column_add.png', __FILE__); ?>" width="32" height="32" /></a>
	<a id="pspt_reset_all" style="margin-top: 10px; position: relative; display: inline-block;" href="javascript:void(0);"><img title="Erase all the data" src="<?php echo plugins_url('img/table_new.png', __FILE__); ?>" width="32" height="32" /></a>
	</p>
	<!-- script-->
	<script type="text/javascript">// <![CDATA[
	$=jQuery.noConflict();
	jQuery(document).ready(function($){

		//suppresion champ
		function remove_chose(){
			$('.pspt_del_row').on('click',function(){
				if($('.pspt_row').length > 6)
				{
					$(this).parent().parent().remove();
				}
			});
			$('.pspt_first_row .pspt_del_col').on('click',function(){
				if($('.nb_column').length > 3)
				{
					var column_nb = $('.pspt_first_row .pspt_del_col').index(this);
					if(column_nb != -1)
					{
						$( ".pspt_row" ).each(function() {
						  $('td:eq(' + column_nb + ')', this).remove();
						});
						
						$( ".pspt_second_row" ).each(function() {
							var highlight_number = 1;
						  $( "input:radio[name=radio_highlight]", this ).each(function() {
							  $(this).val('pspt_highlight_' + highlight_number + '');
							  highlight_number = highlight_number + 1;
							});
						});
						
						$( ".pspt_other_row" ).each(function() {
							var test_number = 1;
						  $( "td.pspt_td_thin input", this ).each(function() {
							  $(this).attr('name','pspt_table_col_' + test_number + '[]');
							  test_number = test_number + 1;
							});
						});
						
						$('#pspt_hidden_nb_col').val($('.pspt_other_row:last .pspt_td_thin').length);
					}
				}
			});
		}
		remove_chose();

		//ajout champ
		$('#pspt_add_row').on('click',function(){
			$('.pspt_row:last').clone().appendTo('#pspt_build_table');
			$('.pspt_row:last input').val('');
			remove_chose();
		});
		
		$('#pspt_add_column').on('click',function(){
			if($('.nb_column').length < 5)
			{
				$('#pspt_build_table tr').append($("<td class='pspt_td_thin'>"));
				$('.pspt_first_row td:last').addClass( "nb_column" );
				$('.pspt_first_row td:last').html('<a class="pspt_del_col" href="javascript:void(0);" ><img title="Delete a column" src="<?php echo plugins_url('img/table_column_delete.png', __FILE__); ?>" width="32" height="32" /></a>');
				$('.pspt_second_row td:last').html('<input type="radio" id="" class="" name="radio_highlight" value=""/>');
				$( ".pspt_second_row" ).each(function() {
					var highlight_number = 1;
				  $( "input:radio[name=radio_highlight]", this ).each(function() {
					  $(this).val('pspt_highlight_' + highlight_number + '');
					  highlight_number = highlight_number + 1;
					});
				});
				
				$( ".pspt_other_row" ).each(function() {
				  $('td:last', this).html('<input type="text" id="" />');
				});
				
				$( ".pspt_other_row" ).each(function() {
					var test_number = 1;
				  $( "td.pspt_td_thin input", this ).each(function() {
					  $(this).attr('name','pspt_table_col_' + test_number + '[]');
					  test_number = test_number + 1;
					});
				});
				
				$('#pspt_hidden_nb_col').val($('.pspt_other_row:last .pspt_td_thin').length);
				
				remove_chose();
			}
		});
		
		$('#pspt_reset_all').on('click',function(){
			var r = confirm("You will erase all the data.\n Are you sure ?");
			if (r == true) {
				$('.pspt_row input').val('');
			} else {
			} 			
			remove_chose();
		});
		
		
	});

	// ]]></script>
	<?php
}	

add_action('save_post','pspt_save_metabox');
function pspt_save_metabox($post_id){
	
	$prefix = '_pspt_';
	
	//Metabox Settings
	if(isset($_POST['plugin_output'])){
		update_post_meta($post_id, $prefix.'plugin_output', sanitize_text_field($_POST['plugin_output']));
	}
	if(isset($_POST['enable_highlight'])){
		update_post_meta($post_id, $prefix.'enable_highlight', sanitize_text_field($_POST['enable_highlight']));
	}

	if(isset($_POST['table_comparison_type_layout'])){
		update_post_meta($post_id, $prefix.'table_comparison_type_layout', sanitize_text_field($_POST['table_comparison_type_layout']));
	}
	if(isset($_POST['table_comparison_layout'])){
		update_post_meta($post_id, $prefix.'table_comparison_layout', sanitize_text_field($_POST['table_comparison_layout']));
	}
	if(isset($_POST['pricing_table_type_layout'])){
		update_post_meta($post_id, $prefix.'pricing_table_type_layout', sanitize_text_field($_POST['pricing_table_type_layout']));
	}
	if(isset($_POST['pricing_table_layout'])){
		update_post_meta($post_id, $prefix.'pricing_table_layout', sanitize_text_field($_POST['pricing_table_layout']));
	}
	
	if(isset($_POST['radio_highlight'])){
		update_post_meta($post_id, $prefix.'radio_highlight', sanitize_text_field($_POST['radio_highlight']));
	}
	if(isset($_POST['hidden_nb_col'])){
		update_post_meta($post_id, $prefix.'hidden_nb_col', esc_html($_POST['hidden_nb_col']));
	}
	if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {
		for ($i = 1; $i <= get_post_meta($post_id, $prefix.'hidden_nb_col',true); $i++) {
			if( isset($_POST['pspt_table_col_'.$i.'']))
			{
				update_post_meta( $post_id, $prefix.'pspt_table_col_'.$i.'', $_POST['pspt_table_col_'.$i.''] );
			}
		}
		if( isset($_POST['pspt_table_col_0']))
		{
			update_post_meta( $post_id, $prefix.'pspt_table_col_0', $_POST['pspt_table_col_0'] );
		}
	}	
}

add_action( 'manage_pspt_type_posts_custom_column' , 'pspt_custom_columns', 10, 2 );

function pspt_custom_columns( $column, $post_id ) {
    switch ( $column ) {
	case 'shortcode' :
		global $post;
		$pre_slug = '' ;
		$pre_slug = $post->post_title;
		$slug = sanitize_title($pre_slug);
    	$shortcode = '<span style="border: solid 3px lightgray; background:white; padding:7px; font-size:17px; line-height:40px;">[ps_pricingtable name="'.$slug.'"]</strong>';
	    echo $shortcode; 
	    break;
    }
}

function pspt_add_columns($columns) {
    return array_merge($columns, 
              array('shortcode' => __('Shortcode'),
                    ));
}
add_filter('manage_pspt_type_posts_columns' , 'pspt_add_columns');


function pspt_shortcode($atts) {
	extract(shortcode_atts(array(
		"name" => ''
	), $atts));
		
	global $post;
    $args = array('post_type' => 'pspt_type', 'numberposts'=>-1);
    $custom_posts = get_posts($args);
	$output = '';
	foreach($custom_posts as $post) : setup_postdata($post);
	$sanitize_title = sanitize_title($post->post_title);
	if ($sanitize_title == $name)
	{
		$postid = get_the_ID();	
	   
		$prefix = '_pspt_';	
		
		$enable_highlight = get_post_meta($post->ID, $prefix.'enable_highlight',true);	
		if($enable_highlight == "")
			$enable_highlight = "no";	
		
		$highlight_number = 0;		
		if($enable_highlight == 'yes')
		{
			$radio_highlight = get_post_meta($post->ID, $prefix.'radio_highlight',true);
			if($radio_highlight == "")
				$radio_highlight = "pspt_highlight_2";	
			$highlight_number = substr($radio_highlight, -1);
		}		
		
		$plugin_output = get_post_meta($post->ID, $prefix.'plugin_output',true);
		
		if($plugin_output == 'table_comparison')
		{
			$table_comparison_layout = get_post_meta($post->ID, $prefix.'table_comparison_layout',true);	
			$pspt_table_col_0 = get_post_meta($post->ID, $prefix.'pspt_table_col_0',true);
		}
		if($plugin_output == 'pricing_table')
		{
			$pricing_table_layout = get_post_meta($post->ID, $prefix.'pricing_table_layout',true);	
		}		
		
		$hidden_nb_col = get_post_meta($post->ID, $prefix.'hidden_nb_col',true);
		for ($i = 1; $i <= $hidden_nb_col; $i++) {
			${"pspt_table_col_" . $i} = get_post_meta($post->ID, $prefix.'pspt_table_col_'.$i.'',true);
		}
			
		$output = '';	
		if($plugin_output == 'table_comparison')
		{
			$output .= '<table id="pspt_id_'.$postid.'" class="pspt_table_comparison_output '.$table_comparison_layout.'">';
			$counter_foreach = 0;
			$len_counter_foreach = count($pspt_table_col_1);
			foreach ($pspt_table_col_1 as $k => $thing) {
				if ($counter_foreach == $len_counter_foreach - 1)
					$output .= '<tr class="pspt_last_tr">';
				else
					$output .= '<tr>';
				
				for ($i = 1; $i <= $hidden_nb_col; $i++) {
					${"pspt_table_col_" . $i . "_c"} = do_shortcode(${"pspt_table_col_" . $i}[$k]);

					switch ($k) {
						case 0:
							if($i == 1)
							{
								$output .= '<td class="pspt_no_features" >';
								$output .= '</td>';
							}
							if ($i == $highlight_number)
								$output .= '<td class="pspt_package_name pspt_col_highlight" >';
							else
								$output .= '<td class="pspt_package_name" >';
							
								$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
							$output .= '</td>';
							break;
						case 1:
							if($i == 1)
							{
								$output .= '<td class="pspt_no_features" >';
								$output .= '</td>';
							}
							if ($i == $highlight_number)
								$output .= '<td class="pspt_price pspt_col_highlight" >';
							else
								$output .= '<td class="pspt_price" >';
							
								$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
							$output .= '</td>';
							break;
						case 2:
							if($i == 1)
							{
								$output .= '<td class="pspt_no_features" >';
								$output .= '</td>';
							}
							if ($i == $highlight_number)
								$output .= '<td class="pspt_subtitle pspt_col_highlight" >';
							else
								$output .= '<td class="pspt_subtitle" >';
							
								$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
							$output .= '</td>';
							break;
						default:
							if($i == 1)
							{
								$output .= '<td class="pspt_features_name" >';
									$output .= '<span>'.$pspt_table_col_0[$k-3].'</span>';
								$output .= '</td>';
							}
							if ($i == $highlight_number)
								$output .= '<td class="pspt_features pspt_col_highlight" >';
							else
								$output .= '<td class="pspt_features" >';
							
								$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
							$output .= '</td>';
					}			
				}
				$output .= '</tr>';
				$counter_foreach++;
			}
			$output .= '</table>';
		}
		
		if($plugin_output == 'pricing_table')
		{
			$output .= '<table id="pspt_id_'.$postid.'" class="pspt_pricing_table_output '.$pricing_table_layout.'">';
			$counter_foreach = 0;
			$len_counter_foreach = count($pspt_table_col_1);
			foreach ($pspt_table_col_1 as $k => $thing) {
				if ($counter_foreach == $len_counter_foreach - 1)
					$output .= '<tr class="pspt_last_tr">';
				else
					$output .= '<tr>';
				
				for ($i = 1; $i <= $hidden_nb_col; $i++) {
					${"pspt_table_col_" . $i . "_c"} = do_shortcode(${"pspt_table_col_" . $i}[$k]);

						switch ($k) {
							case 0:
								if ($i == $highlight_number)
									$output .= '<td class="pspt_package_name pspt_col_highlight" >';
								else
									$output .= '<td class="pspt_package_name" >';
								
									$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
								$output .= '</td>';
								break;
							case 1:
								if ($i == $highlight_number)
									$output .= '<td class="pspt_price pspt_col_highlight" >';
								else
									$output .= '<td class="pspt_price" >';
								
									$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
								$output .= '</td>';
								break;
							case 2:
								if ($i == $highlight_number)
									$output .= '<td class="pspt_subtitle pspt_col_highlight" >';
								else
									$output .= '<td class="pspt_subtitle" >';
								
									$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
								$output .= '</td>';
								break;
							default:
								if ($i == $highlight_number)
									$output .= '<td class="pspt_features pspt_col_highlight" >';
								else
									$output .= '<td class="pspt_features" >';
								
									$output .= '<span>'.${"pspt_table_col_" . $i . "_c"}.'</span>';
								$output .= '</td>';
						}
						if ($i < $hidden_nb_col)
						{
							$output .= '<td class="pspt_between_pt">';
							$output .= '</td>';
						}
					}
				$output .= '</tr>';
				$counter_foreach++;
			}
			$output .= '</table>';
		}
	}
	endforeach; wp_reset_query();
	return $output;
}
add_shortcode( 'ps_pricingtable', 'pspt_shortcode' );

// Creation of the Shortcode "URL"
add_shortcode( 'pspt_url', 'pspt_shortcode_url' );
function pspt_shortcode_url($atts) {

	extract(shortcode_atts(array(	"url" => '', "text" => '', "target" => 'self'), $atts));

	$output = '<a href="'.$url.'" target="_'.$target.'">'.$text.'</a>';

	return $output;

}

// Creation of the Shortcode "Image"
add_shortcode( 'pspt_image', 'pspt_shortcode_image' );
function pspt_shortcode_image($atts) {

	extract(shortcode_atts(array(	"src" => '', "alt" => '', "height" => '', "width" => ''), $atts));

	$output = '<img src="'.$src.'" alt="'.$alt.'" height="'.$height.'" width="'.$width.'"> ';


	return $output;

}

// Creation of the Shortcode "Text"
add_shortcode( 'pspt_text', 'pspt_shortcode_text' );
function pspt_shortcode_text($atts) {

	extract(shortcode_atts(array(	"text" => '', "size" => '10', "color" => 'black'), $atts));

	$output = '<span style="font-size:'.$size.'pt;color:'.$color.';">'.$text.'</span>';


	return $output;

}

// Creation of the Shortcode "Button"
add_shortcode( 'pspt_button', 'pspt_shortcode_button' );
function pspt_shortcode_button($atts) {

	extract(shortcode_atts(array(	"text" => '', "url" => '#', "target" => 'self'), $atts));

	$output = '<a class="pspt_button" href="'.$url.'" target="_'.$target.'">'.$text.'</a>';

	return $output;

}

	
?>