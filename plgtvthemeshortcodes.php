<?php 
/**
 Plugin Name: Custom Button Shorcode
 Plugin URI: http://tattivitorino.com/
 Description: This plugin adds a download button in the TinyMCE toolbar which opens a popup with a few options for styling the button and also a link to the WP Media Library so you can choose a file to link to it. 
 Version: 1.0
 Author: Tatti Vitorino
 Author URI: http://tattivitorino.com
 * */ 
 
 add_action('init', 'plgtv_themeshortcodes_setup');
 function plgtv_themeshortcodes_setup(){
         
     add_action('admin_init', 'plgtv_themeshortcodes_admin_init');
     
     //Add Shortcode
     add_shortcode('link_btndownload', 'plgtv_add_themeshortcodes');
 } 
 
 
 function plgtv_themeshortcodes_admin_init(){
         
     //add editor styles
     add_action('admin_head', 'plgtv_themeshortcodes_admin_head');
     
     //enqueue admin scripts and styles
     add_action('admin_enqueue_scripts', 'plgtv_themeshortcodes_admin_enqueue_scripts');
     
     //templates that outputs the view inside the wordpress editor.
     add_action('print_media_templates', 'plgtv_themeshortcodes_print_templates');
     
     //js script printed in the footer with the editor button handler handler
     //I am not using this action as I decided to enqueue
     //add_action('admin_print_footer_scripts', 'plgtv_themeshortcodes_footer_scripts', 100);
     
     if(!current_user_can('edit_posts') && !current_user_can('edit_pages')){
         return;
     }
     
     if(get_user_option('rich_editing') == true){
        add_filter('mce_buttons_3', 'plgtv_themeshortcodes_register_buttons');
        add_filter('mce_external_plugins', 'plgtv_themeshortcodes_add_plugin');
     }
 }

 /**
  * Enqueue frontend styles
  * */
 add_action('wp_enqueue_scripts', 'plgtv_themeshortcodes_frontend');
 function plgtv_themeshortcodes_frontend(){
     wp_enqueue_style('shortcodes-frontend', plugins_url('/css', __FILE__).'/styles-frontend.css');
 }


/**
 * Add the shortcode
 * */
function plgtv_add_themeshortcodes($atts, $content = null, $tag){
    $output = FALSE;
    
    switch ($tag) {
            
        case 'link_btndownload':
            extract(shortcode_atts(array(
                'arquivo'=>'#',
                'classes'=>'',
                'ic_classes'=>'',
                'target'=>'_self'
             ), $atts));
             $output .= '<a href="'.$arquivo.'" target="'.$target.'" class="'.$classes.'"><i class="'.$ic_classes.'"></i>';
             if(!is_null($content)){
                 $output .= do_shortcode($content);
             }
             $output .= '</a>'; 
            break;
    }
    
    return $output;
}

 /**
  * Styles for the Editor
  * */
 function plgtv_themeshortcodes_admin_head(){
    add_editor_style(array(
        plugins_url('/css', __FILE__).'/editor-style.css'
    ));
 }
 
 /**
  * Admin enqueue Scripts
  * */
 function plgtv_themeshortcodes_admin_enqueue_scripts(){
     if(!isset(get_current_screen()->id) || (get_current_screen()->base != 'post' && get_current_screen()->base != 'page'))
     return;
     
     //styles applied to the button added in the editor toolbar
     wp_enqueue_style('shortcodes-backend', plugins_url('/css', __FILE__).'/styles-backend.css');
     
     //this js is important to be in the footer as it creates and handles the views in the editor area 
     //this one could also be included using the hook admin_print_footer_scripts
     wp_enqueue_script('shortcode-views-handler', plugins_url('/js', __FILE__).'/views-handler.js', array('jquery', 'media-upload', 'media-views'), false, true);
 }
 
 
  /**
   * This action allows us to change the actual shorcode to the visual object (button) we want in the Editor area
   * The reason I used html data attributes in the templates is so when the user clicks in the edit button the view creates I could grab the shortcode attributes and content values for this specific object. The problem here is when you have more than one of the same shortcode (two or three buttons) when clicking the edit button for one of them the values grabbed would always be the last button`s, which is wrong, so the way I found that worked was including these values with data attributes
   * */
  function plgtv_themeshortcodes_print_templates(){
     if(!isset(get_current_screen()->id) || (get_current_screen()->base != 'post' && get_current_screen()->base != 'page'))
     return;
     ?>
        <script type="text/html" id="tmpl-editor-link_btndownload">
            <a href="{{data.arquivo}}" data-sh="btn" data-sh-arquivo="{{data.arquivo}}" data-sh-innercontent="{{data.innercontent}}" data-sh-classes="{{data.classes}}" data-sh-ic-classes="{{data.ic_classes}}" data-sh-target="{{data.target}}" class="{{data.classes}}"><i class="{{data.ic_classes}}"></i>{{data.innercontent}}</a>
        </script>
     <?php
 }
  


 /**
  * Hook: (add_filter mce_buttons_3)
  * Register the button in the TinyMCE toolbar 
  * here I added the button in the 3rd toolbar 
  * */
 function plgtv_themeshortcodes_register_buttons($buttons){
     array_push($buttons, "link_btndownload");
     return $buttons;
 }



 /**
  * Hook (add_filter mce_external_plugins)
  * Add button plugin 
  * */
 function plgtv_themeshortcodes_add_plugin($plugin_array){
     $plugin_array['link_buttons'] = plugins_url('/js', __FILE__).'/mcebuttons.js';
     return $plugin_array;
 }

