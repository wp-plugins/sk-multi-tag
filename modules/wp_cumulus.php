<?php

class wp_cumulus extends SKMT_Module{
    var $w_name = 'WP Cumulus';
    var $label = 'WP Cumulus';
    var $key = 'wpcumulus';
    var $description = '3D tag cloud by weefselkweekje and LukeMorton.';

    function init(){
        $this->addOption('width', 'Cloud width', 'text', '', 160);
        $this->addOption('height', 'Cloud height', 'text', '', 160);
        $this->addOption('tcolor', 'From color (hex 6 digits)', 'text', '/^[0-9a-fA-F]{6}$/', '333333');
        $this->addOption('tcolor2', 'To color (hex 6 digits)', 'text', '/^[0-9a-fA-F]{6}$/', '333333');
        $this->addOption('hicolor', 'Hover color (hex 6 digits)', 'text', '/^[0-9a-fA-F]{6}$/', '000000');
        $this->addOption('bgcolor', 'Background color (hex 6 digits)', 'text', '/^[0-9a-fA-F]{6}$/', 'ffffff');
        $this->addOption('speed', 'Speed', 'text', '/^[0-9]{1,3}$/', 100);
    }

    function getCloud($args, $instance){
        $cloud = SKMultiTag::getTags($args);

        if(empty($cloud['remove'])){
            $return = $this->getCumulus($cloud['add']);
        }else if(empty($cloud['add'])){
            $return = '<h3>'.$instance['remove'].'</h3>';
            $return .= $cloud['remove'];
            $return .= '<h3>'.$instance['add'].'</h3>';
            $return .= $instance['nomore'];
        } else {
            $return = '<h3>'.$instance['remove'].'</h3>';
            $return .= $cloud['remove'];
            $return .= '<h3>'.$instance['add'].'</h3>';
            $return .= $this->getCumulus($cloud['add']);
        }
        return $return;
    }

    function getCumulus($tags){
        $this->getOptions();
        $soname = "widget_so";
	$divname = "wpcumuluswidgetcontent";
	// get compatibility mode variable from the main options

	// get the tag cloud...
	if( $this->options['mode'] != "cats" ){
            $tagcloud = urlencode( str_replace( "&nbsp;", " ", $tags ) );
	}
	// get categories
	if( $this->options['mode'] != "tags" ){
            ob_start();
            wp_list_categories('title_li=&show_count=1&hierarchical=0&style=none');
            $cats = urlencode( ob_get_clean() );
	}
	
	$movie = plugins_url('sk-multi-tag/modules/wp-cumulus/tagcloud.swf');
	$path = plugins_url('sk-multi-tag/modules/wp-cumulus/');
	
	// add random seeds to so name and movie url to avoid collisions and force reloading (needed for IE)
	$soname .= rand(0,9999999);
	$movie .= '?r=' . rand(0,9999999);
	$divname .= rand(0,9999999);
	// write flash tag
	if( $options['compmode']!='true' ){
		$flashtag = '<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->';
		$flashtag .= '<script type="text/javascript" src="'.$path.'swfobject.js"></script>';
		$flashtag .= '<div id="'.$divname.'">';
		if( $this->options['showwptags'] == 'true' ){ $flashtag .= '<p>'; } else { $flashtag .= '<p style="display:none;">'; };
		// alternate content
		if( $this->options['mode'] != "cats" ){ $flashtag .= urldecode($tagcloud); }
		if( $this->options['mode'] != "tags" ){ $flashtag .= urldecode($cats); }
		$flashtag .= '</p><p>WP Cumulus Flash tag cloud by <a href="http://www.roytanck.com">Roy Tanck</a> and <a href="http://lukemorton.co.uk/">Luke Morton</a> requires <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> 9 or better.</p></div>';
		$flashtag .= '<script type="text/javascript">';
		$flashtag .= 'var '.$soname.' = new SWFObject("'.$movie.'", "tagcloudflash", "'.$this->options['width'].'", "'.$this->options['height'].'", "9", "#'.$this->options['bgcolor'].'");';
		if( $options['trans'] == 'true' ){
			$flashtag .= $soname.'.addParam("wmode", "transparent");';
		}
		$flashtag .= $soname.'.addParam("allowScriptAccess", "always");';
		$flashtag .= $soname.'.addVariable("tcolor", "0x'.$this->options['tcolor'].'");';
		$flashtag .= $soname.'.addVariable("tcolor2", "0x' . ($this->options['tcolor2'] == "" ? $this->options['tcolor'] : $this->options['tcolor2']) . '");';
		$flashtag .= $soname.'.addVariable("hicolor", "0x' . ($this->options['hicolor'] == "" ? $this->options['tcolor'] : $this->options['hicolor']) . '");';
		$flashtag .= $soname.'.addVariable("tspeed", "'.$this->options['speed'].'");';
		$flashtag .= $soname.'.addVariable("distr", "'.$this->options['distr'].'");';
		$flashtag .= $soname.'.addVariable("mode", "'.$this->options['mode'].'");';
		// put tags in flashvar
		if( $options['mode'] != "cats" ){
			$flashtag .= $soname.'.addVariable("tagcloud", "'.urlencode('<tags>') . $tagcloud . urlencode('</tags>').'");';
		}
		// put categories in flashvar
		if( $options['mode'] != "tags" ){
			$flashtag .= $soname.'.addVariable("categories", "' . $cats . '");';
		}
		$flashtag .= $soname.'.write("'.$divname.'");';
		$flashtag .= '</script>';
	} else {
		$flashtag = '<object type="application/x-shockwave-flash" data="'.$movie.'" width="'.$this->options['width'].'" height="'.$this->options['height'].'">';
		$flashtag .= '<param name="movie" value="'.$movie.'" />';
		$flashtag .= '<param name="bgcolor" value="#'.$this->options['bgcolor'].'" />';
		$flashtag .= '<param name="AllowScriptAccess" value="always" />';
		if( $this->options['trans'] == 'true' ){
			$flashtag .= '<param name="wmode" value="transparent" />';
		}
		$flashtag .= '<param name="flashvars" value="';
		$flashtag .= 'tcolor=0x'.$this->options['tcolor'];
		$flashtag .= '&amp;tcolor2=0x'.$this->options['tcolor2'];
		$flashtag .= '&amp;hicolor=0x'.$this->options['hicolor'];
		$flashtag .= '&amp;tspeed='.$this->options['speed'];
		$flashtag .= '&amp;distr='.$this->options['distr'];
		$flashtag .= '&amp;mode='.$this->options['mode'];
		// put tags in flashvar
		if( $options['mode'] != "cats" ){
			$flashtag .= '&amp;tagcloud='.urlencode('<tags>') . $tagcloud . urlencode('</tags>');
		}
		// put categories in flashvar
		if( $options['mode'] != "tags" ){
			$flashtag .= '&amp;categories=' . $cats;
		}
		$flashtag .= '" />';
		// alternate content
		if( $this->options['mode'] != "cats" ){ $flashtag .= '<p>'.urldecode($tagcloud).'</p>'; }
		if( $this->options['mode'] != "tags" ){ $flashtag .= '<p>'.urldecode($cats).'</p>'; }
		$flashtag .= '<p>WP-Cumulus by <a href="http://www.roytanck.com/">Roy Tanck</a> and <a href="http://lukemorton.co.uk/">Luke Morton</a> requires <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> 9 or better.</p>';
		$flashtag .= '</object>';
	}
	return $flashtag;
    }

    function getOptions(){
	$newoptions['width'] = '160';
	$newoptions['height'] = '160';
	$newoptions['tcolor'] = '333333';
	$newoptions['tcolor2'] = '333333';
	$newoptions['hicolor'] = '000000';
	$newoptions['bgcolor'] = 'ffffff';
	$newoptions['speed'] = '100';
	$newoptions['trans'] = 'false';
	$newoptions['distr'] = 'true';
	$newoptions['args'] = '';
	$newoptions['compmode'] = 'false';
	$newoptions['showwptags'] = 'true';
	$newoptions['mode'] = 'tags';
        $this->options = array_merge($newoptions, SKMultiTag::$admin->options['options'][$this->key]);
    }
}
?>