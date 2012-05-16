<?php
require_once 'lib/php/config.php';
$page_type = 'tools';
$title = 'Исходный код с подсветкой синтаксиса';
require_once "header.php" ;
?>
	<div id="content">
		<div>
			<?php
				if ((isset($_GET['filename'])) &&
					(preg_match('/^(\w|\/|_)+\.(php|css|js)$/Ui', $_GET['filename'])) &&
					(strpos($_GET['filename'], 'connectvars') === false)) {
					include_once 'srcHighliter/geshi.php';
					$pathInfo = pathinfo(__FILE__);
					$fileName = $_GET['filename'];
					$filePath = $pathInfo['dirname'] . $fileName;
					if (file_exists($filePath)) {
						echo("<h2>Содержимое страницы $fileName:</h2>");
						$source = file_get_contents($filePath);
						$file = explode('.', $fileName);
						$extension = $file[count($file) - 1];
						switch ($extension) {
							case "php": $language = "html-plus"; break;
							case "css": $language = "css"; break;
							case "js": $language = "javascript"; break;
						}
						$geshi = new GeSHi($source, $language);
						$colored_source = $geshi->parse_code();
						$patterns = '/(\.{0,2})((\w|\/|_)+\.(php|css|js)(?!\.))/Ui';
						$replase = '$1<a href="/view.php?filename=/$2">$2</a>';
						$urled_csource = preg_replace($patterns , $replase , $colored_source);
						echo $urled_csource;
					}
					else {
						echo "<p>Ты указала страничку, которой нет на сайте ($fileName)</p>";
					}
				}
				else {
					echo "<p>Что-то не так с именем файла, попробуй еще раз</p>";
					$fileName = "/index.php";
				}
			?>
			<form action="view.php" method="get">
				<fieldset>
					<p>Укажи путь и название страницы сайта, которую хочешь просмотреть:</p>
					<input type="text" name="filename" value="<?php echo($fileName); ?>" />
					<input type="submit" value="Go" />
				</fieldset>
			</form>
		</div>
		<p><br /> &nbsp; &nbsp; <a href="<?php echo $fileName ?>">Обратно на страничку</a> &nbsp; &nbsp; <a href="index.php">На главную</a><br /></p>
		<p>Для подсветки синтаксиса используетcя<a href="http://geshi.org/">GeSHi</a>, разработанный <a href="http://qbnz.com/highlighter/">Nigel McNie</a></p>
	</div>
<?php require_once "footer.php"; ?>