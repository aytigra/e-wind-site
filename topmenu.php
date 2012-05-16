<div id="topmenu">
    <table>
        <tr>
            <?foreach ($catList as $category) {?>
                <? if ($category['cat_path'] == $pagePath['category'] && !$pagePath['entry'] && !$pagePath['tag'] ) {?>

                    <td id="topmenuelement" class="current"><?=$category['cat_name']?></td>

                <?} else {?>

                    <td id="topmenuelement">
                        <a href="<? echo '/'.$category['cat_path'].PAGE_EXT ?>" title="<?=$category['cat_title']?>"><?=$category['cat_name']?></a>
                    </td>

                <?}?>

            <?}?>
        </tr>
    </table>
</div>
