<?php
$myRainbow = new Ewind_Rainbow_Crafter(array(400, 50), 'html');
$errors = '' ;
if (isset($_POST['submit'])) {
    if ((is_numeric($_POST['x']))&& (is_numeric($_POST['y'])) && ($_POST['x'] <= 760) && ($_POST['y'] <= 500))
	$myRainbow->setSize(array($_POST['x'],$_POST['y']));
    else
	$errors ='<p class="error">Error: Размеры должны быть числами и не превышать 760х500, </p>';

    if (isset($_POST['colors'])) $errors .= $myRainbow->setColors($_POST['colors']);
    if (isset($_POST['level']))	    $myRainbow->setBlurLevel($_POST['level']);
    if (isset($_POST['shuffle']))   $myRainbow->shuffleRainbow();
    if (isset($_POST['reverse']))   $myRainbow->reverseRainbow();
    if (isset($_POST['reset']))	    $myRainbow->resetColors();
}
$rainbowCode = $myRainbow->createRainbow();
$colors = implode(' ', $myRainbow->getColors());
$size = $myRainbow->getSize();
$blur = $myRainbow->getBlurLevel();
?>
<div id="rainbowcontroller">
    <?php echo $rainbowCode['css']; ?>
    <?php echo $rainbowCode['html'].'<br />'.$errors; ?>
    <p><br />Управление радугой:</p>
    <form name="rwcontrol" method="POST" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
	<fieldset>
	   <label for="colors">Пиши буквы цветов -
		<strong>к</strong>аждый <strong>о</strong>хотник <strong>ж</strong>елает
		<strong>з</strong>ать <strong>г</strong>де <strong>с</strong>идит
		<strong>ф</strong>азан.<br />Или вставь любой текст и смотри че за радуга из него получится :)</label>
	    <input id="colors" name="colors" value="<? echo $colors ?>" size="100"><br />
	<table>
	    <tr>
	    <td>Ширина:</td><td><input id="size" name="x" value="<? echo $size[0] ?>"></td>
	    <td><input type="checkbox" name="reverse" value="1"></td><td>-Перевернуть .</td>
	    <td><input type="checkbox" name="shuffle" value="1"></td><td>-Перемешать .</td>
	    <td><input type="checkbox" name="reset" value="1"></td><td>-Сбросить цвета .</td>
	    </tr>
	    <tr>
	    <td>Высота:</td><td><input id="size" name="y" value="<? echo $size[1] ?>"></td>
	    <td colspan="3">Уровень размытия:</td>
	    <td>
		<input type="radio" name="level" value="0" <? if ($blur == 0) echo 'checked="checked"'?>>
		<input type="radio" name="level" value="1" <? if ($blur == 1) echo 'checked="checked"'?>>
		<input type="radio" name="level" value="2" <? if ($blur == 2) echo 'checked="checked"'?>>
		<input type="radio" name="level" value="3" <? if ($blur == 3) echo 'checked="checked"'?>>
		<input type="radio" name="level" value="4" <? if ($blur == 4) echo 'checked="checked"'?>>
	    </td>
	    </tr>
	</table>
	<input id ="submit" type="submit" name="submit" value="Проверяй" /><br />
	</fieldset>
    </form>
    <?php
	$grRainbow = new Ewind_Rainbow_Crafter($myRainbow->getSize(), 'png', 'learning/rainbow.png');
	$grRainbow->setColors($myRainbow->getColors());
	$grRainbow->setBlurLevel($blur);
	$grRainbow->createRainbow();
    ?>
    <p><br />Это же в виде картинки, можно сохранить на комп.
    <br /><img src="rainbow.png" alt="тут должна была быть радуга"></p>
</div>

