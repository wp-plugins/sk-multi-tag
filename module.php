<?php
class SKMT_Module{
    var $optionsList;
    var $cloudInfo;

    function __construct(){
        $this->SKMT_Module();
        $this->init();
    }

    /**
     * Overwrite this method to add settings options and info in this cloud style admin tab
     */
    function init(){}

    function SKMT_Module(){
        add_filter('add_admin_tab_skMultiTag', array(&$this, 'adminPanel'), 0);
    }
    
    function adminPanel($list){
        $info = $this->getInfo();
        $panel = $this->getAdminPanel();

        $data = $info.$panel;

        if(!empty($data)){
            $tabs[$this->label] = $data;
            return array_merge($tabs, $list);
        } return $list;
    }

    function getAdminPanel(){
        if(empty($this->optionsList)) return '';
        if(!empty($_POST[$this->key.'_options']))
                $this->saveOptions();

        $pref = $this->key.'_options';
        $return = '<fieldset><legend>'.$this->label.' Options</legend>'.
        '<form method="post" action="">';

        foreach($this->optionsList as $k => $o){
            $o['default'] = empty(SKMultiTag::$admin->options['options'][$this->key][$k])? $o['default'] : SKMultiTag::$admin->options['options'][$this->key][$k];
            switch($o['type']){
                case 'text':
                    $return .= SKMTHtmlHelper::textInput($pref.'['.$k.']', $o['default'], $o['label']);
                break;
                case 'select':
                    $return .= SKMTHtmlHelper::select($o['data'], $pref.'['.$k.']', $o['default'], $o['label']);
                break;
            }
        }

        $return .= '<p><input type="submit" name="submit" class="button"/></p>'.
        '</form>'.
        '<div style="clear:both;"></div>'.
        '</fieldset>';

        return $return;
    }

    function saveOptions(){
        //SKMultiTag::$admin->options['options'][$this->key]
        //$_POST[$this->key.'_options']

        $options = array();
        foreach($this->optionsList as $k => $o){
            $value = $_POST[$this->key.'_options'][$k];
            $validation = $o['validation'];
            $default = $o['default'];
            switch($o['type']){
                case 'text':
                    SKMultiTag::$admin->options['options'][$this->key][$k] = $this->validatePregMatch($value, $validation, $default);
                break;
                case 'select':
                    SKMultiTag::$admin->options['options'][$this->key][$k] = $value;
                break;
            }
        }
        SKMultiTag::$admin->overwriteOptions(SKMultiTag::$admin->options);
    }

    function validatePregMatch($value, $validation, $default){
        if(empty($validation)) return $value;
        return (preg_match($validation, $value))? $value : $default;
    }

    function getInfo(){
        if(empty($this->cloudInfo)) return '';
        $return = '';
        foreach($this->cloudInfo as $info){
            $return .= '<fieldset><legend>'.$info['title'].'</legend>'.$info['html'].'</fieldset>';
        }
        return $return;
    }

    function addInfo($title, $html){
        $this->cloudInfo[] = array('title' => $title, 'html' => $html);
    }

    function addOption($id, $label, $type, $validation, $default, $data = array()){
        if(empty($this->optionsList)) $this->optionsList = array();

        $this->optionsList[$id] = array('label' => $label, 'type' => $type, 'validation' => $validation, 'default' => $default, 'data' => $data);
    }
    
    function getOptionValueById($id){
        if(isset(SKMultiTag::$admin->options['options'][$this->key][$id]))
            return SKMultiTag::$admin->options['options'][$this->key][$id];
        if(isset($this->optionsList[$id]['default']))
            return $this->optionsList[$id]['default'];
        return false;
    }
}
?>
