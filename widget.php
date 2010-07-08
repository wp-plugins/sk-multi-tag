<?php

class SKMT_widget extends WP_Widget{
        var $name = 'Sk multi tag';
    	/**
	 * Widget setup.
	 */
	function SKMT_widget() {
		// Widget settings.
		$widget_ops = array('description' => __('Sk multi tag widget.', 'skMultiTag') );

		// Widget control settings.
		//$control_ops = array( 'width' => 350, 'height' => 350, 'id_base' => 'SKMT_widget-widget');

		// Create the widget.
		$this->WP_Widget( 'SKMT_widget-widget', __('Sk multi tag', 'skMultiTag'), $widget_ops);
	}

        function widget($args, $instance) {
		extract($args);
		$current_taxonomy = 'post_tag';
                if(!isset($instance['cloud_style'])) return;
                $cloud = SKMultiTag::$admin->options['clouds'][$instance['cloud_style']];
                if(empty($cloud)) {
                    echo 'The cloud style associated with this widget no longer exist.';
                    return;
                }
                
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( !empty($instance['title']) )
			echo $before_title . $instance['title'] . $after_title;
		echo '<div class="'.$cloud['cssPrefix'].'_wrap">';
                echo SKMultiTag::$modules[$cloud['type']]->getCloud(SKMultiTag::getArgs($instance['cloud_style']), $instance);
		echo "</div>\n";
		echo $after_widget;
        }

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['add'] = strip_tags(stripslashes($new_instance['add']));
		$instance['remove'] = strip_tags(stripslashes($new_instance['remove']));
		$instance['nomore'] = strip_tags(stripslashes($new_instance['nomore']));
		$instance['cloud_style'] = strip_tags(stripslashes($new_instance['cloud_style']));
		return $instance;
	}

	function form( $instance ) {
            $cloud_style = isset( $instance['cloud_style'] ) ? $instance['cloud_style'] : '';
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>

	<p><label for="<?php echo $this->get_field_id('add'); ?>"><?php _e('Add tag label:', 'skMultiTag') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('add'); ?>" name="<?php echo $this->get_field_name('add'); ?>" value="<?php if (isset ( $instance['add'])) {echo esc_attr( $instance['add'] );} ?>" /></p>

	<p><label for="<?php echo $this->get_field_id('remove'); ?>"><?php _e('Remove tag label:', 'skMultiTag') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('remove'); ?>" name="<?php echo $this->get_field_name('remove'); ?>" value="<?php if (isset ( $instance['remove'])) {echo esc_attr( $instance['remove'] );} ?>" /></p>

	<p><label for="<?php echo $this->get_field_id('nomore'); ?>"><?php _e('No more tags:', 'skMultiTag') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('nomore'); ?>" name="<?php echo $this->get_field_name('nomore'); ?>" value="<?php if (isset ( $instance['nomore'])) {echo esc_attr( $instance['nomore'] );} ?>" /></p>

        <?php
            if(empty(SKMultiTag::$admin->options['clouds']))
                 echo '<p>Attention! You must create a cloud from admin panel first!</p>';
            else {
                ?>
                <p>
                        <label for="<?php echo $this->get_field_id('cloud_style'); ?>"><?php _e('Cloud style:', 'skMultiTag'); ?></label>
                        <select id="<?php echo $this->get_field_id('cloud_style'); ?>" name="<?php echo $this->get_field_name('cloud_style'); ?>">
                <?php
                        foreach(SKMultiTag::$admin->options['clouds'] as $k => $c) {
                                $selected = $cloud_style == $k ? ' selected="selected"' : '';
                                echo '<option'. $selected .' value="'. $k .'">'. $c['name'] .'</option>';
                        }
                ?>
                        </select>
                </p>

                <?php
            }
	}
}
?>
