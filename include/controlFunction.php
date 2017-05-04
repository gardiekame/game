<?php

function getRetrievalSql($range, $pass, $tables) {
	$select1 = "SELECT g_id, title, $tables[1].type_name, 
		$tables[2].name AS platName, release_date, img_url, content FROM $tables[0] ";
	$join1 = "JOIN $tables[1] ON $tables[0].type = $tables[1].type_id ";
	$join2 = "JOIN $tables[2] ON $tables[0].platform = $tables[2].platform_id ";
	$order = "ORDER BY release_date DESC ";

	$retrievalSql = $select1 . $join1 . $join2;
	$retrievalCountsSql = "SELECT count(*) FROM $tables[0] " . $join1 . $join2;
	
	switch($range) {
		case "usual":
			break;
		case "searchType&Plat":
			$condition = "WHERE type_name = '" . $pass['selType'] . "' AND $tables[2].name = '".
				$pass['selPlat'] . "' ";//AND valid='Y'
			$retrievalCountsSql .= $condition;
			$retrievalSql .= $condition;
			break;
		case "searchType":
			$condition = "WHERE type_name = '" . $pass['selType'] . "' ";
			$retrievalCountsSql .= $condition;
			$retrievalSql .= $condition;
			break;
		case "searchPlat":
			$condition = "WHERE $tables[2].name = '" . $pass['selPlat'] . "' ";
			$retrievalCountsSql .= $condition;
			$retrievalSql .= $condition;
			break;
		case "searchTitle":
			$condition = "WHERE title like '%" . $pass['searchTitle'] . "%' ";
			$retrievalCountsSql .= $condition;
			$retrievalSql .= $condition;
			break;
	}

	
	
	if(isset($_GET['plat'])) {
		
		if($query != "usual") {
			$retrievalCountsSql .= "AND ";
			$retrievalSql .= "AND ";
		}
		else {
			$retrievalCountsSql .= "WHERE ";
			$retrievalSql .= "WHERE ";
		}
		
		$platType = $_GET['plat'];
		$condition = "$tables[2].type = (SELECT id FROM $tables[3] WHERE name2 = '$platType') ";
		$retrievalCountsSql .= $condition;
		$retrievalSql .= $condition;
	}
	$retrievalSql .= $order;
	return array($retrievalCountsSql, $retrievalSql);
}

?>
