<?php

include "dbcon.php";


if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {

    header("Location: index.php");
    exit;
}


$id = (int) $_GET["id"];


$stmt = $conn->prepare(
    "DELETE FROM users WHERE id = ?"
);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: index.php?msg=deleted");

    exit;
}

$stmt->close();

header("Location: index.php");

exit;

?>