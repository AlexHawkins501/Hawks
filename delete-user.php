<?php
session_start();
require_once("./Connector/DbConnectorPDO.php");
require("./helper/helperFunctions.php");
$connection = getConnection();
$userId = isset($_SESSION["userId"]) && !empty($_SESSION["userId"]) ? $_SESSION["userId"] : 0;
$userObj = $userId !== 0 && !IsVariableIsSetOrEmpty($_SESSION["user"]) ? $_SESSION["user"] : "";

if($userObj["email"] == "admin@gmail.com"){
	$selectQuery = "DELETE FROM profile WHERE id = '".$_GET["id"]."'";
	$selectQuerystmt = $connection->prepare($selectQuery);
    $selectQuerystmt->execute();
    header("Location: ./view-profiles.php");
}
?>