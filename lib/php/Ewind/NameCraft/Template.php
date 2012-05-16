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
	$errors .='<p class="error">Error: ������������ ���������� ������ �� 1 �� 10 �������� </p>';
}
?>
	<div id="namecraftcontroller">
	    <form method="GET" action="<?=$_SERVER['REDIRECT_URL']; ?>">
                <fieldset>
                    <fieldset><label><strong><?php echo $errors.$name; ?></strong></label></fieldset>
                    <input id="namelength" name="length" size="1" value="<? echo $length ?>">- ������������ ����� �����.
                    <select name="namestyle">
                        <option value="CHI" <?php echo ($namestyle=='CHI'? 'selected':''); ?> >���������</option>
                        <option value="JP" <?php echo ($namestyle=='JP'? 'selected':''); ?> >��������</option>
                        <option value="IND" <?php echo ($namestyle=='IND'? 'selected':''); ?> >���������</option>
                    </select>
                    <br /><input type="submit" name="submit" value="�������">
                </fieldset>
	    </form>
	</div>
