<?php
/*
Plugin Name: Sk Multi Tag
Plugin URI: http://www.skipstorm.org/
Description: This plugin adds a tag cloud widget where you can select multiple tags at once. You can customize the look of the wordcloud by adding a css for the divs with id skwr_removetags and skwr_addtags.
Author: Skipstorm
Version: 0.6
Author URI: http://www.skipstorm.org/
*/



/*
	Gestione del widget
*/
function skmultitag_widget($args) {
	extract($args);
	
	if(!$options = get_option('Sk_MultiTag')){
	   $options = getDefaultValues();
	}
	
	$defaults = array(
          'smallest' => 8, 
          'largest' => 22,
          'unit' => 'pt', 
          'number' => 45,  
          'format' => 'flat',
          'orderby' => 'name', 
          'order' => 'ASC',
          'link' => 'view', 
          'taxonomy' => 'post_tag', 
          'echo' => true
          );
          
	echo $before_widget;
    echo $before_title.$options['wpwr_title'].$after_title;
        
    global $wp_query,$wpdb;
    
    @$selectedTags = $wp_query->query_vars['tag'];
        
	if ($selectedTags == '')
	{
		$tags = get_tags( array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) ); 
		if ($tags) {
			
			foreach ( $tags as $key => $tag ) {
				$link = get_tag_link( $tag->term_id );
				
				//$link = make_tag_link( $tag->slug );	
				$tags[ $key ]->link = $link;
				$tags[ $key ]->id = $tag->term_id;
				//echo $link;
				
			}
            echo '<div id="skwr_addtags">';
			echo wp_generate_tag_cloud($tags, $defaults);
			echo '</div>';
		}
	} else {
		$selectedTagsArray = explode(' ', $selectedTags);
        $tPosts = get_posts(array('tag' => $selectedTags));
        foreach($tPosts as $tPost){
            $tPostIds[] = $tPost->ID;
        }
        
        if(isset($tPostIds) && count($tPostIds) > 0){
            $sql = "SELECT t.slug FROM $wpdb->posts p INNER JOIN $wpdb->term_relationships tr ";
            $sql .= " ON (p.ID = tr.object_id) INNER JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
            $sql .= " INNER JOIN $wpdb->terms t ON (tt.term_id = t.term_id)";
            $sql .= " WHERE tt.taxonomy = 'post_tag' AND p.ID IN ('" . implode("', '", $tPostIds) . "')";

            $sql .= " GROUP BY t.slug";
            $good_slugs = $wpdb->get_col($sql);
        } else {
            $good_slugs = array();
        }
        
		$tags = get_tags( array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) ); 
		$selectedTags = ereg_replace(' ', '+', $selectedTags);


		if ($tags) {
			
			// Remove tags
			if(count($selectedTagsArray) > 1){
				$delTags = array();
				
				foreach ( $tags as $key => $tag ) {
					foreach($selectedTagsArray as $k => $v){
							if($v == $tag->slug)
								$selectedTagsDelArray[] = $tag;
						}	
				}
				
				foreach ( $selectedTagsDelArray as $key => $tag ) {
					$arrayForPars = array();
					foreach($selectedTagsArray as $tr){
						if($tag->slug != $tr)
							$arrayForPars[] = $tr;
					}
					$selectedTagsDel = implode('+', $arrayForPars);
					$selTag = get_tag_link( $tag->term_id );
					$selTag = eregi_replace("[/]*$", '', $selTag);
					$selTag = eregi_replace($tag->slug, '', $selTag);
					$delTags[ $key ] = clone $tag;
					$delTags[ $key ]->link = $selTag.$selectedTagsDel;
					$delTags[ $key ]->id = $tag->term_id;
					
				}
				$delTagLinks = wp_generate_tag_cloud($delTags, $defaults);
			} else {
				$delTagLinks = '<a href="'.get_option('home').'">'.$selectedTags.'</a>';
			}
			echo '<div id="skwr_removetags">'.$options['wpwr_rem_title'];
			echo $delTagLinks;
			echo '</div>';
			
			
			// Add tags
			foreach ( $tags as $key => $tag ) {

				if(in_array($tag->slug, $good_slugs) && !in_array($tag->slug, $selectedTagsArray)){
					$link = get_tag_link( $tag->term_id );
					
					$link = eregi_replace("[/]*$", '', $link);
					
					$tags[ $key ]->link = $link.'+'.$selectedTags;
					$tags[ $key ]->id = $tag->term_id;
				} else {
					unset($tags[ $key ]);
					}

			}
			echo '<div id="skwr_addtags">'.$options['wpwr_add_title'];
            if(count($tags) > 0)
                echo wp_generate_tag_cloud($tags, $defaults);
            else
                echo $options['wpwr_mnt_title'];
			echo '</div>';
		}
	}
    echo stripslashes($options['wpwr_footer']);
    echo $after_widget;
}

function init_skmultitag(){
    register_sidebar_widget("Sk_MultiTag", "skmultitag_widget");
    register_widget_control('Sk_MultiTag', 'control');
}
add_action("plugins_loaded", "init_skmultitag");

function skmultitag_tag_title(){
    global $wp_query;
    @$selectedTags = $wp_query->query_vars['tag'];
    return $selectedTags;
}


function control(){
   if(!$options = get_option('Sk_MultiTag')){
	   $options = getDefaultValues();
	   }

   if(isset($_POST['wpwr_title'])){
	   $options['wpwr_title'] = $_POST['wpwr_title'];
	   update_option('Sk_MultiTag', $options);
	   }
   if(isset($_POST['wpwr_add_title'])){
	   $options['wpwr_add_title'] = $_POST['wpwr_add_title'];
	   update_option('Sk_MultiTag', $options);
	   }
   if(isset($_POST['wpwr_rem_title'])){
	   $options['wpwr_rem_title'] = $_POST['wpwr_rem_title'];
	   update_option('Sk_MultiTag', $options);
	   }
   if(isset($_POST['wpwr_mnt_title'])){
	   $options['wpwr_mnt_title'] = $_POST['wpwr_mnt_title'];
	   update_option('Sk_MultiTag', $options);
	   }
   if(isset($_POST['wpwr_footer'])){
	   $options['wpwr_footer'] = $_POST['wpwr_footer'];
	   update_option('Sk_MultiTag', $options);
	   }

   echo '<p>Sk MultiTag control panel</p>';
   echo '<p>Widget title:</p><p><input type="text" name="wpwr_title" value="'.$options['wpwr_title'].'" /></p>';
   echo '<p>Add title:</p><p><input type="text" name="wpwr_add_title" value="'.$options['wpwr_add_title'].'" /></p>';
   echo '<p>Remove title:</p><p><input type="text" name="wpwr_rem_title" value="'.$options['wpwr_rem_title'].'" /></p>';
   echo '<p>No more tags:</p><p><input type="text" name="wpwr_mnt_title" value="'.$options['wpwr_mnt_title'].'" /></p>';
   echo '<p>Footer:</p><p><textarea name="wpwr_footer">'.stripslashes($options['wpwr_footer']).'</textarea></p>';
}

function getDefaultValues(){
	$options = array('wpwr_title' => '<h1>WordRain</h1>',
                'wpwr_add_title' => '<h3>Add Tag</h3>',
				'wpwr_rem_title' => '<h3>Remove Tag</h3>',
				'wpwr_mnt_title' => '<h4>No more tags, remove some.</h4>',
				'wpwr_footer' => '<span style="font-size:8px;">by<a href="http://www.skipstorm.org" style="font-size:8px;">Skipstorm</a></span>');
	update_option('Sk_MultiTag', $options);
	return $options;
	}
?>