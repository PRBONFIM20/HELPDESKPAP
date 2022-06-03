<?php 

session_start();

include_once  './conexaobasedados.php'; 

if ($_SESSION["NIVEL_UTILIZADOR"]!=2 ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: ./index.php");
}


// manter o critério de pesquisa

if ( isset($_POST["filtroSQL"]))  {
    
    $filtroSQL = $_POST["filtroSQL"];
    
    if ( trim($filtroSQL)=='') {
        $filtroSQL = "SELECT * FROM USERS ORDER BY CODIGO ASC";
    }
    
}  else {
    $filtroSQL = "SELECT * FROM USERS ORDER BY CODIGO DESC";
}


if ( $_SESSION["NIVEL_UTILIZADOR"]==2) {
    
    if ( isset($_POST["botao-ordenar-users-nome-asc"])  ) {
        
        $filtroSQL = "SELECT * FROM USERS ORDER BY NOME ASC";
    }
    if ( isset($_POST["botao-ordenar-users-nome-desc"])  ) {
        
        $filtroSQL = "SELECT * FROM USERS ORDER BY NOME DESC";
    }
}



$campoPesquisa = "";
if ( isset($_POST['botao-pesquisar-lista-utilizadores'])) {
    
    $campoPesquisa = trim(mysqli_real_escape_string($_conn,$_POST['campoPesquisa']));
    
    if ( trim($campoPesquisa)!="") {
        
        $filtroSQL = "SELECT * FROM USERS  WHERE (CODIGO LIKE '%$campoPesquisa%') OR (NOME LIKE '%$campoPesquisa%') OR (EMAIL LIKE '%$campoPesquisa%') OR (DATA_HORA LIKE '%$campoPesquisa%') ORDER BY CODIGO;";
    }
    
}



if ( isset($_POST["botao-ativar-utilizador"])  ) {
    
    // fazer update à tabela de USERS para atualizar o estado e limpar o token
    $sql= "UPDATE  USERS SET USER_STATUS=1, TOKEN_CODE=? WHERE CODIGO=?";
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        
        $codeAtivar="";
        $codigoAtivar = $_POST["codigoAtivar"];
        
        mysqli_stmt_bind_param($stmt, "ss", $codeAtivar,$codigoAtivar);
        mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        
        
    } else {
        mysqli_stmt_close($stmt);
        // falhou a atualização
       
        echo "STATUS ADMIN (ativar utilizador manualmente): " . mysqli_error($_conn);
    }
    
    
    
    
}


if ( isset($_POST["botao-bloquear-utilizador"])  ) {
    
    // fazer update à tabela de USERS para atualizar o estado e limpar o token
    $sql= "UPDATE  USERS SET USER_STATUS=2, TOKEN_CODE=? WHERE CODIGO=?";
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        
        $codeAtivar="";
        $codigoAtivar = $_POST["codigoAtivar"];
        
        mysqli_stmt_bind_param($stmt, "ss", $codeAtivar,$codigoAtivar);
        mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        
        
    } else {
        mysqli_stmt_close($stmt);
        // falhou a atualização
        
        echo "STATUS ADMIN (ativar utilizador manualmente): " . mysqli_error($_conn);
    }
    
    
    
    
}

if ( isset($_POST["botao-desbloquear-utilizador"])  ) {
    
    // fazer update à tabela de USERS para atualizar o estado e limpar o token
    $sql= "UPDATE  USERS SET USER_STATUS=1, TOKEN_CODE=? WHERE CODIGO=?";
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        
        $codeAtivar="";
        $codigoAtivar = $_POST["codigoAtivar"];
        
        mysqli_stmt_bind_param($stmt, "ss", $codeAtivar,$codigoAtivar);
        mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        
        
    } else {
        mysqli_stmt_close($stmt);
        // falhou a atualização
        
        echo "STATUS ADMIN (ativar utilizador manualmente): " . mysqli_error($_conn);
    }
    
    
    
    
}


if(isset($_POST["botao-exportar-contactos"])){
    
        
        $delimiter = ";";
        $filename = "Utilizadores_registados" . "_" . date('Y-m-d') . ".csv";
        
        //create a file pointer
        $f = fopen('php://memory', 'w');
        
        
        //
        //set column headers
        $fields = array('Nome', 'Email', 'Hora de registo');
        fputcsv($f, $fields, $delimiter);
        
        $query = $filtroSQL;
        
        $result = mysqli_query($_conn, $query);
        while($row = mysqli_fetch_assoc($result))
        {
            
            $nomeCSV = $row["NOME"];
            $nomeCSV= iconv("UTF-8","ISO-8859-1",$nomeCSV);
            
           
            $emailCSV = $row["EMAIL"];
            
            
            $lineData = array($nomeCSV, $emailCSV, $row['DATA_HORA']);
            
            
            fputcsv($f, $lineData, $delimiter);
        }
        
        
        //move back to beginning of file
        fseek($f, 0);
        
        //set headers to download file rather than displayed
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        
        //output all remaining data on a file pointer
        fpassthru($f);
        
        fclose($f);
        exit;
  
}



    
// saber total de utilizadores
$resultadoTotal = mysqli_query($_conn, "SELECT COUNT(CODIGO) AS TOTAL FROM USERS");           

$UTILIZADORES_TOTAL = 0;
if (mysqli_num_rows($resultadoTotal) > 0) {
     while($rowTotal = mysqli_fetch_assoc($resultadoTotal)) {
          $UTILIZADORES_TOTAL = $rowTotal["TOTAL"];
     }
}
mysqli_free_result($resultadoTotal);
    

?>
<!DOCTYPE html>
<html>
<head>
<title>Gerir utilizadores</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="styles.css">

</head>

<body>


<i  id="ancoraTopo"><br><br></i>     

<div class="w3-container" id="packages" style="margin-top:75px">
    <h1 class="w3-xxxlarge w3-text-red"><b>Gerir utilizadores</b></h1>
    <hr style="width:50px;border:5px solid red" class="w3-round">

    <div class="w3-row-padding">
    <div class="w3-half w3-margin-bottom">
      <ul class="w3-ul w3-light-grey w3-center">

<form  action="./userGerirUtilizadores.php#ancoraTopo" method="POST">
      <B><?php echo $UTILIZADORES_TOTAL;?> utilizador(es) na base de dados.</B>
 
      <button class="w3-button w3-red w3-padding-large w3-hover-black" type="submit" name="botao-refresh-users-asc"> Atualizar</button>
      <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">          
</form>

<br>

  <form action="./userGerirUtilizadores.php#ancoraTopo" method="POST">
                 
                 Pesquisar código, nome, e-mail ou data/hora:&nbsp;   
                <input type="text" name="campoPesquisa" value="<?php echo $campoPesquisa;?>">
               
                <button class="w3-button w3-red w3-padding-large w3-hover-black" name="botao-pesquisar-lista-utilizadores" type="submit">Pesquisar</button>
  </form>   
        


<br>

  <form action="./userGerirUtilizadores.php#ancoraTopo" method="POST">
                    
                <button class="w3-button w3-red w3-padding-large w3-hover-black" name="botao-exportar-contactos" type="submit">Exportar contactos (CSV)</button>
                <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>"> 
  </form>   





<i  id="ancoraTopo"><br><br></i>     
 
     <p>
     
     <table border = 1>
      <tr>
        <th>Código</th>
        <th>Situação</th>
        <th>Nome<br>
        
                  <form  action="./userGerirUtilizadores.php#ancoraTopo" method="POST">
                       <i class="material-icons" style="font-size:24px;vertical-align:middle;">sort_by_alpha</i><BR>
                        <button type="submit" name="botao-ordenar-users-nome-asc" class="w3-button"><i class="material-icons" style="font-size:24px;vertical-align:middle;">arrow_drop_up</i></button>
                        <button type="submit" name="botao-ordenar-users-nome-desc" class="w3-button"><i class="material-icons" style="font-size:24px;vertical-align:middle;">arrow_drop_down</i></button>
                        
                  </form>
        
        
        </th>
        <th>Email</th>
        <th>Data de registo</th>
      </tr>
     
     
     <?php 
       
        $resultadoTabela = mysqli_query($_conn, $filtroSQL);           

        if (mysqli_num_rows($resultadoTabela) > 0) {
            $ctd = 0;
            while($rowTabela = mysqli_fetch_assoc($resultadoTabela)) {
                $ctd=$ctd+1;
            ?>   

			<tr>
   			 
            <td id ="ancoraUtilizador<?php echo $ctd;?>"><b><?php echo $rowTabela["CODIGO"]?></b></td>

            <td>
                
                <?php  if ($rowTabela["USER_STATUS"]==0) { ?><i class="material-icons w3-text-red" style="font-size:24px;vertical-align:middle;">person</i><?php } ?>
                <?php  if ($rowTabela["USER_STATUS"]==1) { ?><i class="material-icons w3-text-green" style="font-size:24px;vertical-align:middle;">how_to_reg</i><?php } ?>
                <?php  if ($rowTabela["USER_STATUS"]==2) { ?><i class="material-icons w3-text-red" style="font-size:24px;vertical-align:middle;">voice_over_off</i><?php } ?>
                  
                <?php  if ($rowTabela["MENSAGENS_MARKETING"]==0) { ?><i class="material-icons w3-text-red" style="font-size:24px;vertical-align:middle;">notifications_off</i><?php } ?>
                
                
                 <?php  if ($rowTabela["USER_STATUS"]==0) { ?>
                            <form  action="./userGerirUtilizadores.php#ancoraUtilizador<?php echo ($ctd);?>" method="POST">
                                <i class="material-icons w3-text-green" style="font-size:24px;vertical-align:middle;" >subdirectory_arrow_right</i>
                                   <button type="submit" name="botao-ativar-utilizador" class="w3-button w3-text-green"><i class="material-icons" style="font-size:24px;vertical-align:middle;">how_to_reg</i></button>
                                   <input id="codigoAtivar" name="codigoAtivar" type="hidden" value="<?php echo $rowTabela["CODIGO"]; ?>">
                                 
                                   <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">  
                            </form> 
                    <?php } ?>
                    
                     <?php  if ($rowTabela["USER_STATUS"]==1) { ?>
                            <form  action="./userGerirUtilizadores.php#ancoraUtilizador<?php echo ($ctd);?>" method="POST">
                                <i class="material-icons w3-text-grey" style="font-size:24px;vertical-align:middle;" >subdirectory_arrow_right</i>
                                   <button type="submit" name="botao-bloquear-utilizador" class="w3-button w3-text-grey"><i class="material-icons" style="font-size:24px;vertical-align:middle;">voice_over_off</i></button>
                                   <input id="codigoAtivar" name="codigoAtivar" type="hidden" value="<?php echo $rowTabela["CODIGO"]; ?>">
                                 
                                   <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">  
                            </form> 
                    <?php } ?>
                    
                     <?php  if ($rowTabela["USER_STATUS"]==2) { ?>
                            <form  action="./userGerirUtilizadores.php#ancoraUtilizador<?php echo ($ctd);?>" method="POST">
                                <i class="material-icons w3-text-grey" style="font-size:24px;vertical-align:middle;" >subdirectory_arrow_right</i>
                                   <button type="submit" name="botao-desbloquear-utilizador" class="w3-button w3-text-grey"><i class="material-icons" style="font-size:24px;vertical-align:middle;">how_to_reg</i></button>
                                   <input id="codigoAtivar" name="codigoAtivar" type="hidden" value="<?php echo $rowTabela["CODIGO"]; ?>">
                                 
                                   <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">  
                            </form> 
                    <?php } ?>
                    
                    
                                    
            </td>
		    
			<td><b> <?php echo $rowTabela["NOME"]?></b><?php  if ($rowTabela["NIVEL"]==2) { echo "<br>(Administrador)";}?></td>                  
			<td><?php echo $rowTabela["EMAIL"]?></td>                  
			<td>[<?php echo $rowTabela["DATA_HORA"]?>]</td>        

            
			</tr>

 	<?php
            }
        }
        mysqli_free_result($resultadoTabela);
       
    ?>
    </table>
   
    <br>
    <a class="w3-button w3-red w3-padding-large w3-hover-black"  href="./index.php">Voltar</a>

</body>
</html>
