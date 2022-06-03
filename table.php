
<?php

error_reporting(E_ERROR | E_PARSE);
session_start(); 

include_once  './navbar.php';


    $conn = mysqli_connect("localhost", "root", "", "helpdesk");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM ticket";
    $result = $conn->query($sql);
    $conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="styles.css">


<title>Table with database</title>

</head>
<body>

<div class="w3-container" id="packages" style="margin-top:75px " >
    <h1 class="w3-xxxlarge w3-text-purple"><b>Dashboard</b></h1>
    <hr style="width:50px;border:5px solid purple" class="w3-round">

    <div class="w3-row-padding">
    <div class="w3 w3-margin-bottom">
      <ul class="w3-ul w3-light-grey w3-center">

<table>
<tr>
<th>Codigo</th>
<th>Email</th>
<th>Local</th>
<th>Natureza da Avaria</th>
<th>Descrição</th>
<th>Data_Hora</th>
<th>Status</th>
</tr>
<?php 

if ($result->num_rows > 0) {
    // output data of each row
            while($row = $result->fetch_assoc()) {
            echo "<tr> <td>" . $row["ticket_codigo"] . "</td>  <td>" . $row["ticket_email"] . "</td><td>". $row["ticket_local"]. "</td> <td>". $row["ticket_nature"]. "</td> <td>". $row["ticket_descricao"]. "</td> <td>". $row["data_hora"]. "</td>  <td>" . $row["status"] . "</td></tr>";
            }
            echo "</table>";
        } else { echo "0 results"; }

?>

   <br><a href="./index.php" class="w3-button w3-block w3-padding-large w3-purple w3-margin-bottom" >Voltar</a>

</table>
</body>
</html>



