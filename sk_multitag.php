<?php
/*
Plugin Name: Sk Multi Tag
Plugin URI: http://www.skipstorm.org/2010/07/sk-multi-tag-v-1-0-tag-multipli-su-wordpress
Description: This plugin adds a tag cloud widget where you can select multiple tags at once. Check the plugin webpage for customization.
Author: Skipstorm
Version: 1.0
Author URI: http://www.skipstorm.org/
*/

// Importazione di wpAdminLib, libreria per la creazione del pannello di amministrazione
include(dirname(__FILE__).DIRECTORY_SEPARATOR.'wpAdminLib'.DIRECTORY_SEPARATOR.'wp-admin-lib.php');
include(dirname(__FILE__).DIRECTORY_SEPARATOR.'widget.php');
include(dirname(__FILE__).DIRECTORY_SEPARATOR.'module.php');
SKMultiTag::init();

class SKMultiTag{
    public static $key = 'skMultiTag';
    public static $name = 'Sk Multi Tag';
    public static $thisUri;
    public static $admin;
    public static $modules;
    public static $settings;
    public static $errors;
    public static $messages;
    public static $tags;

    public static function init(){
        self::$thisUri = get_bloginfo('url').'/wp-content/plugins/sk-multi-tag';
        self::$admin = new skmt_admin(self::$key, self::$name, self::$thisUri);
        add_filter('add_admin_tab_'.self::$key, array('SKMultiTag', 'adminPanel'));
        self::importModules();
        //self::analyzeTags();
        add_action( 'widgets_init', array('SKMultiTag', 'load_widget') );
    }


    function load_widget() {
        register_widget( 'SKMT_widget' );
    }

    public static function importModules(){
        self::$modules = array();
        $modulesList = SKMTFilesystem::getFileList(dirname(__FILE__).DIRECTORY_SEPARATOR.'modules', array('php'));
        if(!empty($modulesList)){
            foreach($modulesList as $m){
                include_once('modules'.DIRECTORY_SEPARATOR.$m);
                $mn = str_ireplace('.php', '', $m);
                $tm = new $mn();
                self::$modules[$tm->key] = $tm;
            }
        }
    }

    public static function getModulesList(){
        $list = array();
        foreach(self::$modules as $m){
            $list[$m->key] = $m->w_name.': '.$m->description;
        }
        return $list;
    }

    public static function adminPanel($list){
        $tabs['New Tag Cloud'] = self::newTagCloud();
        $tabs['Tag Clouds'] = self::listTagCloud();
        return array_merge($tabs, $list);
    }

    public static function analyzeTags(){
         global $wp_query,$wpdb;
        @$selectedTags = $wp_query->query_vars['tag'];

    }

    public static function newTagCloud(){
        if(!empty($_POST['newTagStyle']) && $_POST['newTagStyle'] == 'new')
                self::saveTagCloud();
        return '<fieldset><legend>Here you can create a new tag cloud style</legend>'.
        '<form method="post" action="">'.
            '<input name="newTagStyle" type="hidden" value="new">'.
            '<p><label>Name *</label><input name="new[name]" type="text" value="My Custom Cloud" /></p>'.
            '<p><label>Description *</label><input name="new[description]" type="text" value="Insert description here" /></p>'.
            '<p><label>Identificator (unique/overwrites) *</label><input name="new[cssPrefix]" type="text" value="cloud_'.rand(0,100).'" /></p>'.
            '<p><label>Smallest tag size</label><input name="new[smallest]" type="text" value="8" /></p>'.
            '<p><label>Largest tag size</label><input name="new[largest]" type="text" value="26" /></p>'.
            '<p><label>Size unit</label>'.SKMTHtmlHelper::selectSimple(array('px' => 'Pixel','pt' => 'Point','em' => 'EM' ), 'new[unit]').'</p>'.
            '<p><label>Order tag by</label>'.SKMTHtmlHelper::selectSimple(array('RAND' => 'Random','none' => 'Tag id','name' => 'Name','count' => 'Post count' ), 'new[orderby]').'</p>'.
            '<p><label>Order</label>'.SKMTHtmlHelper::selectSimple(array('ASC' => 'Ascendant','DESC' => 'Descendant'), 'new[order]').'</p>'.
            '<p><label>Exclude tags (ids comma delimited)</label><input name="new[exclude]" type="text" /></p>'.
            '<p><label>Use only (ids comma delimited, overwrites "exclude")</label><input name="new[include]" type="text" /></p>'.
            '<p><label>Maximum number of tags to display</label><input name="new[number]" type="text" /></p>'.
            '<p><strong>Select a cloud style</strong></p>'.
            SKMTHtmlHelper::radios(self::getModulesList(), 'new[type]').
        '<p><input type="submit" name="submit" class="button"/></p>'.
        '</form>'.
        '<div style="clear:both;"></div>'.
        '</fieldset>'.
        '<fieldset><legend>Info</legend>'.
            '<p><strong>Name:</strong> you\'ll find this value in the widget option.</p>'.
            '<p><strong>Description:</strong> just a reminder for your self.</p>'.
            '<p><strong>Identificator:</strong> as it\'s used to generate the class name must start with a letter and be composed only by letters, numbers and _. Good: my_class. Bad: 12309, this is my class.</p>'.
            '<p>This field is unique, this means if you use a value that already exists will overwrite the options associated with it.</p>'.
            '<p><strong>Exclude:</strong> tags whis specified ids won\'t be shown.</p>'.
            '<p><strong>Use only:</strong> will show only the tags with specified ids</p>'.
        '</fieldset>';
    }

    public static function saveTagCloud(){
        $cssPrefix = self::sanitizeCloudId($_POST['new']['cssPrefix']);
        
        $new['name'] = (!empty($_POST['new']['name']))? $_POST['new']['name'] : 'No Name';
        $new['description'] = (!empty($_POST['new']['description']))? $_POST['new']['description'] : 'No Description';
        $new['cssPrefix'] = self::sanitizeCloudId($_POST['new']['cssPrefix']);
        $new['smallest'] = (is_numeric($_POST['new']['smallest']))? $_POST['new']['smallest'] : 8;
        $new['largest'] = (is_numeric($_POST['new']['largest']))? $_POST['new']['largest'] : 22;
        $new['unit'] = $_POST['new']['unit'];
        $new['orderby'] = $_POST['new']['orderby'];
        $new['order'] = $_POST['new']['order'];
        $new['exclude'] = ($_POST['new']['exclude'])? $_POST['new']['exclude'] : null;
        $new['include'] = ($_POST['new']['include'])? $_POST['new']['include'] : null;
        $new['number'] = (is_numeric($_POST['new']['number']))? $_POST['new']['number'] : null;
        $new['type'] = self::validateType($_POST['new']['type']);

        self::$admin->updateSubOptions('clouds', $new, $cssPrefix);
    }

    private static function validateType($t){
        foreach(self::getModulesList() as $k => $v){
            $f = empty($f)? $k : $f;
            if($k == $t) return $t;
        } return $f;
    }

    public static function listTagCloud(){
        if(!empty($_POST['deleteCloud']) && $_POST['deleteCloud'] == 'del' && !empty($_POST['delCloud'])){
            $data = array();
            foreach($_POST['delCloud'] as $del){
                unset(self::$admin->options['clouds'][$del]);
            }
            /*
            foreach(self::$admin->options['clouds'] as $k => $c){
                $data[] = array($c['name'], $c['description'], $modules[$c['type']]);
            }
            self::$admin->options['clouds'] = $data;
             */
            self::$admin->overwriteOptions(self::$admin->options);
        }
        
        if(empty(self::$admin->options['clouds']) || count(self::$admin->options['clouds']) < 1) return 'No tag clouds created yet. Go to "New Tag Cloud" tab and create one.';

        $data = array();
        $modules = self::getModulesList();
        foreach(self::$admin->options['clouds'] as $k => $c){
            $data[$k] = array($c['name'], $c['description'], $c['cssPrefix'], $modules[$c['type']]);
        }
        
        return '<fieldset><legend>Your tag cloud styles</legend>'.
        '<form method="post" action="">'.
        '<input name="deleteCloud" type="hidden" value="del">'.
        SKMTHtmlHelper::tableFormDelete($data, array('Name', 'Description', 'ID / CSS Class / Prefix', 'Type', 'Delete'), array('width' => '100%', 'class' => 'categoryList'), null, 'delCloud').
        '<p><input type="submit" name="submit" class="button"/></p>'.
        '</form>'.
        '<div style="clear:both;"></div>'.
        '</fieldset>';

    }

    /**
     *
     * @param <type> $id
     * @return <type>
     */
    public static function sanitizeCloudId($id){
        $id = preg_replace('/[^a-z0-9A-Z-_]/', '', $id);
        if(empty($id) || !is_string($id)){

            return 'cloud_'.rand(0, 10000);
        }
        if(!preg_match('/^[a-zA-Z]{1}/', $id)){
            return 'cloud_'.$id;
        }
        
        return $id;
    }

    public static function getArgs($cloud){
        $defaults = array(
                    'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
                    'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
                    'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => false,
                    'topic_count_text_callback' => 'default_topic_count_text',
                    'topic_count_scale_callback' => 'default_topic_count_scale', 'filter' => 1,
                    'class' => 'skmt_tag'
            );
        return (!empty(self::$admin->options['clouds'][$cloud]) && is_array(self::$admin->options['clouds'][$cloud]))? array_merge($defaults, self::$admin->options['clouds'][$cloud]) : $default;
    }

    public static function selectedTags(){
        @$selectedTags = $wp_query->query_vars['tag'];
    }

    /**
     * In modo da avere i dati disponibili in ogni posizione del template è meglio se questa parte
     * viene fatta automaticamente e il modulo richiede semplicemente le liste add e remove
     * 
     * @global <type> $wp_query
     * @global <type> $wpdb
     * @param <type> $args
     * @return <type>
     */
    public static function getTags($args){
        extract($args);
        global $wp_query,$wpdb;

        @$selectedTags = $wp_query->query_vars['tag'];

	if ($selectedTags == '')
	{
            // No tags selected
            return self::tags_noneSelected($args);
	} else {
                // Tags selected
		$selectedTags = ereg_replace(' ', '+', $selectedTags);
		$selectedTagsArray = explode('+', $selectedTags);

                $tPosts = get_posts(array('tag' => $selectedTags, 'numberposts' => -1));

                // Posts with the selected tags
                foreach($tPosts as $tPost){
                    $tPostIds[] = $tPost->ID;
                }
                
                // Retreive slugs
                $good_slugs = self::tags_getSlugs($tPostIds);
                
		$tags = get_tags(array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' )));
                
		if ($tags) {

			// Remove tags
			if(count($selectedTagsArray) > 1){
				$delTags = array();


                                 //* Per ogni tag, se è presente nella lista dei tag selezionati
                                 //* viene inserito nella lista di rimozione
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
					$selTag = eregi_replace("$tag->slug$", '', $selTag);
					$delTags[ $key ] = clone $tag;
					$delTags[ $key ]->link = $selTag.$selectedTagsDel;
					$delTags[ $key ]->id = $tag->term_id;

				}
				$delTagLinks = self::generate_tag_cloud($delTags, $args);
			} else {
				$delTagLinks = '<a href="'.get_option('home').'">'.$selectedTags.'</a>';
			}
			
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
                        $addTagLinks = '';
                        if(count($tags) > 0)
                            $addTagLinks = self::generate_tag_cloud($tags, $args);
			
		}
                return array('add' => $addTagLinks, 'remove' => $delTagLinks);
	}
    }
    public static function tags_getSlugs($tPostIds = array()){
        global $wpdb;
        if(isset($tPostIds) && count($tPostIds) > 0){
            $sql = "SELECT t.slug FROM $wpdb->posts p INNER JOIN $wpdb->term_relationships tr ";
            $sql .= " ON (p.ID = tr.object_id) INNER JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
            $sql .= " INNER JOIN $wpdb->terms t ON (tt.term_id = t.term_id)";
            $sql .= " WHERE tt.taxonomy = 'post_tag' AND p.ID IN ('" . implode("', '", $tPostIds) . "')";

            $sql .= " GROUP BY t.slug";
            return $wpdb->get_col($sql);
        } else {
            return array();
        }
    }
    public static function tags_noneSelected($args){
            $tags = get_tags( array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) );
            if ($tags) {

                    foreach ( $tags as $key => $tag ) {
                            $link = get_tag_link( $tag->term_id );

                            $tags[ $key ]->link = $link;
                            $tags[ $key ]->id = $tag->term_id;

                    }
                    return array('add' => self::generate_tag_cloud($tags, $args), 'remove' => '');
            }
            return array('add' => '', 'remove' => '');
    }

    public static function tag_cloud( $args = '' ) {
            $defaults = array(
                    'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
                    'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
                    'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => false,
                    'class' => 'skmt_tag'
            );
            $args = wp_parse_args( $args, $defaults );

            $tags = get_terms( $args['taxonomy'], array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) ); // Always query top tags

            if ( empty( $tags ) )
                    return;

            foreach ( $tags as $key => $tag ) {
                    if ( 'edit' == $args['link'] )
                            $link = get_edit_tag_link( $tag->term_id, $args['taxonomy'] );
                    else
                            $link = get_term_link( intval($tag->term_id), $args['taxonomy'] );
                    if ( is_wp_error( $link ) )
                            return false;

                    $tags[ $key ]->link = $link;
                    $tags[ $key ]->id = $tag->term_id;
            }

            $return = self::generate_tag_cloud( $tags, $args ); // Here's where those top tags get sorted according to $args

            //$return = apply_filters( 'wp_tag_cloud', $return, $args );

            if ( 'array' == $args['format'] || empty($args['echo']) )
                    return $return;

            echo $return;
    }


    public static function generate_tag_cloud( $tags, $args = '' ) {
            global $wp_rewrite;
            $defaults = array(
                    'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 0,
                    'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
                    'topic_count_text_callback' => 'default_topic_count_text',
                    'topic_count_scale_callback' => 'default_topic_count_scale', 'filter' => 1,
                    'class' => 'skmt_tag'
            );

            if ( !isset( $args['topic_count_text_callback'] ) && isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {
                    $body = 'return sprintf (
                            _n(' . var_export($args['single_text'], true) . ', ' . var_export($args['multiple_text'], true) . ', $count),
                            number_format_i18n( $count ));';
                    $args['topic_count_text_callback'] = create_function('$count', $body);
            }

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            if ( empty( $tags ) )
                    return;

            $tags_sorted = apply_filters( 'tag_cloud_sort', $tags, $args );
            if ( $tags_sorted != $tags  ) { // the tags have been sorted by a plugin
                    $tags = $tags_sorted;
                    unset($tags_sorted);
            } else {
                    if ( 'RAND' == $order ) {
                            shuffle($tags);
                    } else {
                            // SQL cannot save you; this is a second (potentially different) sort on a subset of data.
                            if ( 'name' == $orderby )
                                    uasort( $tags, create_function('$a, $b', 'return strnatcasecmp($a->name, $b->name);') );
                            else
                                    uasort( $tags, create_function('$a, $b', 'return ($a->count > $b->count);') );

                            if ( 'DESC' == $order )
                                    $tags = array_reverse( $tags, true );
                    }
            }

            if ( $number > 0 )
                    $tags = array_slice($tags, 0, $number);

            $counts = array();
            $real_counts = array(); // For the alt tag
            foreach ( (array) $tags as $key => $tag ) {
                    $real_counts[ $key ] = $tag->count;
                    $counts[ $key ] = $topic_count_scale_callback($tag->count);
            }

            $min_count = min( $counts );
            $spread = max( $counts ) - $min_count;
            if ( $spread <= 0 )
                    $spread = 1;
            $font_spread = $largest - $smallest;
            if ( $font_spread < 0 )
                    $font_spread = 1;
            $font_step = $font_spread / $spread;

            $a = array();

            foreach ( $tags as $key => $tag ) {
                    $count = $counts[ $key ];
                    $real_count = $real_counts[ $key ];
                    $tag_link = '#' != $tag->link ? esc_url( $tag->link ) : '#';
                    $tag_id = isset($tags[ $key ]->id) ? $tags[ $key ]->id : $key;
                    $tag_name = $tags[ $key ]->name;
                    $size = ( $smallest + ( ( $count - $min_count ) * $font_step ) );
                    $a[] = "<a href='$tag_link' class='tag-link-$tag_id tag_size_$size' title='" . esc_attr( $topic_count_text_callback( $real_count ) ) . "' style='font-size: " .
                            $size
                            . "$unit;'>$tag_name</a>";
            }

            switch ( $format ) :
            case 'array' :
                    $return =& $a;
                    break;
            case 'list' :
                    $return = "<ul class='wp-tag-cloud'>\n\t<li>";
                    $return .= join( "</li>\n\t<li>", $a );
                    $return .= "</li>\n</ul>\n";
                    break;
            default :
                    $return = join( $separator, $a );
                    break;
            endswitch;

            return $return;
    }
}
?>