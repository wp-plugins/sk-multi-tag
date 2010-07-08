<?php
class SKMTjQueryUtils{

    /**
     * Restituisce il markup per la creazione dei tab jQuery a partire da una array
     *
     * @param <type> $id l'id del contenitore da usare con
     *      $(function() {
     *		$("#ID").tabs();
     *      });
     * @param <type> $data un array Titolo del tab => Contenuto del box di testo
     * @return <type>
     */
    public static function makeTabs($id, $data){
        if(is_array($data) && count($data) > 0){
            $tabs = '<div id="'.$id.'">';
            $tButtons = '';
            $tContent = '';
            $i = 0;
            foreach($data as $key => $val){
                $i++;
                $tButtons .= '<li><a href="#'.$id.$i.'">'.$key.'</a></li>';
                $tContent .= '<div id="'.$id.$i.'"><div>'.$val.'</div></div>';
            }
            return '<div id="'.$id.'">'.'<ul>'.$tButtons.'</ul>'.$tContent.'</div>';
        } else {return '';}
    }
}
?>
