<div id="sidemenu">

    <?foreach ($catMap as $entry) {?>
        <? if ($entry['entry_path'] == $pagePath['entry']) {?>

            <div id="sidemenuelement" class="current"><?=$entry['entry_name']?></div>

        <?} else {?>

            <div id="sidemenuelement">
                <a href="<? echo '/'.$pagePath['category']."/".$entry['entry_path'].PAGE_EXT ?>" title="<?=$entry['entry_title']?>"><?=$entry['entry_name']?></a>
            </div>

        <?}?>
    <?}?>

</div>