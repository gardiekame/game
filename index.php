<?php
require_once("include/configure.php");
require_once("include/dbFunction.php");
require_once("include/uploadCoverImage.php");
require_once("include/view.php");
require_once("include/header.php");

$dbConn = connectDb($host, $user, $pwd, $dbName);
##----------------------------------------------------------------
$mainSql = "SELECT * FROM $itemTable ";
$getCountsSql = "SELECT count(*) FROM $itemTable ";

if(isset($_POST['searchTitle']) && $_POST['searchTitle'] !="")
	$searchTitle = htmlspecialchars($_POST['searchTitle']);

if(isset($_POST['selType']) && $_POST['selType'] != "全部") {
	$selType = $_POST['selType'];
	
	if(isset($searchTitle)) {
		$sqlcmd = $getCountsSql . "WHERE type = '$selType' AND title like '%$searchTitle%'";//AND valid='Y'
		$gameCounts = counts($sqlcmd, $dbConn);
		$sqlcmd = $mainSql . "WHERE type = '$selType' AND title like '%$searchTitle%' ORDER BY release_date DESC"; 
	}
	else{
		$sqlcmd = $getCountsSql . "WHERE type = '$selType'";
	    $gameCounts = counts($sqlcmd, $dbConn);
		$sqlcmd = $mainSql . "WHERE type = '$selType' ORDER BY release_date DESC";
	}
	
}
else{
	
	if(isset($searchTitle)) {
		$sqlcmd = $getCountsSql . "WHERE title like '%$searchTitle%'";
		$gameCounts = counts($sqlcmd, $dbConn);
		$sqlcmd = $mainSql . "WHERE title like '%$searchTitle%' ORDER BY release_date DESC";
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

$sqlCmd = "SELECT type from $itemTable WHERE type != '' AND type IS NOT null GROUP BY type";
$gType = queryDb($sqlCmd, $dbConn);
$sqlCmd = "SELECT platform from $itemTable WHERE platform != '' AND platform IS NOT null GROUP BY platform";
$gPlat = queryDb($sqlCmd, $dbConn);
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
		<?php 
		if( isset($_POST['selType']) && $_POST['selType'] == "全部")
			echo " selected";
		
		echo ">全部</option>";
		
		foreach($gType as $theType) {
			$t = $theType['type'];
			echo "<option value=\"$t\" ";
			
			if( isset($_POST['selType']) && $_POST['selType'] == $t)
				echo "selected";
			
			echo ">$t</option>";
		}
		?>
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
		<?php
		foreach($gPlat as $thePlat) {
			$p = $thePlat['platform'];
			echo "<option value=\"$p\">$p</option>";
		}
		?>
	</select><br/><br/>
	類型:
	<select id="addType" name="typeAdded" >
		<?php
		foreach($gType as $theType) {
			$t = $theType['type'];
			echo "<option value=\"$t\">$t</option>";
		}
		?>
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