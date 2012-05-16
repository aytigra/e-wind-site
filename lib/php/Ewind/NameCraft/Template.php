<?php
$nameCrafter = new Ewind_NameCraft_Crafter();
$errors = '' ;
$name = '' ;
$namestyle = 'CHI' ;
$length = 3 ;
if (isset($_GET['submit'])) {
    if (isset($_GET['namestyle'])) {
        $namestyle = $_GET['namestyle'] ;
        $errors = $nameCrafter->setNameStyle($namestyle);
    }
    if ((is_numeric($_GET['length'])) && ($_GET['length'] <= 10)&& ($_GET['length'] > 0)) {
        $length = $_GET['length'] ;
	$name = $nameCrafter->createName($length) ;
    }
    else
	$errors .='<p class="error">Error: максимальное количество слогов от 1 до 10 символов </p>';
}
?>
	<div id="namecraftcontroller">
	    <form method="GET" action="<?=$_SERVER['REDIRECT_URL']; ?>">
                <fieldset>
                    <fieldset><label><strong><?php echo $errors.$name; ?></strong></label></fieldset>
                    <input id="namelength" name="length" size="1" value="<? echo $length ?>">- максимальная длина имени.
                    <select name="namestyle">
                        <option value="CHI" <?php echo ($namestyle=='CHI'? 'selected':''); ?> >Китайские</option>
                        <option value="JP" <?php echo ($namestyle=='JP'? 'selected':''); ?> >Японские</option>
                        <option value="IND" <?php echo ($namestyle=='IND'? 'selected':''); ?> >Индийские</option>
                    </select>
                    <br /><input type="submit" name="submit" value="Называй">
                </fieldset>
	    </form>
	</div>
