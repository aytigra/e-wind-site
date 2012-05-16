<?php
define('NW_NUMONPAGE', 2);
$pageNumber = 1;
if (isset($_GET['page']))  $pageNumber = $_GET['page'];
$news = $dbe->getLastEntries(NW_NUMONPAGE, $pageNumber, "news");
/* @var $dbe Ewind_DBEngine */
$maxPages = $news['num_of_pages'];
?>

    <div id="news">
        <h2>Новости сайта.</h2>
        <table>

            <? foreach ($news as $entry) if (is_array($entry)){?>

            <tr>
                <td class="newsdata"><?=date("d.m.y", $entry['entry_time'])?></td>
                <td class="newsentry"><?=$entry['entry_descr']?></td>
            </tr>

            <?}?>

        </table>

        <? require_once 'pageswitcher.php'; ?>
    </div> <!-- End "news" -->
