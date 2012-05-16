<?php
if ($pageNumber > $maxPages) $pageNumber = $maxPages;
$pagePrev = $pageNumber - 1;
$pageNext = $pageNumber + 1;
?>
        <div id="pageswitcher">
            <span>Страница: </span>

            <?if ($pagePrev > 0) {?>

                <!-- switch to previous page k -->
                <span class="pageswitch">
                    <a href="<? echo $_SERVER['REDIRECT_URL']."?page=$pagePrev"?>">&lt;&lt;</a>
                </span>

            <?}?>
            <?for ($page = 1; $page <= $maxPages; $page++)
                if ($page == $pageNumber){?>

                    <!-- current page -->
                    <span class="pagenumber"  class="current"><?=$page?></span>

                <?} else {?>

                    <!-- other pages -->
                    <span class="pagenumber">
                        <a href="<? echo $_SERVER['REDIRECT_URL']."?page=$page"?>"><?=$page?></a>
                    </span>

                <?}?>
            <?if ($pageNext <= $maxPages) {?>

                <!-- switch to next page -->
                <span class="pageswitch">
                    <a href="<? echo $_SERVER['REDIRECT_URL']."?page=$pageNext"?>">&gt;&gt;</a>
                </span>

            <?}?>
        </div> <!-- End "pageswitcher" -->

