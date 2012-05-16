<?php
/**
 *  Autorisation
 */
$PowerAuth_password = AD_PASSWORD;
require_once 'PowerAuth.php';


$dbe = new Ewind_DBEngine();
$categ_id = isset($_GET['categ_id']) ? intval($_GET['categ_id']) : 0 ;
$entry_id = isset($_GET['entry_id']) ? intval($_GET['entry_id']) : 0 ;
$addnew = isset($_GET['new']) ? 1 : 0 ;
$tagedit = isset($_GET['tagedit']) ? 1 : 0 ;
$ae = new Ewind_AdminEngine($dbe);
$ae->getData($categ_id, $entry_id, $addnew, $tagedit);
$tagsString = '';
foreach ($ae->data['alltags'] as $tag) {
    $tagsString .= $tag['tag_name'].', ';
}
$respond = $ae->doIt($categ_id, $entry_id, $addnew, $tagedit);
$confirmButtonState = '';
$fieldState = '';
$saveButtonState = '';
$delButtonState = '';
$delFlagState = '';
if ($respond['confirm']) {
    $fieldState = ' readonly class="disabled"';
    $saveButtonState = ' disabled hidden ';
    $delButtonState = ' disabled hidden ';
}
else {
    $confirmButtonState = ' disabled hidden ';
    if ($addnew) $delButtonState = ' disabled ';
}
if (isset($_GET['tagedit'])){
    $delButtonState = ' disabled hidden ';
}
if (!isset($_POST['delete'])) {
    $delFlagState = ' disabled ';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link rel="stylesheet" href="/style/admin.css" />
	<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
			tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",
			plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",


			// Skin options
			//skin : "o2k7",
			//skin_variant : "silver",

			// Example content CSS (should be your site CSS)
			//content_css : "css/example.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "js/template_list.js",
			external_link_list_url : "js/link_list.js",
			external_image_list_url : "js/image_list.js",
			media_external_list_url : "js/media_list.js"

		});
	</script>

</head>
<body>
<div>

    <div id="adminmenu">
        <p>Карта сайта</p>
        <p>
            <a href="/<?=AD_LOGIN?>">В начало</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/<?=AD_LOGIN?>?logout=1">Разлогиниться</a>
        </p>
        <p><a href="/<?=AD_LOGIN?>?tagedit=1">Редактировать теги</a></p>
        <?$catList = $dbe->getCatList(TRUE);?>
        <?foreach ($catList as $category) {
            $categ_url = '/'.AD_LOGIN.'?categ_id='.$category['cat_id'];
            $categ_name = 'ID: '.$category['cat_id'].': '.$category['cat_name'];
            $newcateg_url = '/'.AD_LOGIN.'?new=1';
            $newentry_url = '/'.AD_LOGIN.'?categ_id='.$category['cat_id'].'&new=1';
            ?>

            <span id="foldingbutton<?=$category['cat_id']?>">-</span>

            <? if ($category['cat_id'] == $categ_id && !$entry_id && !$addnew) {?>

                <span id="categ"><?=$categ_name?></span>

            <?} else {?>

                <span id="categ">
                    <a href="<?=$categ_url?>"><?=$categ_name?></a>
                </span>

            <?}?>

            <br />
            <span id="newentry" >
                &nbsp;&boxur;&nbsp;
                <a href="<?=$newentry_url?>"> + add new</a>
            </span>
            <br />
            <div id="categ<?=$category['cat_id']?>">

            <? $catMap = $dbe->getCatMap(TRUE, $category['cat_id']);
            foreach ($catMap as $entry) {
                $entry_name = ' -|'.$entry['entry_state'].
                              '|'. ($entry['entry_type'] ? $entry['entry_type'] : '-' ).
                              '|'.date('d.m.y', $entry['entry_time']).
                              '| '.($entry['entry_name'] ? $entry['entry_name'] : 'no-name') ;
                $entry_url = '/'.AD_LOGIN.'?categ_id='.$category['cat_id'].
                             '&entry_id='.$entry['entry_id'];
                ?>

                &nbsp;&boxur;&nbsp;

                <? if ($entry['entry_id'] == $entry_id) {?>

                    <span id="entry" class="current"><?=$entry_name?></span>

                <?} else {?>

                    <span id="entry">
                        <a href="<?=$entry_url?>"><?=$entry_name?></a>
                    </span>

                <?}?>

                <br />

            <?}?>
            </div>
        <?}?>
        <span id="newcateg" >
            <a href="<?=$newcateg_url?>"> + add new category</a>
        </span>
    </div>

    <div id="content">

        <h3><?=$ae->data['about']?></h3>
        <pre class="error"><?=$respond['message']?></pre>

        <?if ($categ_id || $addnew || isset($_GET['tagedit'])) {?>

        <form name="admin" action="<?=AD_LOGIN.'?'.$_SERVER['QUERY_STRING']?>" method="POST">
            <table>
            <?if (isset($_GET['tagedit'])) {?>

                <table>
                 <tr>
                     <th>Имя тега</th><th>Удалить</th><th></th>
                 </tr>
                <?for($i=0; $i < count($ae->data['alltags']); $i++) {
                    $tag = $ae->data['alltags'][$i];
                    if (!isset($tag['del'])) $tag['del'] = 0;
                    if (!isset($tag['mod'])) $tag['mod'] = 0;
                    $tagchecked = '';
                    $tagmodified = 'disabled';
                    $tagmodifiednotice = '';
                    $tagdeletenotice = '';
                    if ($tag['mod']) {
                        $tagmodified = '';
                        $tagmodifiednotice = '- будет изменен!';
                    }
                    if ($tag['del'] && $tag['tag_id'] != 1) {
                        $tagchecked = 'checked';
                        $tagdeletenotice = ' - будет удален!';
                        $tagmodifiednotice = '';
                    }
                    ?>

                    <tr>
                        <td><input type="text" name="alltags[<?=$i?>][tag_name]" class="alltagsname" <?=$fieldState?>
                                value="<?=$tag['tag_name']?>" /></td>
                            <input type="hidden" name="alltags[<?=$i?>][tag_id]" <?=$fieldState?>
                                value="<?=$tag['tag_id']?>"  />
                        <td> -
                            <input type="hidden" name="alltags[<?=$i?>][del]" <?=$fieldState?>
                                value="0"  />
                            <input type="checkbox" name="alltags[<?=$i?>][del]" <?=$fieldState?>
                                value="1" <?=$tagchecked?>/>
                            <input type="hidden" name="alltags[<?=$i?>][mod]" <?=$fieldState?>
                                value="1"  <?=$tagmodified?>/> -
                        </td>
                        <td>
                        <span class="tagmodifiednotice"><?=$tagmodifiednotice?></span>
                        <span class="tagdeletenotice"><?=$tagdeletenotice?></span>
                        </td>
                    </tr>

                <?}?>

                </table>

            <?}?>
            <?if ($categ_id || $addnew) {?>
                <table>
                    <tr>
                        <td><label name="path">Путь</label></td>
                        <td><input type="text" name="path" id="datapath" <?=$fieldState?>
                                value="<?=$ae->data['path']?>" /></td>
                    </tr>
                    <tr>
                        <td><label name="name">Имя</label></td>
                        <td><input type="text" name="name" id="dataname" <?=$fieldState?>
                                value="<?=$ae->data['name']?>" /></td>
                    </tr>

                <?if ($entry_id || ($categ_id && $addnew)) {?><!-- Entry specific -->
                    <tr>
                        <td><label name="time">Время</label></td>
                        <td><input type="text" name="time" id="datatime" <?=$fieldState?>
                                value="<?=$ae->data['time']?>" />ДД.ММ.ГГ</td>
                    </tr>
                    <tr>
                        <td><label name="type">Тип</label></td>
                        <td><input type="text" name="type" id="datatype" <?=$fieldState?>
                                value="<?=$ae->data['type']?>" />"n"-обычная "e"-страница ошибки</td>
                    </tr>
                    <tr>
                        <td><label name="tags">Теги</label></td>
                        <td><input type="text" name="tags" id="datatags" <?=$fieldState?>
                                value="<?=$ae->data['tags']?>" /> перечислить через запятую, первый станет основным</td>
                    </tr>
                    <tr>
                        <td>Уже есть теги - </td><td>&quot; <?=$tagsString?>&quot;</td>
                    </tr>
                    <tr>
                        <td><label name="cat_id">ID группы</label></td>
                        <td><input type="text" name="cat_id" id="datacatid" <?=$fieldState?>
                                value="<?=$ae->data['cat_id']?>" />Можно перенести в другую, в несуществующей не найдешь</td>
                    </tr>
                <?}else{?><!-- Category specific -->
                    <tr>
                        <td><label name="serialnum">Порядковый номер</label></td>
                        <td><input type="text" name="serialnum" id="dataserialnum" <?=$fieldState?>
                                value="<?=$ae->data['serialnum']?>" /></td>
                    </tr>
                <?}?>

                    <tr>
                        <td><label name="title">Заголовок</label></td>
                        <td><input type="text" name="title" id="datatitle" size="100"<?=$fieldState?>
                                value="<?=$ae->data['title']?>" /></td>
                    </tr>

                </table>
                <br /><label name="descr">Описание:</label><br />
                <textarea name="descr" id="datadescr" rows="5" cols="100"
                <?=$fieldState?> ><?=$ae->data['descr']?></textarea>

                <?if ($entry_id || ($categ_id && $addnew)) {?><!-- Entry specific -->
                <br /><label name="content">Контент:</label><br />
                <textarea name="content" id="datacontent" rows="20" cols="100"
                    <?=$fieldState?> ><?=$ae->data['content']?></textarea>

                <?}?>

                <br />
                <label name="state">Опубликовать?</label>
                <input type="radio" name="state" id="datastateno" <?=$fieldState?>
                    value="0" <?=($ae->data['state'] ? '' : 'checked="checked"')?> />Нет
                <input type="radio" name="state" id="datastateyes" <?=$fieldState?>
                    value="1" <?=($ae->data['state'] ? 'checked="checked"' : '')?> />Да
                <br />
            <?}?>
            <input type="submit" value="Сохранить" name="save" id="datasave" <?=$saveButtonState?>/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" value="Удалить" name="delete" id="datadelete" <?=$delButtonState?>/>
            <input type="submit" value="ОК" name="OK" id="OK" <?=$confirmButtonState?>/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" value="Отмена" name="cansel" id="cansel" <?=$confirmButtonState?>/>

            <input type="hidden" name="del_flag" value="1" id="del_flag" <?=$delFlagState?>/>
        </form>
        <?}?>
    </div>

</div>
</body>
</html>
