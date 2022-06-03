<?php 

session_start();

include_once  './conexaobasedados.php'; 


include_once './function_mail_utf8.php'; 


$msgTemporaria = "";

$mensagemErroMotivo = "";
$mensagemErroSenha = "";
$nome = "";
$email = "";


if ( !isset($_SESSION["UTILIZADOR"])) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
} else {
    // ler definições de conta 
    
    $codigo = $_SESSION["UTILIZADOR"]; 

    $stmt = $_conn->prepare('SELECT * FROM USERS WHERE CODIGO = ?');
    $stmt->bind_param('s', $codigo); 
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
         
            
            $senha ="";
            $senhaEncriptada =$rowUsers['PASSWORD'];
            
            $nome = $rowUsers['NOME'];
            
            if ( !isset($_POST["motivo"])) {
                
                // ok
                
                   
            } else {
                
                $podeApagar = "Sim"; 
                
                ///////// em modo de eliminação - filtrar e validar campos
                
                $motivo= mysqli_real_escape_string($_conn, $_POST['motivo']);
                $motivo = trim($motivo); 


                if (strlen(trim($motivo))<10) {
                     $mensagemErroMotivo="Utilize pelo menos 10 caracteres para nos indicar o seu motivo.";
                     $podeApagar = "Nao"; 
                }
                ///////////////////////////////  
            }
            
            
            
            
        }
    } else {
        echo "STATUS ADMIN (cancelar conta): " . mysqli_error($_conn);
    }           
                    
    
     mysqli_stmt_close($stmt);
    
}









if ( isset($_POST['botao-apagar-conta']) ) {
    
       
        
        
        // verificar senha por questões de segurança
      
        $senha=mysqli_real_escape_string($_conn, $_POST['senha']);
        $senha = trim($senha);
        
        
        if ( password_verify($senha, $senhaEncriptada)) {
            
            // senha OK, filtar e validar inputs
            
           
            
        } else {
            
            $mensagemErroSenha = "Senha incorreta!";
            $podeApagar = "Nao"; 
        }
        
        
        if ( $podeApagar == "Sim" )  {
            
           
                ///////////////////////////////////
                // APAGAR
                //////////////////////////////////
            
                // Tabela USERS
            
                $sql= "DELETE FROM USERS WHERE CODIGO = ?";
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                
                    mysqli_stmt_bind_param($stmt, "s", $codigo);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                } else{
                
                    echo "STATUS ADMIN (cancelar conta - utilizador): " . mysqli_error($_conn);
                }
               
                
                //////////////////////////// 
                // ENVIAR MOTIVO POR MAIL ao utilizador
                ////////////////////////////

                $urlPagina = "http://localhost:8888/";
                
                $email =  $_SESSION["EMAIL_UTILIZADOR"];
                
                $mensagem = "$nome cancelou a sua conta" . "." . "\r\n" .  "\r\n" .

                    "E-mail: $email". "\r\n" .  "\r\n" .

                    "Registámos o motivo: $motivo" ."\r\n" ."\r\n" .

                    "Esta mensagem foi-lhe enviada automaticamente.";  

                $subject = "$nome cancelou conta em $urlPagina";

                // use wordwrap() if lines are longer than 70 characters
                $mensagem = wordwrap($mensagem,70);

                // send email
                mail_utf8($email,$subject,$mensagem); 
                  
                
                
                ////////////////////////////
               
                $msgTemporaria = "A sua conta foi cancelada. Todos os seus dados foram removidos da nossa base de dados.";
                    
                // limpar variáveis de sessão
                session_destroy();

                // encaminhar com timer 3 segundos
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                header("Refresh: 10; URL=index.php");
             
                
        }
            
            
}


 
?>
<!DOCTYPE html>
<html>
<head>
<title>Cancelar conta</title>
<meta charset="UTF-8">
</head>
<body>
         
    <form action="#" method="POST">
        <p ><?php echo $msgTemporaria;?></p>
        <p> 
            Esta opção permite cancelar a sua conta. 
            Os seus dados pessoais serão removidos definitivamente da nossa base de dados. 
            De acordo com os nossos princípios éticos de responsabilidade e transparência descritos na nossa política de privacidade,
            esta remoção inclui também dados referentes a atividades em que tenha participado nesta página. 
            Esta operação é irreversível.<br><br>Por favor, indique-nos apenas o motivo pelo qual pretende cancelar a sua conta.
            Será també enviado em email para <?php echo $_SESSION["EMAIL_UTILIZADOR"];?>
            </p>
       
        <p>Motivo:</p><p><?php echo $mensagemErroMotivo;?></p>
        <input type="text" name="motivo" value="<?php echo $motivo;?>"  required>
        <p>Utilizador <?php echo $codigo;?>, serão removidos todos os seus dados, por favor digite a sua senha para cancelar a conta.</p><p></p>
        <p>Senha:</p><p><?php echo $mensagemErroSenha;?></p>
        <input type="password" name="senha" value="<?php echo $senha;?>" required><br>
       
       <br> <br>
        <button name="botao-apagar-conta" type="submit"> Cancelar conta imediatamente</button>
       
    </form>   

    <br>
    

    <form action="./index.php" method="POST">
        
         <button name="botao-desistir-apagar" type="submit"> Não pretendo cancelar a minha conta</button>
       
    </form>
    <br>
   

</body>
</html>
