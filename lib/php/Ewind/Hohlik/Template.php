<?php
$errors = '' ;
$report = '' ;
$namestyle = 'CHI';
$addhohliks = 20 ;
$maxage = 15 ;
$maxreprrate = 5 ;
$minreprest = 5 ;
$growdays = 3 ;
$invalidrate = 15 ;
$days = 20 ;
if (isset($_GET['submit'])) {
    if (isset($_GET['namestyle'])) $namestyle = $_GET['namestyle'] ;
    if (isset($_GET['maxage'])) $maxage = $_GET['maxage'] ;
    if (isset($_GET['minreprest'])) $minreprest = $_GET['minreprest'] ;
    if (isset($_GET['maxreprrate'])) $maxreprrate = $_GET['maxreprrate'] ;
    if (isset($_GET['growdays'])) $growdays = $_GET['growdays'] ;
    if (isset($_GET['invalidrate'])) $invalidrate = $_GET['invalidrate'] ;
    if (isset($_GET['days'])) $days = $_GET['days'] ;
    if (isset($_GET['addhohliks'])) $addhohliks = $_GET['addhohliks'] ;
    $hoh = new Ewind_Hohlik_Environment() ;
    $errors .= $hoh->setHohlikVille($maxage, $invalidrate, $maxreprrate, $minreprest, $growdays, $namestyle) ;
    $errors .= $hoh->addHohliks($addhohliks) ;
    if (!$errors) $report = $hoh->liveForDays($days) ;
}
?>
<div id="hohlikcontroller">

        <form method="GET" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
            <fieldset>
            <label><strong><?php echo $errors ?></strong></label><br />
            <table>
                <tr>
                    <td><input id="addhohliks" name="addhohliks" size="2" value="<? echo $addhohliks ?>"> - ��������.</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><input id="maxage" name="maxage" size="2" value="<? echo $maxage ?>"> - ���� �����</td>
                    <td><input id="maxreprrate" name="maxreprrate" size="2" value="<? echo $maxreprrate ?>"> - ����. ��������� ��������</td>
                    <td>
                    <select name="namestyle">
                        <option value="CHI" <?php echo ($namestyle=='CHI'? 'selected':''); ?> >���������</option>
                        <option value="JP" <?php echo ($namestyle=='JP'? 'selected':''); ?> >��������</option>
                        <option value="IND" <?php echo ($namestyle=='IND'? 'selected':''); ?> >���������</option>
                    </select> - �����.
                    </td>
                </tr>
                <tr>
                    <td><input id="growdays" name="growdays" size="2" value="<? echo $growdays ?>"> - ���� ������</td>
                    <td><input id="invalidrate" name="invalidrate" size="2" value="<? echo $invalidrate ?>"> - ��������� �������(%)</td>
                    <td><input id="minreprest" name="minreprest" size="2" value="<? echo $minreprest ?>"> - ���� �������� ����� �����������</td>
                </tr>
            </table>
            <br /><input type="submit" name="submit" value="������ � ����������!"><input id="days" name="days" size="2" value="<? echo $days ?>">����
            </fieldset>
            <fieldset><?=$report?></fieldset>
        </form>

</div>
