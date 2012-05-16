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

            <p>� ������� ������� ������ �� ������� � �����: <strong><?=$pagePath['tag']?></strong></p>

        <?}else{?>
            <? foreach ($aricles as $entry) if (is_array($entry)){?>
            <div id="article">
                <h3>
                    <a href="<? echo '/'.$pagePath['category'].'/'.$entry['entry_path'].PAGE_EXT ?>"><?=$entry['entry_title']?></a>
                </h3>
                <div id="tags">
                    <p>
                        <span>����:</span>

                        <? foreach ($entry['tag_list'] as $tag) {?>

                        <span class="tag">
                            <a href="<? echo $_SERVER['REDIRECT_URL']."?tag=$tag"?>"><?=$tag?></a>
                        </span>

                        <?}?>

                    <p>
                </div> <!-- End "tags"  -->
                <p><?=$entry['entry_descr']?></p>
                <p id ="entrytime">��������� ���������� ����: <?=date("d.m.y", $entry['entry_time'])?></p>
            </div> <!-- End "article"  -->
            <?}?>

            <? require_once 'pageswitcher.php'; ?>
        <?}?>
    <?}else {?>

        <h2><?=$pageContent['entry_title']?></h2>
           <div id="tags">
                <p><span>����:</span>

                <? foreach ($pageContent['tag_list'] as $tag) {?>

                    <span class="tag">
                        <a href="<? echo "/".$pagePath['category'].PAGE_EXT."?tag=$tag"?>"><?=$tag?></a>
                    </span>

                <?}?>

                <p>
            </div> <!-- End "tags"  -->
        <?=$pageContent['entry_content']?>
        <p id ="entrytime">��������� ���������� ����: <?=date("d.m.y", $pageContent['entry_time'])?></p>
    <?}?>
    </div> <!-- End "articles"  -->

</div>

<!--
    <dl>
            <dt><a href="../articles/oop_part_one.html" title="��������� �������� ���������������� ����������������">������� ��� ���</a></dt>
            <dd>��������� �������� ���������������� ���������������, ��������� �������� ������ � ��������������� ���������, ������� ��� �������� ������ �� ������ ������� �������� ��� ����� ��� � ������ ��� ����� :)</dd>
            <dt><a href="" title="��� �������� ���� ����">��� �������� ������ ����</a></dt>
            <dd>������ � ��� ��� �������� ���� ������ ���������� �� �����, � ������� �������������� ������������ ����� � ������������ ��������.</dd>
    </dl>
-->