<?php
/*
 Plugin Name: 2018 World Cup News
 Description: Widgets that displays latest new from fifa world cup 2018 Russia
 Version: 1.0.0
 Author: Ujwol Bastakoti
 Author URI:https://ujwolbastakoti.wordpress.com/
 License: GPLv2
 */

class world_cup_2018_news extends WP_Widget{
    
    public function __construct() {
        parent::__construct(
            '2018-world-cup-news', // Base ID
            '2018 World Cup  News', // Name
            array( 'description' => __( 'World cup 2018 News Widget', 'text_domain' ), ) // Args
            );
       
        wp_enqueue_script('worldCupNewsJs',plugins_url('worldcup2018_script.js',__FILE__ ), array('jquery'));
        wp_localize_script( 'worldCupNewsJs', 'my_ajax_url', admin_url( 'admin-ajax.php' ) );
       
        
            /* AJAX Callback */
        add_action( 'wp_ajax_update_worldcup_news_ajax', array($this, 'update_worldcup_news_ajax'));
            
            
        add_action( 'wp_ajax_nopriv_update_worldcup_news_ajax',array($this,'update_worldcup_news_ajax'));
    }//end of function construct
    
    
    //function handle ajax request admin and non priv both
    public function update_worldcup_news_ajax(){
        
        $this::widget_area_content();
        wp_die();
        
        
    }
    
    
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'World Cup 2018 News', 'text_domain' );
        }
        
        
        ?>
        
        
        	<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		    </p>
		    
		      
		      <p>
				<label for="<?php echo $this->get_field_id( 'worldcup_news_dimension' ); ?>"><?php _e( 'Widget Dimension: ' ); ?></label> 
				<br/>
				<span>Width:</span><input style="width:80px !important;" id="<?php echo $this->get_field_id( 'worldcup_news_widget_width' ); ?>" name="<?php echo $this->get_field_name( 'worldcup_news_widget_width' ); ?>" type="text" value="<?php if(isset($instance['worldcup_news_widget_width'])){ echo esc_attr( $instance['worldcup_news_widget_width' ] ); } ?>" />&nbsp;X&nbsp;
				<span>Height:</span><input style="width:80px !important;"  id="<?php echo $this->get_field_id( 'worldcup_news_widget_height' ); ?>" name="<?php echo $this->get_field_name( 'worldcup_news_widget_height' ); ?>" type="text" value="<?php if(isset($instance['worldcup_news_widget_height'])){ echo esc_attr( $instance['worldcup_news_widget_height' ] ); } ?>" />
		    </p>
		  
		    
        
        <?php

         }//end of function
         
         
         
         /**
          * Sanitize widget form values as they are saved.
          *
          * @see WP_Widget::update()
          *
          * @param array $new_instance Values just sent to be saved.
          * @param array $old_instance Previously saved values from database.
          *
          * @return array Updated safe values to be saved.
          */
         public function update( $new_instance, $old_instance ) {
             $instance = array();
             $instance['title'] = strip_tags( $new_instance['title'] );
             $instance['worldcup_news_widget_width'] = strip_tags( $new_instance['worldcup_news_widget_width'] );
             $instance['worldcup_news_widget_height'] = strip_tags( $new_instance['worldcup_news_widget_height'] );
             
             return $instance;
         }//end of function update
         
         
         
         
         
     
         
         // set time for feed cache
          public function set_cache_time() {
             $seconds = 600;
         
             return $seconds;
         }
         

         
         
         /**
          * This function will generate html cotent for widget area and also fires 
          * when ajax request is made with refresh button 
          * 
          */
        
         public function widget_area_content(){
     
             
             $feed = fetch_feed('https://www.fifa.com/worldcup/news/rss.xml');
             //$feed->set_item_limit(1);
             $maxitems =0;
             
             // Checks that the object is created correctly
             if ( ! is_wp_error( $feed ) ){
                 
                 // Figure out how many total items there are, but limit it to 5.
                 $maxitems = $feed->get_item_quantity(25);
                 
                 //$feed->force_feed(true);
                 $feed->enable_order_by_date(false);
                 
                 // Build an array of all the items, starting with element 0 (first element).
                 $rss_items = $feed->get_items(0,25);
             }
             
             
             
             
             
             if($maxitems != 0){
                 
                 $i=1;
                
             
                 
                 echo '<a id="refresh_wc_news" href="javascript:void(0);" alt="refresh"><span class="dashicons dashicons-image-rotate"></span></a>';
                 
                 // Loop through each feed item and display each item as a hyperlink.
                 foreach ( $rss_items as $item ){
                    
                     
                     if ($enclosure = $item->get_enclosure()){
                         
                        
                         
                              if(!empty($item->get_content())){
                                 
                                     echo  '<div style=" padding-right: 10px; padding-left: 15px;">';
                                     
                                     echo '<a href="#TB_inline?width=600&height=600&inlineId=fifa-feed-'.$i.'" class="thickbox" >';
                                    
                                     echo '<h5 style="text-align:center;color:black; width:99%; height:23px; overflow:hidden; ">'.$item->get_title().'</h4>';
                                     
                                     echo '<img style="border-radius: 8px;height:200px; width:99% !important;" src="';
                                     if(!empty($enclosure->get_link())){echo $enclosure->get_link();}
                                     else{echo "https://api.fifa.com/api/v1/picture/tournaments-sq-4/254645_w";}
                                     echo '"   class="thickbox">';
                                     echo '</a>';
                                     echo '</div><br/>';
                                    
                                     echo '<div id="fifa-feed-'.$i.'" style="display:none;" >';
                                     echo '<div style="text-align: center; padding-left:30px; padding-top:20px;">';
                                    
                                     echo '<a href="'.$item->get_link().'" target="_blank">';
                                     
                                     echo '<h4 style="color:red;text-align:center;">'.$item->get_title().'</h4><br/>';
                                     echo '</a>';
                                     
                                     echo '<img  style="border-radius: 8px;" src="';        
                                      if(!empty($enclosure->get_link())){echo $enclosure->get_link();}
                                      else{echo "https://api.fifa.com/api/v1/picture/tournaments-sq-4/254645_w";}
                                     echo'"   target="_blank">';
                                    
                                     echo $item->get_content();
                                     echo '<span>'.$item->get_date().'</span>';
                                     echo '</div>';
                                     echo '</div>';
                                 
                                 $i++;
                                     
                                 }
                                 else{
                                     echo '<div  style="padding-right: 10px; padding-left: 15px;">';
                                     echo '<a href="'.$item->get_link().'" target="_blank">';
                                     echo '<h4 style="text-align:center;color:black; width:99% !important; height:23px; overflow: hidden !important;">'.$item->get_title().'</h5>';
                                     echo '<img style="border-radius: 8px;height:200px !important; width:99% !important;" src="';
                                     if(!empty($enclosure->get_link())){echo $enclosure->get_link();}
                                     else{echo "https://api.fifa.com/api/v1/picture/tournaments-sq-4/254645_w";}
                                     echo '"   class="thickbox">';
                                     echo '</a>';
                                     echo '</div>';
                                     
                                 
                         }
                     }
                     
                     
                     
                     
                     // echo '</a>';
                     
                     //echo "<li  class='feed_item'  style='list-style-type:none!important;'>".$item->get_content()."</li>";
                     
                 }
                 echo '<div style="padding:15px;" align="center">';
                 echo '<p>'.$feed->get_copyright().'</p>';
                 echo '<a href="'.$feed->get_permalink().'" target="_blank" alt="'.$feed->get_description().'">';
                 echo '<img src="'.$feed->get_image_url().'"  />';
                 echo '</a>';
                 echo '</div>';
               
                 
             }//end of if
             
             
             
             
         }//end of function
         
         
         
         
         
         /**
          * Front-end display of widget.
          *
          * @see WP_Widget::widget()
          *
          * @param array $args     Widget arguments.
          * @param array $instance Saved values from database.
          */
         public function widget( $args, $instance ) {
  
             add_thickbox();
             extract( $args );
             $title = apply_filters( 'widget_title', $instance['title'] );
             echo $before_widget;
             if ( ! empty( $title ) )
                 echo $before_title . $title . $after_title;
                 
              // set widget height and width
                 if(!empty($instance['worldcup_news_widget_height'])){
                     $height = $instance['worldcup_news_widget_height'];
                 }
                 else{
                     $height = '500';
                 }
                 
                 if(!empty($instance['worldcup_news_widget_width'])){
                     $width = $instance['worldcup_news_widget_width'];
                 }
                 else{
                     $width = '300';
                 }
                 
                 /*set height and width for image inside feed*/
                 global $imgHeight;
                 global $imgWidth;
                 
                 if(!empty($instance['worldcup_news_image_height'])){
                     
                     $imgHeight = $instance['worldcup_news_image_height'];
                 }
                 else{
                     $imgHeight = '200';
                 }
                 if(!empty($instance['worldcup_news_image_width'])){
                     $imgWidth = $instance['worldcup_news_image_width'];
                 }
                 else{
                     $imgWidth = '250';
                 }
                 
                 
                 //add filter set time for  feed cache
                 add_filter('wp_feed_cache_transient_lifetime', array($this, 'set_cache_time'));
                 
                 echo '<div id="word_cup_news_feed" style="overflow-y: scroll;overflow-x: hidden;max-width:'.$width.'px; height:'.$height.'px;border: 4px solid black;border-radius:10px; padding:5px;">' ;
                 // echo '<h6 style="text-align:centre;">'.str_replace('- News','',$feed->get_title()).'</h6>';
                 
                 
                 
                 $this::widget_area_content();
               
                 echo '</div>';//end of feed content div
    
         }
    
}//end of class


/*function resgiter widget as plguin*/
function register_world_cup_2018_news(){
    register_widget( "world_cup_2018_news" );
}

add_action( 'widgets_init', 'register_world_cup_2018_news' );	