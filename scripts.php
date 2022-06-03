<?php 


if (mysqli_connect_error()){
                echo "alert('Error')";
                die('Connect Error ('. mysqli_connect_errno() .') '
                . mysqli_connect_error());
            }else{
                $sql =
select count (*) from users ; 


?>