

<?php


include_once 'conexaobasedados.php';


$sql= "SELECT  ticket  from  =$_SESSION["UTILIZADOR"] ";
$result = $conn->query($sql);

?>






<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="styles.css">


<title>Tabela</title>

</head>
<body>


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

   <br><a href="./index.php" class="w3-button w3-block w3-padding-large w3-red w3-margin-bottom" >Voltar</a>

</table>
</body>
</html>
