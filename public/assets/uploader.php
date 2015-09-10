
<?php 
require_once 'Logger.php';
function uploadFile($tempfilename, $filename, $userid)
{		
	$tempfilepath = $_SERVER['DOCUMENT_ROOT'] . "/userfiles/temp" . $userid;
	//If the temp directory doesn't exist, create it
	if (!file_exists($tempfilepath))
	{
		mkdir($tempfilepath);
	}
	$fullpath = $tempfilepath . "/" . $filename;
	return move_uploaded_file($tempfilename, $fullpath);
}
$userid = 0;
$failedfiles = array();
if (isset($_POST['user_id']))
{
	$userid = $_POST['user_id'];
}

foreach ($_FILES as $fieldName => $file)
{
	$fileuploaded = uploadFile($file['tmp_name'], $file['name'], $userid);
	if (!$fileuploaded)
	{
		array_push($failedfiles, $file["name"]);
	}
}

//If any files failed, then indicate them here.
//otherwise send a success response.
if (count($failedfiles) > 0)
{
	$response = array('FAILURE' => $failedfiles);
}
else
{
	$response = array('SUCCESS' => 'SUCCESS');
}
$encoded = json_encode($response);
header('Content-type: application/json');
echo $encoded;
?>
