﻿<?php

function getSqlCmd($query, $pass, $table1, $table2) {
	$select1 = "SELECT g_id , title , game_type.type_name , platform , release_date , img_url , content ";
	$mainSql = $select1 . "FROM $table1 JOIN $table2 ON game.type = game_type.type_id ";
	$getCountsSql = "SELECT count(*) FROM $table1 ";
	$orderSql = "ORDER BY release_date DESC ";
	
	switch($query) {
		case "usual":
			$sql1 = $getCountsSql;
			$sql2 = $mainSql . $orderSql;
			break;
		case "searchType&Title":
			$condition = "WHERE type = '" . $pass['selType'] . "' AND title like '%" . $pass['searchTitle'] . "%' ";//AND valid='Y'
			$sql1 = $getCountsSql . $condition;
			$sql2 = $mainSql . $condition . $orderSql;
			break;
		case "searchType":
			$condition = "WHERE type = '" . $pass['selType'] . "' ";
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

if(isset($_POST['selType']) && $_POST['selType'] != "全部") {
	$selType = $_POST['selType'];
	$toPass['selType'] = $selType;
	
	if(isset($searchTitle)) {
		$sqlcmd = getSqlCmd("searchType&Title", $toPass, $itemTable, $typeTable);
		$gameCounts = counts($sqlcmd[0], $dbConn);
	}
	else{
		$sqlcmd = getSqlCmd("searchType", $toPass, $itemTable, $typeTable);
	    $gameCounts = counts($sqlcmd[0], $dbConn);
	}
	
}
else{
	
	if(isset($searchTitle)) {
		$sqlcmd = getSqlCmd("searchTitle", $toPass, $itemTable, $typeTable);
	    $gameCounts = counts($sqlcmd[0], $dbConn);
	}
	else {
		$sqlcmd = getSqlCmd("usual", "1", $itemTable, $typeTable);
		$gameCounts = counts($sqlcmd[0], $dbConn);
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
	
	$sql = "SELECT type_id from $typeTable WHERE type_name = '" . $_POST['typeAdded'] . "'";
	$typeAdded = queryDb($sql, $dbConn);
	$typeAdded = $typeAdded[0]['type_id'];
	
	if($_POST['theOperation'] == "addItem")
		addGameItem($_POST['titleAdded'], $typeAdded, $_POST['platAdded'], 
			$dateAdded, $imageAdded, $dbConn, $itemTable);
	else if($_POST['theOperation'] == "editItem" && $_FILES["fileToUpload"]["name"] != "")
		updateGameItem((string)$itemId, $_POST['titleAdded'], $typeAdded, 
			$_POST['platAdded'], $dateAdded, $imageAdded, $dbConn, $itemTable);
	else if($_POST['theOperation'] == "editItem" && $_FILES["fileToUpload"]["name"] == "")
		updateGameItem2((string)$itemId, $_POST['titleAdded'], $typeAdded, 
			$_POST['platAdded'], $dateAdded, $dbConn, $itemTable);
}
##---------------------------------------------------------------

$game = queryDb($sqlCmd, $dbConn);

$sqlCmd = "SELECT * from $typeTable";
$gType = queryDb($sqlCmd, $dbConn);
$sqlCmd = "SELECT platform from $itemTable WHERE platform != '' AND platform IS NOT null GROUP BY platform";
$gPlat = queryDb($sqlCmd, $dbConn);

?>
