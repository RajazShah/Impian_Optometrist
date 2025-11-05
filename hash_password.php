<?php
// Password for liana@gmail.com
$passwordLiana = 'liana123';
$hashLiana = password_hash($passwordLiana, PASSWORD_DEFAULT);
echo "Hash for liana123: <br>" . $hashLiana . "<br><br>"; // Added line breaks

// Password for izzat@gmail.com
$passwordIzzat = 'izzat123';
$hashIzzat = password_hash($passwordIzzat, PASSWORD_DEFAULT);
echo "Hash for izzat123: <br>" . $hashIzzat . "<br>";
?>