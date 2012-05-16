<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EngineTest
 *
 * @author e-wind
 */
class Ewind_DBEngineTest {
    function runTest() {
        if ($e = new Ewind_DBEngine()) echo "connect to database - OK<br />\n";
        $page = $e->setPaths($_SERVER['REDIRECT_URL'], @$_GET['tag']);
        echo $_SERVER['REDIRECT_URL'].'<br>';
        ?><pre><?
        //print_r($_SERVER);
        //print_r($_GET);
        echo "\n Setted Path: \n";
        print_r($page);
        echo "\n Page content: \n";
        print_r($e->getContent());
        //echo "\n Top menu content: \n";
        print_r($e->getCatList());
        //print_r($e->getCatList(TRUE));
        //print_r($e->getCatMap());
        print_r($e->getCatMap(TRUE));
        print_r($e->getLastEntries(2, 1));
        print_r($e->getLastEntries(2, 2));
        ?></pre>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset=windows-1251 />
    </head>
<body>
<a href="http://e-wind.lo/main.html">one</a>
<a href="http://e-wind.lo/main/about_me.html">one</a>
<a href="http://e-wind.lo/main/jgfjjh.html">one</a>
<a href="http://e-wind.lo/articles/oop_part_one.html?tag=OOP">два</a>
<a href="http://e-wind.lo/articles.html?tag=OOP">third</a>
</body>
</html>
        <?
    }

}

?>
