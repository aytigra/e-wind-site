<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigEngine
 *
 * @author e-wind
 */
class Ewind_ConfigEngine {
    //put your code here
    public function getStyleList($pageType = '') {
        $colorTheme = 'sky';
        $styleList = array();
        $styleList[] = 'main.css';
        $styleList[] = 'layout_fix.css';
        $styleList[] = 'sidenav_left.css';
        if (isset($_COOKIE['css_style_theme'])) {
            $colorTheme = $_COOKIE['css_style_theme'];
        }
        $styleList[] = 'color_theme_'.$colorTheme.'.css';
        return $styleList;
    }
    public function setStyle() {
        if(isset($_GET['theme'])) {
            if(file_exists('style/color_theme_'.$_GET['theme'].'.css')) {
                    setcookie('css_style_theme', $_GET['theme'], time()+ (3600*24*360), "/");
            }
	}
    }
}

?>
