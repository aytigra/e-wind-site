		<form method="POST" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
                    <fieldset>
			<label for="text">�������� �� ���������</label><br />
			<textarea id="text" name="text" rows="10" cols="60" autofocus></textarea><br />
			<input type="submit" name="submit" value="��������" /><br />


<?php
if(isset($_POST['text'])) {
	$text = $_POST['text']; #��������� ������ �� ���� �����
	if (!empty($text)) { #���������, ��� � ���� ������� �����
		echo '����� �� ��������:<br />' . $text . '<br /><br />';
		$text = strtr($text, '�������������������������������ި', '���������������������������������' ); #��������� ��� ,����� ������� �������
		$text = strtr($text, "\"[]{}'()\-,+|_#/*&^%$@!�;:?.~`",
                                        "                              "); #�������� ��� ������ ������� �� �������
		$text = trim($text); #������� ������� ������� � ��������
		$text = str_replace(" ","",$text); #������� ������� ����� �������
		$reversed_text = strrev($text); #�������������� ������
		echo '������������ ����� ��� ��������:<br />' . $reversed_text . '<br /><br />';
		if ((!empty($text)) && ($text === $reversed_text)) #��������� ����� �� ������ � ������������ �����
			echo '����������, ��� ���������<br />';
		else #����� �� �����
			echo '��, ������� ��� ����� ���������: ' .
                        '<a href="http://ru.wikipedia.org/wiki/%CF%E0%EB%E8%ED%E4%F0%EE%EC" ' .
                        'title="���������� �� ���������">����</a><br />';
	}
	else #������ ���� �����
		echo '�� ����� ���-������, ��� ��.<br />';
}
?>
                    </fieldset>
                </form>