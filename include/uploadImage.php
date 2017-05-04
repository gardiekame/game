<?php
function uploadCover($operation) {
	$uploadDir = "cover/";
	$imageFileType = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
	$fileType = strtolower($imageFileType);
	$uploadFile = $uploadDir . (string)($GLOBALS['imageAdded']) . "." . $imageFileType;
	
	$GLOBALS['imageAdded'] = $uploadFile;

	//$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	
	//if($check !== false) {
		
		if($_FILES["fileToUpload"]["size"] < 2000000) {
			
			if($fileType == "jpg" || $fileType == "png" || $fileType == "jpeg"
				|| $fileType == "gif" ) {
				
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uploadFile)) {
					echo "檔案 ". basename( $_FILES["fileToUpload"]["name"]). "上傳成功 ";
				} 
				else {
					echo "上傳圖片失敗 ";
					$GLOBALS['imageAdded'] = "";
				}

			}
			else {
				echo "檔案僅支援'jpg', 'png', 'jpeg'和'gif' ";
				$GLOBALS['imageAdded'] = "";
			}
			
		}
		else {
			echo "檔案太大 ";
			$GLOBALS['imageAdded'] = "";
		}
	
	//}
	//else {
	//	echo "檔案不是圖片檔";
	//}
	
}
?>