<?php 

error_reporting(E_ERROR | E_PARSE); 
session_start();



session_start();

include_once './navbar.php';
include_once  './conexaobasedados.php'; 
include_once './function_mail_utf8.php'; 

$mensagemErroCodigo = "";
$mensagemErroSenha = "";

$geraFormulario = "Sim";


if ( isset($_POST['botao-recuperar-senha']) ) {
    
    $codigo = mysqli_real_escape_string($_conn, $_POST['formCodigo']);
    $codigo = strtolower(trim($codigo));
 
    
        $stmt = $_conn->prepare('SELECT * FROM USERS WHERE CODIGO = ?');
        $stmt->bind_param('s', $codigo); 
        $stmt->execute();

        $resultadoUsers = $stmt->get_result();

        if ($resultadoUsers->num_rows > 0) {
            while ($rowUsers = $resultadoUsers->fetch_assoc()) { 
                
                $nome = $rowUsers['NOME'];

                if ($rowUsers['USER_STATUS']==2) { // utilizador bloqueado

                        $mensagemErroSenha="Não foi enviada mensagem de recuperação de senha, contacte os nossos serviços para obter ajuda.";

                        } else  if ($rowUsers['USER_STATUS']==0 ) { // Utilizador criou a conta mas não ativou

                                     $mensagemErroSenha=  $rowUsers['NOME'] . ", ainda não ativou a sua conta. A mensagem com o código inicial de ativação de conta foi enviada para a sua caixa de correio. Caso não a encontre na sua caixa de entrada, verifique também o seu correio não solicitado ou envie-nos um email para ativarmos a sua conta. Obrigado."; 

                                } else   {

                // Recuperar a senha 
                // gerar token, preparar e enviar mail de recuperação
                
                $code = md5(uniqid(rand()));
                

                $sql= "UPDATE  USERS SET TOKEN_CODE=? WHERE CODIGO=?";
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                                   
                    mysqli_stmt_bind_param($stmt, "ss", $code,$codigo);
                    mysqli_stmt_execute($stmt);
                    
                    // /////////////////////////////////////////////////////////////////////////////////////////////////
                    // Update efetuado com sucesso, preparar e enviar mensagem /////////////////////////////////////////
                    $id = base64_encode($codigo);
                    
                    $urlPagina = "http://localhost:8888/";
                  
                    
                    $mensagem = "Caro(a) $nome" . "," . "\r\n" .  "\r\n" .

                            
                        "Foi-nos pedido para recuperar a sua senha. Se nos pediu isto basta seguir as instruções seguintes, caso contrário, ignore esta mensagem.". "\r\n" .  "\r\n" .

                        "Para recuperar agora a sua senha basta carregar na seguinte ligação:" ."\r\n" ."\r\n" .

                        $urlPagina . "userNovaSenha.php?id=$id&code=$code" ."\r\n" ."\r\n" . 

                        "Esta mensagem foi-lhe enviada automaticamente.";  

                    $subject = "Recuperação da sua senha em $urlPagina";

                    // use wordwrap() if lines are longer than 70 characters
                    $mensagem = wordwrap($mensagem,70);

                    // send email
                    mail_utf8($email,$subject,$mensagem); 
                    echo $mensagem; // apenas para efeitos de teste...
                           
                           
                    //$msgTemporaria = $email . " " . $subject . " " . $mensagem;
                    // mail enviado
                   
                    $mensagemEmail= " " . "$nome, verifique por favor a sua caixa de correio para recuperar de imediato a sua senha!";
                   
                    
                    //
                    // fim do envio de mensagem //////////////////////////////////////////////////////////////////            
                        
                    $geraFormulario = "Nao";
    
                                    
                } else {
                    // erro
                    echo "STATUS ADMIN (recuperar senha): " . mysqli_error($_conn);
                }                  
                                    
                    
                } 



            }
        } else {
            $mensagemErroCodigo = "O código de utilizador não existe na nossa base de dados!";
        }
    
    $stmt->free_result();
    $stmt->close();
    
}
include_once './navbar.php';


 
?>
<!DOCTYPE html>
<html>



<head>
<title>Recuperar senha</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">

</head> 

<body>

<?php 

   if ( $geraFormulario == "Sim" ) { 
   
?>
           
<form action="#" method="POST">
               
          
                <p>Introduza o código que utiliza para inciar sessão e carregue no botão para recuperar senha. Será enviada para o seu e-mail uma mensagem com um código de recuperação de senha.
                <br> Por vezes estas mensagens são consideradas correio não solicitado. Se não vir a mensagem de recuperação verifique o seu correio não solicitado (SPAM).</p>
                <br><p> </p>
                <p>Código de utilizador para recuperação de senha:</p> 
                <input class="w3-input w3-border" type="text" name="formCodigo" id="formCodigo" value="<?php echo $codigo;?>" required>
                <br><?php echo $mensagemEmail;?>
                <br><?php echo $mensagemErroCodigo;?>
                <br><?php echo $mensagemErroSenha;?>
                <br>
                
                <button name="botao-recuperar-senha" type="submit" class="w3-button w3-block w3-padding-large w3-red w3-margin-bottom"> Recuperar senha agora</button>

            </form>         

<?php 
   } else { 
?>
   <h2>Recuperação de senha</h2>
   <p>
   <br>Link de recuperação enviado. Verifique a sua caixa de correio.
   
   </p>
   
   <?php 
   
   // encaminhar para página principal
   header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
   header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
   
   // Comentado para efeitos de teste (copy/paste do link de recuperação)
   // header("Refresh: 5; URL=index.php"); // encaminhar 5 segundos depois
           
   ?>

<?php
   }   		
?>


            
</body>
</html>
