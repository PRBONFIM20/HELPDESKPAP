
<?php  
  
error_reporting(E_ERROR | E_PARSE); 

session_start();

?>

<!DOCTYPE html>
<html>
<body>   
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
</style>
</head>
<body class="w3-light-grey">



<style>
body,h1,h2,h3,h4,h5 {font-family: "Poppins", sans-serif}
body {font-size:16px;}
.w3-half img{margin-bottom:-6px;margin-top:16px;opacity:0.8;cursor:pointer}
.w3-half img:hover{opacity:1}
</style>


 



<!-- Utilizador em sessão -->
<?php if (isset($_SESSION["UTILIZADOR"]) ) { ?>
        <?php include_once  './navbar.php'; ?>  
      
          
     
        
          
<?php } else { ?>
	<?php include_once  './userEntrar.php'; ?>
		
    
		
<?php } ?>

 

<!-- Interface para administrador -->
<?php if ($_SESSION["NIVEL_UTILIZADOR"]==2 ) { ?>
 
        <!-- Header -->
 <header class="w3-container" style="padding-top:22px">
    <h5><b><i class="fa fa-dashboard"></i> My Dashboard</b></h5>
  </header>

  <div class="w3-row-padding w3-margin-bottom">
    <div class="w3-quarter">
      <div class="w3-container w3-red w3-padding-16">
        <div class="w3-left"><i class="fa fa-comment w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3>2</h3>
        </div>
        <div class="w3-clear"></div>
        
        <div class="w3-clear"></div>
        <h4> <A href=table.php A> Todos os Tickets</h4>
      </div>
    </div>
    <div class="w3-quarter">
      <div class="w3-container w3-teal w3-padding-16">
        <div class="w3-left"><i class="fa fa-share-alt w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3>23</h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Tickets Abertoss</h4>
      </div>
</div>
<div class="w3-quarter">
      <div class="w3-container w3-blue w3-padding-16">
        <div class="w3-left"><i class="fa fa-eye w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3>10</h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Tickets Resolvidos</h4>
      </div>
    </div>
    <div class="w3-quarter">
    <div class="w3-quarter">
      <div class="w3-container w3-orange w3-text-white w3-padding-16">
        <div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3>50</h3>
        </div>
        <div class="w3-clear"></div>
        <h4> <A href="userGerirUtilizadores.php">Users</h4>
      </div>
    </div>
  </div>

  
 
 
<?php } ?>


<br><br><br>

 

<?php if (isset($_SESSION["UTILIZADOR"]) ) { ?>
          
          
        <br><br><br>
        <b>Zona de conteúdos privados!</b>  
        <A href="ticket.php">Ticket</A><br> 
<?php }  ?>


</body>
</html>