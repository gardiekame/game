<?php
function showMenu() {
	print <<<_HTML_
	<ul class="menu">
		<li><a href="index.php">遊戲作品</a>
			<ul>
			<li><a href="xboxone.php">Xbox One</a></li>
			<li><a href="ps4.php">PS4</a></li>
			</ul>
		</li>
		<li><a href="#">我的清單</a></li>
		<li><a href="#">分享清單</a></li>
	</ul><br />
_HTML_;
}

function showItem($game) {
	echo
	'<div class="zone2"><br />
	<div class="page_zone">';
	
/*
if(isset($_POST['searchTitle']) && $_POST['searchTitle'] != "")
	$gamePerPage = $gameCounts;*/
//if($gameCounts == 0) echo "查無資料";
	$i = 1;
	foreach( $game as $item) {
		$title = $item['title'];
		$type = $item['type'];
		$platform = $item['platform'];
		$releaseDate = $item['release_date'];
		
		if($releaseDate == "0000-00-00")
			$releaseDate = "未定";

		$imgUrl = $item['img_url'];
		$content = $item['content'];
		
		if($content == "")
			$content = "暫無簡介";
		
		$gId = $item['g_id'];
		

	echo "
	<p id=\"gID$i\" style=\"display: none;\">$gId</p>
	<table class=\"gameItem\">
		<tr>
			<td>
				<button class=\"gameButton\" id=\"$i\" style=\"background-image: url('$imgUrl');border: 0; height: 280; width: 220; background-size: 100%; \"></button>
			</td>
		</tr>
		<tr>
			<td><div style=\"text-align: right;\"><button class=\"editButton\" id=\"e$i\">編輯</button><div/></td>
		</tr>
		<tr>
			<td id=\"title$i\">$title</td>
		</tr>
		<tr>
			<td id=\"type$i\">類型: $type</td>
		</tr>
		<tr>
			<td id=\"plat$i\">平台: $platform</td>
		</tr>
		<tr>";

	if(!($platform == "PC: olg")) {
		echo "<td id=\"date$i\">發售日期: $releaseDate</td>";
	}else {
		echo "<td id=\"date$i\">公測日期: $releaseDate</td>"; }

	echo "
		</tr>
	</table>
	<table class=\"blankSpace\">
		<tr>
			<td></td><td></td><td></td>
		</tr>
		<tr>
			<td></td><td></td><td></td>
		</tr>
	</table>
	<div class=\"contentDialog\" id=\"c$i\" title=\"$title\">
	<p>$content</p></div>";

	if($i % $GLOBALS["itemPerLine"] == 0)
		echo "<br/>";
	$i++;
	}
	echo
	"</div><br />
	</div><br />";

}
?>