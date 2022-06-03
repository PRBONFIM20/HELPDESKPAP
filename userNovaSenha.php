<?php 

session_start();

include_once  './conexaobasedados.php'; 


if (empty($_GET['id']) || empty($_GET['code'])) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
     
}

$codigo ="";
$senha ="";
$senhaConfirmacao ="";
$nome ="";


$mensagem ="";

$geraForm = "Nao"; 
$interfaceSenhas = "Nao";
$sucesso = "Nao";

if (isset($_GET['id']) && isset($_GET['code'])) {
    
    
    $codigo = base64_decode($_GET['id']); 
    $code = $_GET['code'];
    
    $stmt = $_conn->prepare('SELECT * FROM USERS WHERE CODIGO = ? AND TOKEN_CODE= ?');
    $stmt->bind_param('ss', $codigo, $code); 
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
             $geraForm="Sim";  
             $interfaceSenhas = "Sim";
             $nome= $rowUsers["NOME"];
        }            
            
        } else {
           
           $mensagem = "O código de utilizador ou código de recuperação já foi utilizado ou danificado. Em caso de dificuldade, pode solicitar novamente a recuperação de senha.";
        } 
    
    mysqli_stmt_close($stmt);
   
           
}



if (isset($_POST['botao-guardar-nova-senha'])) {

    $podeAlterar ="Sim"; 
    
    $senha = $senha=mysqli_real_escape_string($_conn,$_POST["senha"]);
    $senha = trim($senha);
    
    $senhaConfirmacao = $senhaConfirmacao=mysqli_real_escape_string($_conn,$_POST["senhaConfirmacao"]);
    $senhaConfirmacao = trim($senhaConfirmacao);
    
    
    if (strlen(trim($senha))<8) { 
            $mensagem="A senha tem que ter pelo menos 8 caracteres!";
         
            $podeAlterar = "Nao"; 
    }
        
    if ($senha!=$senhaConfirmacao) { 
            $mensagem="A senha de confirmação deve ser igual à primeira senha!";
          
            $podeAlterar = "Nao"; 
    }
    
    
    if ( $podeAlterar == "Sim") {
        
        ///////////////////////////////////
        // ALTERAR SENHA
        //////////
    
        $sql= "UPDATE USERS SET PASSWORD = ?, TOKEN_CODE = ''  WHERE CODIGO = ?";

        if ( $stmt = mysqli_prepare($_conn, $sql)  ) {

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            mysqli_stmt_bind_param($stmt, "ss", $senhaHash, $codigo);
            
            mysqli_stmt_execute($stmt);
            
           
            $mensagem = "A sua nova senha foi alterada com sucesso." ;
              
            $interfaceSenhas = "Nao";
            
            
            $sucesso = "Sim";
            $geraForm = "Nao";
            
      

        } else{
            
            echo "STATUS ADMIN (nova senha): " . mysqli_error($_conn);
        }

       // mysqli_stmt_close($stmt);
        
    }
    
}
    
?>
<!DOCTYPE html>
<html>

<head>
<title>Definir nova senha</title>
<meta charset="UTF-8">
</head> 

<body>
           
          <p> Alterar senha</p>
          
          <?php if ( $geraForm == "Sim") { ?>
            <form action="#" method="POST">

                <br> Alteração de senha para <?php echo $nome;?>.
                
                <?php if ( $interfaceSenhas == "Sim") { ?>
                        <br><br><?php echo $mensagem;?>
                
                        <p>Nova Senha:</p>
                        <input type="password" name="senha"  required><br>
                        <p>Confirmação da nova Senha:</p>
                        <input type="password" name="senhaConfirmacao" required>
                        <br><br> 
                        <button name="botao-guardar-nova-senha" type="submit">Confirmar nova senha</button>
                <?php } else { ?>
                    <p><?php echo $mensagem;?></p>
               <?php }?>
            </form>
          <?php } else { ?>
          
          <?php 
          $destino = "./index.php";
          if ( $sucesso == "Nao" ) { 
          		$destino = "./userRecuperarSenha.php";
          }
          
          ?>
          
          <form action="<?php echo $destino;?>" method="POST">
              
              <p><?php echo $mensagem;?><br></p> 
              
              <?php if ( $interfaceSenhas == "Nao") { 
              
              	if ( $sucesso == "Nao" ) {
              ?>
                
                <button type="submit"> Solicitar recuperação de senha</button>
              <?php } else { ?>
              
               <button type="submit"> Voltar</button>
               
              <?php  
               
              } ?>
          </form>  
          <?php 
              
              } 
           }
              
              
          ?>
        
          
       
    
</body>
</html>
