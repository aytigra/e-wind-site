		<form method="POST" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
                    <fieldset>
			<label for="text">Проверка на палиндром</label><br />
			<textarea id="text" name="text" rows="10" cols="60" autofocus></textarea><br />
			<input type="submit" name="submit" value="Проверяй" /><br />


<?php
if(isset($_POST['text'])) {
	$text = $_POST['text']; #принимает строку из поля ввода
	if (!empty($text)) { #проверяет, что в поле ввведен текст
		echo 'Текст на проверку:<br />' . $text . '<br /><br />';
		$text = strtr($text, 'ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮЁ', 'йцукенгшщзхъфывапролджэячсмитьбюё' ); #переводит все ,буквы внижний регистр
		$text = strtr($text, "\"[]{}'()\-,+|_#/*&^%$@!№;:?.~`",
                                        "                              "); #заменяет все лишние символы на пробелы
		$text = trim($text); #убирает боковые пробелы и переносы
		$text = str_replace(" ","",$text); #удаляет пробелы между словами
		$reversed_text = strrev($text); #переворачивает строку
		echo 'Перевернутый текст без пробелов:<br />' . $reversed_text . '<br /><br />';
		if ((!empty($text)) && ($text === $reversed_text)) #проверяет равны ли прямое и перевернутое слово
			echo 'Поздравляю, это палиндром<br />';
		else #слова не равны
			echo 'Не, почитай что такое палиндром: ' .
                        '<a href="http://ru.wikipedia.org/wiki/%CF%E0%EB%E8%ED%E4%F0%EE%EC" ' .
                        'title="Посмотреть на википедии">Вики</a><br />';
	}
	else #пустое поле ввода
		echo 'Ну введи что-нибудь, что ли.<br />';
}
?>
                    </fieldset>
                </form>