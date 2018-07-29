<?php
include("db.php");

$placementId = $_REQUEST['placementId'];

$query = 'DELETE FROM placements WHERE placementId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();

$db->close();
