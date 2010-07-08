<?php

class SKMTFilesystem{

    public static function getImageList($folder){
        return self::getFileList($folder, array('jpg', 'jpeg', 'gif', 'png'));
    }

    /**
     * Restituisce la lista dei file contenuti nella cartella data
     *
     * @param String $folder
     * @param Array $extensions
     * @return Array
     */
    public static function getFileList($folder, $extensions){
        $fileList = array();
        $dir = rtrim($folder, '\\/').DIRECTORY_SEPARATOR;
        if (is_dir($dir) && $dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if(is_file($dir . $file)){
                    if(isset($extensions) && count($extensions) > 0){
                        if(in_array(strtolower(end(explode(".", $file))),  $extensions ))
                            $fileList[] = $file;
                    } else {
                        $fileList[] = $file;
                    }
                }
            }
        }
        closedir($dh);
        return $fileList;
     }
}
?>
