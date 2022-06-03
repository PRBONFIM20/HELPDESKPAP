<?php

?>

<!DOCTYPE html>
<html>
<head>
<title>Form site</title>
</head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">

<body>

<div class="w3-container" id="contact" style="margin-top:75px" ;>  
    <h1 class="w3-xxxlarge w3-text-red"><b>Formulário de avaria</b></h1>
    <hr style="width:50px;border:5px solid red" class="w3-round">

<form   action="connect.php" method="post">
        
        Local : <input type="text"  class="w3-input w3-border" name="ticket_local"><br><br>
        Natureza da Avaria : <input type="text" class="w3-input w3-border" name="ticket_natureza" placeholder="LocaldaAvaria"><br><br>
        Descrição :<br><textarea rows="5" name="ticket_descricao" ^cols="30" class="w3-input w3-border"></textarea><br>
        
        <input type="submit">
</form>




</body>
</html>

