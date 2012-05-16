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
                    <td><input id="addhohliks" name="addhohliks" size="2" value="<? echo $addhohliks ?>"> - хохликов.</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><input id="maxage" name="maxage" size="2" value="<? echo $maxage ?>"> - дней живет</td>
                    <td><input id="maxreprrate" name="maxreprrate" size="2" value="<? echo $maxreprrate ?>"> - макс. рождается хохликов</td>
                    <td>
                    <select name="namestyle">
                        <option value="CHI" <?php echo ($namestyle=='CHI'? 'selected':''); ?> >Китайские</option>
                        <option value="JP" <?php echo ($namestyle=='JP'? 'selected':''); ?> >Японские</option>
                        <option value="IND" <?php echo ($namestyle=='IND'? 'selected':''); ?> >Индийские</option>
                    </select> - имена.
                    </td>
                </tr>
                <tr>
                    <td><input id="growdays" name="growdays" size="2" value="<? echo $growdays ?>"> - дней растет</td>
                    <td><input id="invalidrate" name="invalidrate" size="2" value="<? echo $invalidrate ?>"> - процентов больных(%)</td>
                    <td><input id="minreprest" name="minreprest" size="2" value="<? echo $minreprest ?>"> - дней отдыхают после размножения</td>
                </tr>
            </table>
            <br /><input type="submit" name="submit" value="Живите и трахайтесь!"><input id="days" name="days" size="2" value="<? echo $days ?>">дней
            </fieldset>
            <fieldset><?=$report?></fieldset>
        </form>

</div>
