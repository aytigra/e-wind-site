
    <div id="content">
        <? if (!$pagePath['entry']) {?>
            <? require_once 'news.php'; ?>
        <?}else {?>
            <h2><?=$pageContent['entry_title']?></h2>
            <?if ( $pageContent['entry_type'] == 'e' && strpos($pageContent['entry_title'], '404')){?>

                <p>Не найдена страница по адресу: <?='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL']?></p>

            <?}?>
            <?=$pageContent['entry_content']?>
        <?}?>
    </div> <!-- End "content" -->
