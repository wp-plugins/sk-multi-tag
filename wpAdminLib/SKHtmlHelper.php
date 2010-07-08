<?php

class SKMTHtmlHelper{

    /**
     * Builds tables
     * @param <type> $content
     * @param <type> $header
     * @param <type> $args
     * @param <type> $css
     * @return <type>
     */
    public static function table($content, $header = array(), $args = array(), $css = array()){
            return '<table '.self::htmlArgs($args).' '.self::cssArgs($css).'>'
                .self::tableRows($header, 'th')
                .self::tableRows($content, 'td')
                .'</table>';
    }

    /**
     * Una tabella con form per la cancellazione delle voci nella lista
     *
     * @param <type> $content
     * @param <type> $header
     * @param <type> $args
     * @param <type> $css
     * @return <type>
     */
    public static function tableFormDelete($content, $header = array(), $args = array(), $css = array(), $prefix = ''){
            // Valore di default per l'intestazione della colonna per la cancellazione
            if(count($header) == count(end($content))){
                $header[] = 'Elimina';
            }

            // Aggiunta della colonna per la cancellazione
            foreach($content as $k => $v){
                $content[$k][] = '<input type="checkbox" name="'.$prefix.'['.$k.']" value="'.$k.'"/>';
            }
            
            return '<table '.self::htmlArgs($args).' '.self::cssArgs($css).'>'
                .self::tableRows($header, 'th')
                .self::tableRows($content, 'td')
                .'</table>';
    }

    /**
     * Returns table rows from array
     * @param <type> $data
     * @param <type> $cellType
     * @return <type>
     */
    public static function tableRows($data, $cellType = 'td'){
        $return = '';
        if(isset($data) && is_array($data) && count($data) > 0){
            if(is_string($data[0]))
                return '<tr><'.$cellType.'>'.implode('</'.$cellType.'><'.$cellType.'>', $data).'</tr>';
            $i = 0;
            foreach($data as $v){
                $rowClass = ($i%2 == 0)? 'even' : 'odd';
                $return .= '<tr class="'.$rowClass.'"><'.$cellType.'>'.implode('</'.$cellType.'><'.$cellType.'>', $v).'</tr>';
                $i++;
            }
        }
        return $return;
    }

    /**
     * Unrolls html attributes
     * @param <type> $data
     * @return <type>
     */
    public static function htmlArgs($data){
        $return = '';
        if(isset($data) && is_array($data) && count($data) > 0){
            foreach($data as $k => $v){
                $return .= $k.'="'.$v.'" ';
            }
        }
        return ($return != '')? ' '.$return : '';
    }

    /**
     * Unrolls css attributes
     * @param <type> $data
     * @return <type>
     */
    public static function cssArgs($data){
        $return = '';
        if(isset($data) && is_array($data) && count($data) > 0){
            foreach($data as $k => $v){
                $return .= $k.':'.$v.';';
            }
        }
        return ($return != '')?  ' style="'.$return.'"' : '';
    }

    public static function radios($array, $name){
        if(empty($array)) return '';
        $return = '';
        $c = 'checked';
        foreach($array as $k => $v){
            $return .= '<p><label for="radio_'.$k.'">'.$v.'</label><input type="radio" '.$c.' id="radio_'.$k.'" name="'.$name.'" value="'.$k.'"/></p>';
            $c = '';
        }
        return $return;
    }

    public static function selectSimple($array, $name){
        if(empty($array)) return '';
        $return = '<select name="'.$name.'">';
        foreach($array as $k => $v){
            $return .= '<option value="'.$k.'">'.$v.'</option>';
        }
        return $return.'</select>';
    }

    public static function select($array, $name, $selected, $label){
        if(empty($array)) return '';
        $label = '<label>'.$label.'</label>';
        $return = '<p>'.$label.'<select name="'.$name.'">';
        foreach($array as $k => $v){
            $sel = ($k == $selected)? 'selected' : '';
            $return .= '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
        }
        return $return.'</select></p>';
    }

    public static function textInput($name, $default = '', $label = ''){
        if(!empty($label)) $label = '<label>'.$label.'</label>';
        return '<p>'.$label.'<input name="'.$name.'" type="text" value="'.$default.'" /></p>';;
    }
}
?>
