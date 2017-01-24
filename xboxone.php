<?php
require_once("include/configure.php");
require_once("include/dbFunction.php");
require_once("include/uploadCoverImage.php");
require_once("include/view.php");
require_once("include/header.php");

$dbConn = connectDb($host, $user, $pwd, $dbName);
##----------------------------------------------------------------
$mainSql = "SELECT * FROM $itemTable WHERE platform ='Xbox One' ";
$getCountsSql = "SELECT count(*) FROM $itemTable WHERE platform ='Xbox One' ";

if(isset($_POST['searchTitle']) && $_POST['searchTitle'] !="")
	$searchTitle = htmlspecialchars($_POST['searchTitle']);

if(isset($_POST['selType']) && $_POST['selType'] != "全部") {
	$selType = $_POST['selType'];
	
	if(isset($searchTitle)) {
		$sqlcmd = $getCountsSql . "AND type = '$selType' AND title like '%$searchTitle%'";//AND valid='Y'
		$gameCounts = counts($sqlcmd, $dbConn);
		$sqlcmd = $mainSql . "AND type = '$selType' AND title like '%$searchTitle%' ORDER BY release_date DESC"; 
	}
	else{
		$sqlcmd = $getCountsSql . "AND type = '$selType'";
	    $gameCounts = counts($sqlcmd, $dbConn);
		$sqlcmd = $mainSql . "AND type = '$selType' ORDER BY release_date DESC";
	}
	
}
else{
	
	if(isset($searchTitle)) {
		$sqlcmd = $getCountsSql . "AND title like '%$searchTitle%'";
		$gameCounts = counts($sqlcmd, $dbConn);
		$sqlcmd = $mainSql . "AND title like '%$searchTitle%' ORDER BY release_date DESC";
	}
	else {
		$gameCounts = counts($getCountsSql, $dbConn);
		$sqlcmd = $mainSql . "ORDER BY release_date DESC";
	}
	
}

$totalPage = (int)ceil($gameCounts/$itemPerPage);

if(!isset($_GET['page']) || $_GET['page'] < 1 || $_GET['page'] > $totalPage)
	$page = 1;
else
	$page = $_GET['page'];
if(isset($_GET['lastPage']) && $_GET['page'] > 1 && $_GET['page'] <= $totalPage) 
	$page--;
if(isset($_GET['nextPage']) && $_GET['page'] > 0 && $_GET['page'] < $totalPage) 
	$page++;

$start = ($page - 1)*$itemPerPage;
$sqlCmd = $sqlcmd ." LIMIT $start, $itemPerPage";
##---------------------------------------------------------------
if(isset($_POST['titleAdded']) && $_POST['titleAdded'] != "") {
	
	if($_POST['theOperation'] == "addItem")
		$imageAdded = counts("SELECT MAX(g_id) FROM $itemTable", $dbConn) + 1;
	else if($_POST['theOperation'] == "editItem") {
		$itemId = (int)$_POST["itemId"];
		$imageAdded = (string)($itemId);
	}
		
	if($_FILES["fileToUpload"]["name"] != "")
		uploadCover($_POST['theOperation']);
	
	if(isset($_POST['hasDate']) && $_POST['hasDate'] == "none")
		$dateAdded = null;
	else
		$dateAdded = $_POST['dateAdded'];
	
	if($_POST['theOperation'] == "addItem")
		addGameItem($_POST['titleAdded'], $_POST['typeAdded'], $_POST['platAdded'], 
			$dateAdded, $imageAdded, $dbConn, $itemTable);
	else if($_POST['theOperation'] == "editItem" && $_FILES["fileToUpload"]["name"] != "")
		updateGameItem((string)$itemId, $_POST['titleAdded'], $_POST['typeAdded'], 
			$_POST['platAdded'], $dateAdded, $imageAdded, $dbConn, $itemTable);
	else if($_POST['theOperation'] == "editItem" && $_FILES["fileToUpload"]["name"] == "")
		updateGameItem2((string)$itemId, $_POST['titleAdded'], $_POST['typeAdded'], 
			$_POST['platAdded'], $dateAdded, $dbConn, $itemTable);
}

$game = queryDb($sqlCmd, $dbConn);
?>

<body>

<?php
showMenu();
?>

<div class="block">
<form class="operation" method="POST" action="">
	<input class="add" type="button" value="新增">
</form>
</div>

<form class="type" method="POST" action="">
	類型: 
	<select name="selType" onchange="submit();">
		<option value="全部"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "全部") echo "selected"?>>全部</option>
		<option value="動作"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "動作") echo "selected"?>>動作</option>
		<option value="冒險"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "冒險") echo "selected"?>>冒險</option>
		<option value="角色扮演"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "角色扮演") echo "selected"?>>角色扮演</option>
		<option value="射擊"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "射擊") echo "selected"?>>射擊</option>
		<option value="競速"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "競速") echo "selected"?>>競速</option>
		<option value="益智解謎"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "益智解謎") echo "selected"?>>益智解謎</option>
		<option value="策略模擬"
		<?php if( isset($_POST['selType']) && $_POST['selType'] == "策略模擬") echo "selected"?>>策略模擬</option>
	</select>
	
	<div class="block2-2">
	<input type="text" name="searchTitle"  size="10">
	<button class="searchActive" onclick="submit();" >搜尋</button>
	</div>
</form>


<?php
if(count($game) != 0)
	showItem($game);
else
	echo '<div style="text-align: center;">查無項目</div>';
?>

<div class="page">
<form name="selPage" method="GET" action="">
	<?php
	if($page > 1)
		echo "<button class=\"lastPage\" type=\"submit\" name=\"lastPage\" ></button>";

	if($totalPage > 1) {
		echo '<select name="page" onchange="submit();">';
		
		for ($p=1; $p<=$totalPage; $p++) { 
			echo '  <option value="' . $p . '"';
			if ($p == $page) echo ' selected';
			echo ">P.$p</option>\n";
		}
		
		echo '</select>';
	}
	
	if($page < $totalPage) 
		echo "<button class=\"nextPage\" type=\"submit\" name=\"nextPage\" ></button>";
	?>
</form>
</div>

<div id="operationDialog" style="text-align:center;">
<form method="POST" action="" id="addItem" enctype="multipart/form-data">
	名稱:
	<input id="addTitle" name="titleAdded" type="text" size="10" autofocus>
	<p id="titleInfo" style="color:red;"></p>
	平台:
	<select id="addPlat" name="platAdded" >
		<option value="PC">PC</option>
		<option value="PC online">PC online</option>
		<option value="PS4">PS4</option>
		<option value="Xbox One">Xbox One</option>
		<option value="Wii U">Wii U</option>
		<option value="PSV">PSV</option>
		<option value="3DS">3DS</option>
	</select><br/><br/>
	類型:
	<select id="addType" name="typeAdded" >
		<option value="動作">動作</option>
		<option value="冒險">冒險</option>
		<option value="角色扮演">角色扮演</option>
		<option value="射擊">射擊</option>
		<option value="競速">競速</option>
		<option value="益智解謎">益智解謎</option>
		<option value="策略模擬">策略模擬</option>
	</select><br/><br/>
	發售日期:
	<input type="date" id="addDate" name="dateAdded" min= "1970-01-01" max="2030-12-31">
	<input type="checkbox" name="hasDate" value="none">未定<br/><br/>
	<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
	封面圖片:
	<input type="file" name="fileToUpload" id="fileToUpload"><br/><br/>
	<input class="submitAdd" type="submit" value="確認" form="addItem">
	<input class="cancelAdd" type="button" value="取消">
	<input id="theOperation" name="theOperation" type="text" value="" style="display: none;">
	<input id="itemId" name="itemId" type="text" value="" style="display: none;">
</form></div>

<script>
$("#operationDialog").dialog({
	width: 430,
	position: { my: "center", at: "top", of: window },
	autoOpen: false
});

$("div.contentDialog").each(function() {
	$(this).dialog({
			autoOpen: false,
	});
});

$(document).ready(function() {
	$("input.add").click(function(event){
		$("#operationDialog").dialog({
			title: "新增作品"
		});
		
		document.getElementById("theOperation").value = "addItem";
		document.getElementById("addTitle").value = "";
		document.getElementById("addPlat").value = "PC";
		document.getElementById("addType").value = "動作";
		document.getElementById("addDate").value = "";
		
		$("#operationDialog").dialog("open");
	});
	
	$("button.editButton").click(function(event) {
		$("#operationDialog").dialog({
			title: "編輯作品"
		});
		
		document.getElementById("theOperation").value = "editItem";
		var itemNo = event.target.id.slice(1);
		document.getElementById("itemId").value = document.getElementById("gID"+itemNo).innerHTML;
		document.getElementById("addTitle").value = document.getElementById("title"+itemNo).innerHTML;
		document.getElementById("addPlat").value = document.getElementById("plat"+itemNo).innerHTML.slice(4);
		document.getElementById("addType").value = document.getElementById("type"+itemNo).innerHTML.slice(4);
		document.getElementById("addDate").value = document.getElementById("date"+itemNo).innerHTML.slice(6);
		
		$("#operationDialog").dialog("open");
	});
	/*$("input.submitAdd").click(function(event){
		var theTitle = document.getElementById("addTitle").value;
		if(theTitle === "")
			document.getElementById("titleInfo").innerHTML = "請輸入名稱";
	});*/
	
	$("input.cancelAdd").click(function(event){
		$("#operationDialog").dialog("close");
		document.getElementById("addTitle").value = "";
		document.getElementById("titleInfo").innerHTML = "";
	});
	
    $("button.gameButton").click(function(event) {
        var contentPanelId = event.target.id;
		var cid = "#c" + contentPanelId.toString();
		
		$(cid).dialog({
			position: { my: "center", at: "center", of: event }
		});
		
		$(cid).dialog("open");		
    });
});
</script>

</body>
</html>