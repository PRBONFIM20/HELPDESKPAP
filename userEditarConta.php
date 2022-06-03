<?php 

session_start();

include_once  './conexaobasedados.php';
include_once './navbar.php'; 


if ( isset($_POST['botao-cancelar-alteracoes']) ) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
}


$msgTemporaria = "";

$mensagemErroNome = "";
$mensagemErroSenha = "";


if ( !isset($_SESSION["UTILIZADOR"])) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
} else {
    // ler informações de conta 
    
    $codigo = $_SESSION["UTILIZADOR"]; 

    $stmt = $_conn->prepare('SELECT * FROM USERS WHERE CODIGO = ?');
    $stmt->bind_param('s', $codigo); 
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
         
            
            $senha ="";
            $senhaEncriptada =$rowUsers['PASSWORD'];
            
            
            if ( !isset($_POST["nome"])) {
                
                $nome =$rowUsers['NOME'];
                $receberMsgs = $rowUsers['MENSAGENS_MARKETING'];
                
                   
            } else {
                
                $podeRegistar = "Sim"; 
                
                ///////// em modo de alteração - filtrar e validar campos
                
                $nome= mysqli_real_escape_string($_conn, $_POST['nome']);
                $nome = trim($nome); 

             
                $receberMensagens=$_POST['receberMensagens'];
                if ( $receberMensagens == "Sim") { 
                    $receberMsgs = 1;
                } else {
                    $receberMsgs = 0;
                }

                if (strlen(trim($nome))<2) {
                     $mensagemErroNome="O nome é demasiado curto!";
                     $podeRegistar = "Nao"; 
                }
              
              ///////////////////////////////  
            }
            
            
            
            
        }
    } else {
        echo "STATUS ADMIN (Editar conta): " . mysqli_error($_conn);
    }           
                    
    
     mysqli_stmt_close($stmt);
    
}









if ( isset($_POST['botao-gravar-alteracoes']) ) {
    
        
        // verificar senha por questões de segurança
      
        $senha=mysqli_real_escape_string($_conn, $_POST['senha']);
        $senha = trim($senha);
        
        
        if ( password_verify($senha, $senhaEncriptada)) {
            
            // senha OK, filtar e validar inputs
            
           
            
        } else {
            
            $mensagemErroSenha = "Senha incorreta!";
            $podeRegistar = "Nao"; 
        }
        
        
        if ( $podeRegistar == "Sim" )  {
            
           
                ///////////////////////////////////
                // ALTERA
                //////////////////////////////////
                
            
                $nome = strip_tags($nome); // demonstração da remoção de caracteres especiais html por exemplo..
            
                $sql= "UPDATE USERS SET NOME = ?, MENSAGENS_MARKETING = ?  WHERE CODIGO = ?";
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                
                    mysqli_stmt_bind_param($stmt, "sis", $nome,$receberMsgs,$codigo);


                    mysqli_stmt_execute($stmt);
                    
                    $msgTemporaria = "Definições de conta alteradas com sucesso.";
                    
                    // atualizar variável de sessão, a questão de receber mensagens de marketing não
                    // é uma variável de sessão, não é necessário guardar em sessão.
                    
                    $_SESSION["NOME_UTILIZADOR"] = $nome;
                    
                    
                    // encaminhar com timer 3 segundos
                    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                    header("Refresh: 3; URL=index.php");
                    
                    
                    

                    
                } else{
                    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($_conn);
                    echo "STATUS ADMIN (alterar definições): " . mysqli_error($_conn);
                }
               
                mysqli_stmt_close($stmt);
                
        }
            
            
}


 
?>
<!DOCTYPE html>
<html>
<head>
<title>Editar conta</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="styles.css">

</head>

<body>



      
    <form  action="#" method="POST">
       
      <br> <p> Por uma questão de segurança, para alterar as suas definições de conta deverá digitar a sua senha. No final, não se esqueça de gravar as alterações. Se apenas pretende alterar a sua senha use a opção</p> <a href="./userRecuperarSenha.php">recuperar senha</A>.
        <p><?php echo $msgTemporaria;?></p>
        <p>Nome:</p><p><?php echo $mensagemErroNome;?></p>
        <input  type="text"  name="nome" class="w3-input w3-border" value="<?php echo $nome;?>" required>
      
        <p>Senha:</p><p><?php echo $mensagemErroSenha;?></p>
        <input type="password" name="senha" class="w3-input w3-border" value="<?php echo $senha;?>" required><br>
        

        <p>Pretendo receber mensagens de marketing:</p>
        
         <select name="receberMensagens">
                <option value="Sim" <?php if ($receberMsgs == 1 ) { echo " selected"; } ?>>Sim</option>
                <option value="Não" <?php if ($receberMsgs == 0 ) { echo " selected"; } ?>>Não</option>
        </select>
        
       <br> <br>
        <button  name="botao-gravar-alteracoes" class="w3-button w3-dark-grey" type="submit"> Gravar </button>
       
    </form>   

    <br>
    
    <form action="#" method="POST">
        
         <button name="botao-cancelar-alteracoes" class="w3-button w3-dark-grey"  type="submit"> Cancelar alterações</button>  
    </form>
    <br>


    <form action="./userCancelarConta.php" method="POST">
        
         <button  name="botao-cancelar-minha-conta" class="w3-button w3-dark-grey" type="submit" > Pretendo cancelar a minha conta</button>
       
    </form>
    <br>


</body>
</html>
