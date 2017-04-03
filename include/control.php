<?php

function getSqlCmd($query, $pass, $tables)
{
	$select1 = "SELECT g_id, title, game_type.type_name, 
		game_platform.name AS platName, release_date, img_url, content FROM $tables[0] ";
	$joinSql1 = "JOIN $tables[1] ON game.type = game_type.type_id ";
	$joinSql2 = "JOIN $tables[2] ON game.platform = game_platform.platform_id ";
	$mainSql = $select1 . $joinSql1 . $joinSql2;
	$getCountsSql = "SELECT count(*) FROM $tables[0] " . $joinSql1 . $joinSql2;
	$orderSql = "ORDER BY release_date DESC ";

	switch($query) {
		case "usual":
			$sql1 = $getCountsSql;
			$sql2 = $mainSql . $orderSql;
			break;
		case "searchType&Plat":
			$condition = "WHERE type_name = '" . $pass['selType'] . "' AND game_platform.name = '"
				. $pass['selPlat'] . "' ";//AND valid='Y'
			$sql1 = $getCountsSql . $condition;
			$sql2 = $mainSql . $condition . $orderSql;
			break;
		case "searchType":
			$condition = "WHERE type_name = '" . $pass['selType'] . "' ";
			$sql1 = $getCountsSql . $condition;
			$sql2 = $mainSql . $condition . $orderSql;
			break;
		case "searchPlat":
			$condition = "WHERE game_platform.name = '" . $pass['selPlat'] . "' ";
			$sql1 = $getCountsSql . $condition;
			$sql2 = $mainSql . $condition . $orderSql;
			break;
		case "searchTitle":
			$condition = "WHERE title like '%" . $pass['searchTitle'] . "%' ";
			$sql1 = $getCountsSql . $condition;
			$sql2 = $mainSql . $condition . $orderSql;
			break;
	}

	return array($sql1, $sql2);
}

$dbConn = connectDb($host, $user, $pwd, $dbName);

if(isset($_POST['searchTitle']) && $_POST['searchTitle'] !="") {
	$searchTitle = htmlspecialchars($_POST['searchTitle']);
	$toPass['searchTitle'] = $searchTitle;
}

if(isset($_POST['selType']))
	$selType = $_POST['selType'];
else
	$selType = "全部";
if(isset($_POST['selPlat']))
	$selPlat = $_POST['selPlat'];
else
	$selPlat = "全部";

$tables = array($itemTable, $typeTable, $platformTable);
$toPass['selType'] = $selType;
$toPass['selPlat'] = $selPlat;

if($selType != "全部" && $selPlat != "全部") {		
	$sqlcmd = getSqlCmd("searchType&Plat", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);	
}
else if($selType != "全部" && $selPlat == "全部"){
	$sqlcmd = getSqlCmd("searchType", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);
}
else if($selType == "全部" && $selPlat != "全部") {
	$sqlcmd = getSqlCmd("searchPlat", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);
}
else if(isset($searchTitle)) {
	$sqlcmd = getSqlCmd("searchTitle", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);
}
else {
	$sqlcmd = getSqlCmd("usual", "1", $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);
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
$sqlCmd = $sqlcmd[1] ."LIMIT $start, $itemPerPage";

##---handle operation of adding/editing items----------------
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
##---------------------------------------------------------------

$game = queryDb($sqlCmd, $dbConn);

$sqlCmd = "SELECT * from $typeTable";
$gType = queryDb($sqlCmd, $dbConn);
$sqlCmd = "SELECT * from $platformTable ORDER BY name";
$gPlat = queryDb($sqlCmd, $dbConn);

?>
