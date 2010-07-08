<?php

class skmt_default extends SKMT_Module{
    var $w_name = 'Standard';
    var $label = 'Standard T.C.';
    var $key = 'standard';
    var $description = 'Standard multi tag cloud.';

    function init(){
        $message = $this->colorfulInfo();
        if(empty($message)) return;
        $this->addInfo('How to change the cloud style', $message);
    }
    function colorfulInfo(){
        $return = '';
        $message = '<p>This plugin adds a different class for each tag size allowing customization of the cloud.</p>';
        $message .= '<p>Use the following classes to edit your style.css file.</p>';
        $message .= '<p><strong>Example:</strong> .my_cloud_wrap .tag_size_8{color:#000000;} <em>/* The tag of size 8 will be black */</em> </p>';
        $message .= '<p>The first class is the container, the second is the tag link. This way you can either have the same style for all clouds or one for each instance.</p>';
        $message .= '<table width="100%;" class="categoryList"><tr><th>Name</th><th>Cloud container class</th><th>Tag classes</th></tr>';
        
        if(!empty(SKMultiTag::$admin->options['clouds']) && count(SKMultiTag::$admin->options['clouds']) > 0){
            foreach(SKMultiTag::$admin->options['clouds'] as $k => $c){
                $i = 0;
                if($c['type'] == $this->key){
                    $tr = ($i%2 == 0)? ' class="even"' : ' class="odd"';
                    $return .= '<tr '.$tr.'><td>'.$c['name'].'</td><td>'.$c['cssPrefix'].'_wrap</td><td>'.$this->colorfulInfoGetClasses($c['smallest'], $c['largest']).'</td></tr>';
                    $i++;
                }
            }
            $return = empty($return)? '' : $message.$return.'</table>';
        }
        return $return;
    }

    function colorfulInfoGetClasses($s, $l){
        if(!is_numeric($s)) $s = 8;
        if(!is_numeric($l)) $l = 26;
        if($s > $l) return 'You inserted invalid values for the tag size attributes.';
        $return = '';
        $sep = '';
        for($i = $s; $i <= $l; $i++){
            $return .= $sep.'tag_size_'.$i;
            $sep = ', ';
        }
        return $return;
    }

    function getCloud($args, $instance){
        $cloud = SKMultiTag::getTags($args);
        
        if(empty($cloud['remove'])){
            $return = $cloud['add'];
        }else if(empty($cloud['add'])){
            $return = '<h3>'.$instance['remove'].'</h3>';
            $return .= $cloud['remove'];
            $return .= '<h3>'.$instance['add'].'</h3>';
            $return .= $instance['nomore'];
        } else {
            $return = '<h3>'.$instance['remove'].'</h3>';
            $return .= $cloud['remove'];
            $return .= '<h3>'.$instance['add'].'</h3>';
            $return .= $cloud['add'];
        }
        return $return;
    }
    
}
?>