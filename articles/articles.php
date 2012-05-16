<?php
define('AR_NUMONPAGE', 5);
$pageNumber = 1;
if (isset($_GET['page']))  $pageNumber = $_GET['page'];
$aricles = $dbe->getLastEntries(AR_NUMONPAGE, $pageNumber);
/* @var $dbe Ewind_DBEngine */
$maxPages = $aricles['num_of_pages'];
?>

<div id="content">
    <div id="articles">
    <? if (!$pagePath['entry']) {?>
        <h2><?=$pageContent['cat_title']?></h2>
        <p><?=$pageContent['cat_descr']?></p>

        <?if (!isset($aricles[0])) {?>

            <p>В текущем разделе ничего не найдено с тегом: <strong><?=$pagePath['tag']?></strong></p>

        <?}else{?>
            <? foreach ($aricles as $entry) if (is_array($entry)){?>
            <div id="article">
                <h3>
                    <a href="<? echo '/'.$pagePath['category'].'/'.$entry['entry_path'].PAGE_EXT ?>"><?=$entry['entry_title']?></a>
                </h3>
                <div id="tags">
                    <p>
                        <span>Теги:</span>

                        <? foreach ($entry['tag_list'] as $tag) {?>

                        <span class="tag">
                            <a href="<? echo $_SERVER['REDIRECT_URL']."?tag=$tag"?>"><?=$tag?></a>
                        </span>

                        <?}?>

                    <p>
                </div> <!-- End "tags"  -->
                <p><?=$entry['entry_descr']?></p>
                <p id ="entrytime">Последнее обновление было: <?=date("d.m.y", $entry['entry_time'])?></p>
            </div> <!-- End "article"  -->
            <?}?>

            <? require_once 'pageswitcher.php'; ?>
        <?}?>
    <?}else {?>

        <h2><?=$pageContent['entry_title']?></h2>
           <div id="tags">
                <p><span>Теги:</span>

                <? foreach ($pageContent['tag_list'] as $tag) {?>

                    <span class="tag">
                        <a href="<? echo "/".$pagePath['category'].PAGE_EXT."?tag=$tag"?>"><?=$tag?></a>
                    </span>

                <?}?>

                <p>
            </div> <!-- End "tags"  -->
        <?=$pageContent['entry_content']?>
        <p id ="entrytime">Последнее обновление было: <?=date("d.m.y", $pageContent['entry_time'])?></p>
    <?}?>
    </div> <!-- End "articles"  -->

</div>

<!--
    <dl>
            <dt><a href="../articles/oop_part_one.html" title="Парадигма объектно ориентированного программирования">Понятно про ООП</a></dt>
            <dd>Парадигма объектно ориентированного програмирования, написаная понятным языком с запоминающимися примерами, надеюсь что прочитав статью ты будешь отлично понимать что такое ООП и почему это клево :)</dd>
            <dt><a href="" title="Как защитить свою инфу">Как защитить личную инфу</a></dt>
            <dd>Статья о том как защитить свою личную информацию на компе, с помощью зашифрованного виртуального диска и портабельных программ.</dd>
    </dl>
-->