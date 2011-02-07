<?php
$test = "EmployeeID_69_ComputerID_55";
preg_match('/^([a-zA-Z0-9]+)_(\d+).*/', $test, $groups);
echo $groups[1] ;
#echo $groups[2] ;
?>

