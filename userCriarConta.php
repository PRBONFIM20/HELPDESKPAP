<?php 


error_reporting(E_ERROR | E_PARSE); 
session_start();


include_once  './conexaobasedados.php'; 
include_once './function_mail_utf8.php'; 




// iniciar e limpar possíveis mensagens de erro
$msgTemporaria = "";

$mensagemErroCodigo = "";
$mensagemErroEmail = "";
$mensagemErroSenha = "";
$mensagemErroSenhaRecuperacao = "";
$mensagemErroNome = "";

// inciar e limpar variáveis
$codigo="";
$email="";
$senha="";
$senhaConfirmacao="";
$nome="";
$aceito="";
$aceitoMarketing = 0;

$geraFormulario = "Sim";


if ( isset($_POST['submit-cancelar-conta']) ) {
    
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}


if ( isset($_POST['submit-criar-conta']) ) {


        $podeCriarRegisto = "Sim";
        
         
        // obter parametros (determinadas validações poderiam ser feitas no lado cliente)
        
        
        
    
        $codigo = mysqli_real_escape_string($_conn, $_POST['formCodigo']);
        $codigo = strtolower(trim($codigo));
        $email=mysqli_real_escape_string($_conn, $_POST['formEmail']);
        $email=strtolower(trim($email));
        $senha=mysqli_real_escape_string($_conn, $_POST['formSenha1']);
        $senha = trim($senha);
        $senhaConfirmacao=mysqli_real_escape_string($_conn, $_POST['formSenha2']);
        $senhaConfirmacao = trim($senhaConfirmacao);
        $nome= mysqli_real_escape_string($_conn, $_POST['formNome']);
        $nome = trim($nome);
        $aceito = $_POST['formAceito'];
        
       
        if ( $aceito == "aceito_marketing") { 
                   $aceitoMarketing = 1;
               } else {
                   $aceitoMarketing = 0;
        }

        
        // retirar possíveis tags html do código
        $codigo = strip_tags($codigo);
        $email = strip_tags($email);
        $nome = strip_tags($nome);
        
        // não permitir que um user tenha espaços no código...
        $codigo = str_replace(' ', '', $codigo);
        
        // validar parametros recebidos

        if (strlen(trim($codigo))<4) {
            $mensagemErroCodigo="O código é demasiado curto!";
            $podeCriarRegisto = "Nao"; 
        }

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagemErroEmail="O e-mail não é válido!";
            $podeCriarRegisto = "Nao"; 
        } 


        if (strlen(trim($senha))<8) { 
            $mensagemErroSenha="A senha tem que ter pelo menos 8 caracteres!";
            $podeCriarRegisto = "Nao"; 
        }
        
        if ($senha!=$senhaConfirmacao) { 
            $mensagemErroSenhaRecuperacao="A senha de confirmação deve ser igual à primeira senha!";
            $podeCriarRegisto = "Nao"; 
        }

         
        if (strlen(trim($nome))<2) {
            $mensagemErroNome="O nome é demasiado curto!";
            $podeCriarRegisto = "Nao"; 
              
        }
        
        
        // a check box não precisa de ser validada..
        
        
        // inicio
        
        if ( $podeCriarRegisto == "Sim") { 
            
            // validações corretas: validar se existe utilizador
            $stmt = $_conn->prepare('SELECT * FROM USERS WHERE CODIGO = ?');
            $stmt->bind_param('s', $codigo); 
            $stmt->execute();

            $resultadoUsers = $stmt->get_result();
    
            if ($resultadoUsers->num_rows > 0) {
                
                $mensagemErroCodigo = "Já existe um utilizador registado com este código.";
                
                $stmt->free_result();
                $stmt->close();
            }     
            
            else {
               
               
                ///////////////////////////////////
                // INSERE UTILIZADOR NA BASE DE DADOS
                //////////
                $sql= "INSERT INTO USERS (CODIGO, EMAIL, PASSWORD, NOME, NIVEL,USER_STATUS, MENSAGENS_MARKETING,DATA_HORA) 
                                    VALUES (?,?,?,?,?,?,?,?)";
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                
                    $nivel = 1;
                    $status = 0;

                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    
                    date_default_timezone_set('Europe/Lisbon');
                    $data_hora = date("Y-m-d H:i:s", time()); 
                                
                    mysqli_stmt_bind_param($stmt, "ssssiiis", $codigo, $email,$senhaHash,$nome,$nivel,$status,$aceitoMarketing,$data_hora);


                    mysqli_stmt_execute($stmt);

                    $geraFormulario = "Nao";
            
                } else{
            
                    echo "STATUS ADMIN (inserir user): " . mysqli_error($_conn);
                }
               
                mysqli_stmt_close($stmt);
                //////////
                // INSERIDO
                ////////////////////////////////////////
                
                ////////////////////////////////////////////////////////////////////////////
                // registo efetuado, gerar token, preparar e enviar mail de ativação
                
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

                        "Obrigado por se ter registado.". "\r\n" .  "\r\n" .

                        "Para ativar a sua conta basta carregar na seguinte ligação:" ."\r\n" ."\r\n" .

                        $urlPagina . "userAtivarConta.php?id=$id&code=$code" ."\r\n" ."\r\n" . 

                        "Esta mensagem foi-lhe enviada automaticamente.";  

                    $subject = "Ativação da sua conta em $urlPagina";

                    // use wordwrap() if lines are longer than 70 characters
                    $mensagem = wordwrap($mensagem,70);

                    // send email
                    mail_utf8($email,$subject,$mensagem); 
                    echo $mensagem; // apenas para efeitos de teste...
                    // 
                    //$msgTemporaria = $email . " " . $subject . " " . $mensagem;
                    // mail enviado
                    $msgTemporaria= $msgTemporaria . " " . "$nome, verifique por favor a sua caixa de correio para ativar de imediato a sua conta! Por vezes estas mensagens são consideradas correio não solicitado. Se não vir a mensagem de ativação verifique o seu correio não solicitado (SPAM).";
            
                    //
                    // fim do envio de mensagem //////////////////////////////////////////////////////////////////
                    
                } else{
                    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($_conn);
                    echo "STATUS ADMIN (gerar token): " . mysqli_error($_conn);
                }
               
                mysqli_stmt_close($stmt);
                // mail de ativação enviado
                /////////////////////////////////////////////////////////////////////////////
                
                    
                    
                    
            } 
                
        }

        
        // fim
    
} 

?>

<!DOCTYPE html>
<html>

<head>
<title>Criar conta</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">

</head> 

<body>


<?php 
      if ($geraFormulario == "Sim") {  
?>



<div class="w3-container" id="contact" style="margin-top:75px">
    <h1 class="w3-xxxlarge w3-text-red"><b>Criar conta</b></h1>
    <hr style="width:50px;border:5px solid red" class="w3-round">

<form action="#" method="POST">

    <label  for="formCodigo">Código de utilizador:</label><br>
    <input  class="w3-input w3-border" type="text" id="formCodigo" name="formCodigo" value="<?php echo $codigo;?>">
    <?php echo $mensagemErroCodigo;?>
 <br><br>
  
    <label  for="formEmail">e-Mail:</label><br>
    <input  class="w3-input w3-border" type="text" id="formEmail" name="formEmail" value="<?php echo $email;?>">
    <?php echo $mensagemErroEmail;?>
 <br><br>
  
    <label  for="formSenha1">Senha:</label><br>
    <input  class="w3-input w3-border"  type="password" id="formSenha1" name="formSenha1" value="<?php echo $senha;?>">
    <?php echo $mensagemErroSenha;?>
 <br><br>
  
    <label for="formSenha2">Confirmação de senha:</label><br>
    <input class="w3-input w3-border" type="password" id="formSenha2" name="formSenha2" value="<?php echo $senhaConfirmacao;?>">
    <?php echo $mensagemErroSenhaRecuperacao;?>
 <br><br>

    <label for="formNome">Nome completo:</label><br>
    <input class="w3-input w3-border" type="text" id="formNome" name="formNome" value="<?php echo $nome;?>">
    <?php echo $mensagemErroNome;?>
 <br><br>

    
  <input type="checkbox" id="formAceito" name="formAceito" value="aceito_marketing" <?php if ($aceitoMarketing == 1 ) { echo " checked"; } ?>>
  <label for="formAceito"> Aceito que os meus dados sejam utilizados para efeitos de marketing</label><br><br><br>
  
  
  <input type="submit" value="Criar conta" id="submit-criar-conta" name="submit-criar-conta" class="w3-button w3-block w3-padding-large w3-red w3-margin-bottom">
  <input type="submit" value="Cancelar" id="submit-cancelar-conta" name="submit-cancelar-conta" class="w3-button w3-block w3-padding-large w3-red w3-margin-bottom">
  
</form>

   
<?php 
   } else { 
?>
   <h2>Conta criada com sucesso</h2>
   <p>
   <br>
   <?php echo $msgTemporaria;?>
   </p>
   
   <?php 
   
   // encaminhar para página principal
   header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
   header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
   
   // Comentado para efeitos de teste (copy/paste do link de ativação, link voltar em html é provisºorio)
   // header("Refresh: 5; URL=index.php"); // encaminhar 5 segundos depois
              
   ?>

     <br><a href="./index.php" class="w3-button w3-black w3-xxlarge >Voltar</a>
   

<?php
   }   		
?>

</body>
</html>


