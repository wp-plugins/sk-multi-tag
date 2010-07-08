<?php
/*
 * Simplified version of sk wp admin lib
 *
 */
class skmt_admin{
    /**
     * 
     * @param <type> $key
     */
    function __construct($key, $name, $baseUri, $defaults = array()){
        $this->wp_admin_plugin($key, $name, $baseUri, $defaults);
    }
    /**
     *
     * @param <String> $key Some key to identify your template, must start with a letter and use only letters and numbers, no spaces not special characters
     * @param <String> $name Display name for your template
     */
    function wp_admin_plugin($key, $name, $baseUri, $defaults = array()){
        $this->key = $key;
        $this->name = $name;
        $this->baseUri = $baseUri;
        $this->options = $this->getOptions($defaults);
        $this->messages = array();
        add_action('admin_menu', array(&$this, 'adminMenu'));
    }

    function addMessage($key, $message){
        $this->messages[$key][] = $message;
    }
    
    function getMessage($key){
        return empty($this->messages[$key])? array() : $this->messages[$key];
    }
    
    function getMessageP($key){
        $return = '';
        foreach($this->getMessage($key) as $message){
            $return .= '<p>'.$message.'</p>';
        }
        return $return;
    }

    function adminMenu(){
        $this->pluginPage = add_options_page($this->name.' Options', $this->name, 9, $this->key, array(&$this, 'adminOptions'));
        add_action( 'admin_print_scripts-' . $this->pluginPage, array(&$this, 'adminHeader') );
        
    }

    function adminHeader(){
        echo '<link rel="stylesheet" href="'.$this->baseUri.'/wpAdminLib/css/admin.css" type="text/css" />'."\n";
        echo '<link rel="stylesheet" href="'.$this->baseUri.'/wpAdminLib/css/smoothness/jquery-ui-1.8.1.custom.css" type="text/css" />'."\n";
        echo '<script type="text/javascript" src="'.$this->baseUri.'/wpAdminLib/js/jquery-1.4.2.min.js"></script>'."\n";
        echo '<script type="text/javascript" src="'.$this->baseUri.'/wpAdminLib/js/jquery-ui-1.8.1.custom.min.js"></script>'."\n";
        echo '<script type="text/javascript" src="'.$this->baseUri.'/wpAdminLib/js/jquery.cookie.js"></script>'."\n";
        echo '<script type="text/javascript" src="'.$this->baseUri.'/wpAdminLib/js/jquery.ui.datepicker-it.js"></script>'."\n";
        echo '<script type="text/javascript" src="'.$this->baseUri.'/wpAdminLib/js/admin.js"></script>'."\n";

        echo '<script type="text/javascript">'."\n";

        $headScriptsA = array();
        $headScriptsA = apply_filters('add_admin_ready_'.$this->key, $headScriptsA);
        $headScripts = '';
        if(count($headScriptsA) > 0){
            foreach($headScriptsA as $s){
                $headScripts .= $s;
            }
        }
        echo '
            $(document).ready(function() {
                    $("#tabs").tabs({ cookie: { expires: 30 } });
                    '.$headScripts.'
            });
        ';
        echo '</script>'."\n";
    }

    /**
     * Admin options tabs
     * use in functions.php like:
        add_filter('add_template_admin_tab', 'adminPanel');
        function adminPanel($list){
            $tabs['Tab title'] = tabContent();
            return array_merge($tabs, $list);
        }
     */
    function adminOptions(){
        echo '<div class="wrap templateAdminWrap '.$this->key.'_options"><h2>'.$this->name.' options</h2>';
        $controlPanel = array();

        $controlPanel = apply_filters('add_admin_tab_'.$this->key, $controlPanel);

        echo SKMTjQueryUtils::makeTabs('tabs', $controlPanel);
        echo '</div>';
    }

    /**
     * Template options
     * @param <type> $defaults
     * @return <type>
     */
    function getOptions($defaults = array()){
        if(isset($this->options)) return $this->options;
        // Reset options
        //update_option($this->key, array());
        $options = get_option($this->key);
        if(!$options){
            return $defaults;
        }
        return $options;
    }

    function updateOptions($key, $val){
        if(isset($this->options)){
            $this->options[$key] = $val;
            update_option($this->key, $this->options);
        }
    }
    function updateSubOptions($key, $val, $id = null){
        if(isset($this->options)){
            if(isset($id)){
                $this->options[$key][$id] = $val;
            }
            else
                $this->options[$key][] = $val;
            update_option($this->key, $this->options);
        }
    }
    function deleteSubOptions($key, $val){
        if(isset($this->options)){
            unset($this->options[$key][$val]);
            update_option($this->key, $this->options);
        }
    }
    function overwriteOptions($val){
        $this->options = $val;
        update_option($this->key, $val);
    }
}
?>
