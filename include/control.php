<?php

$dbConn = connectDb($host, $user, $pwd, $dbName);

if(isset($_POST['searchTitle']) && $_POST['searchTitle'] !="") {
	$searchTitle = htmlspecialchars($_POST['searchTitle']);
	$toPass['searchTitle'] = $searchTitle;
}

isset($_POST['selType']) ? $selType = $_POST['selType'] : $selType = "全部";
isset($_POST['selPlat']) ? $selPlat = $_POST['selPlat'] : $selPlat = "全部";

$tables = array($itemTable, $typeTable, $platformTable, $platformTable2);
$toPass['selType'] = $selType;
$toPass['selPlat'] = $selPlat;

if($selType != "全部" && $selPlat != "全部") {		
	$sqlcmd = getRetrievalSql("searchType&Plat", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);	
}
else if($selType != "全部" && $selPlat == "全部"){
	$sqlcmd = getRetrievalSql("searchType", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);
}
else if($selType == "全部" && $selPlat != "全部") {
	$sqlcmd = getRetrievalSql("searchPlat", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);
}
else if(isset($searchTitle)) {
	$sqlcmd = getRetrievalSql("searchTitle", $toPass, $tables);
	$gameCounts = counts($sqlcmd[0], $dbConn);
}
else {
	$sqlcmd = getRetrievalSql("usual", "1", $tables);
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
$sqlCmd = "SELECT * from $platformTable2";
$platType = queryDb($sqlCmd, $dbConn);

if(isset($_GET['plat'])){
	$platTypeChosed = $_GET['plat'];
}
?>
