<?php
setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
require_once 'lib/php/config.php';
define('PAGE_EXT', '.html');

class EwindFrontController {
    public static function run() {
        ob_start();
        try {
            require_once 'adminvars.php';
            if ($_SERVER['REDIRECT_URL'] == ('/'.AD_LOGIN)) {
                require_once 'admin.php';
            }
            elseif ($_SERVER['REDIRECT_URL'] == ('/setstyle')) {
                $ce = new Ewind_ConfigEngine();
                $ce->setStyle();
                header("Location:".$_SERVER['HTTP_REFERER']);
                exit();
            }
            else {
                $dbe = new Ewind_DBEngine();
                $ce = new Ewind_ConfigEngine();
                $pagePath = $dbe->setPaths($_SERVER['REDIRECT_URL'], @$_GET['tag']);
                $pageContent = $dbe->getContent();
                if (isset($pageContent['entry_path'])) {
                    $title = $pageContent['cat_name'] . " - " . $pageContent['entry_name'];
                }
                elseif ($pagePath['tag']) {
                    $title = $pageContent['cat_name'].' - поиск по тегу <strong>'.$pagePath['tag'].'<strong>';
                }
                else {
                    $title = $pageContent['cat_title'];
                }
                $styleList = $ce->getStyleList(@$pageContent['entry_type']);
                require_once 'head.php';
                require_once 'header.php';

                $catList = $dbe->getCatList();
                require_once 'topmenu.php';

                $catMap = $dbe->getCatMap();
                require_once 'sidemenu.php';

                require_once "{$pagePath['category']}/{$pagePath['category']}.php";

                require_once 'footer.php';
            }
        }
        catch (Exception $e){
            ?><pre><?
            print_r($e);
            ?></pre><?
        }
        ob_end_flush();
    }
    public static function test() {
        $engTest = new Ewind_DBEngineTest();
        $engTest->runTest();
    }

}
//EwindFrontController::test();
EwindFrontController::run();
?>
