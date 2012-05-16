<?php
define('LE_NUMONPAGE', 5);
$pageNumber = 1;
if (isset($_GET['page']))  $pageNumber = $_GET['page'];
$learns = $dbe->getLastEntries(LE_NUMONPAGE, $pageNumber);
/* @var $dbe Ewind_DBEngine */
$maxPages = $learns['num_of_pages'];
?>

<div id="content">
    <div id="learning">
    <? if (!$pagePath['entry']) {?>
        <h2><?=$pageContent['cat_title']?></h2>
        <p><?=$pageContent['cat_descr']?></p>

        <?if (!isset($learns[0])) {?>

            <p>В текущем разделе ничего не найдено с тегом: <strong><?=$pagePath['tag']?></strong></p>

        <?}else{?>
            <? foreach ($learns as $entry) if (is_array($entry)){?>
            <div id="lesson">
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
            </div> <!-- End "lesson"  -->
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
        <?=$pageContent['entry_descr']?>
        <? require_once "Ewind/".$pageContent['entry_path']."/Template.php"; ?>
        <?=$pageContent['entry_content']?>
        <p id ="entrytime">Последнее обновление было: <?=date("d.m.y", $pageContent['entry_time'])?></p>
    <?}?>
    </div> <!-- End "learning"  -->

</div>
