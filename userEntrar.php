<?php  
error_reporting(E_ERROR | E_PARSE); 
session_start();

// para ver todos os erros:
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

include_once  './conexaobasedados.php';

$mensagemErroCodigo = "";
$mensagemErroSenha = "";

if ( isset($_POST['botao-cancelar-entrada']) ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if ( isset($_POST['botao-iniciar-sessao']) ) {
    
    $codigo = strtolower(trim(mysqli_real_escape_string($_conn,$_POST["formCodigo"])));
    $codigo = trim($codigo);
    
    $senha = trim(mysqli_real_escape_string($_conn,$_POST["formSenha"]));
    $senha = trim($senha);
    
    $codigo = strip_tags($codigo);
    
    $stmt = $_conn->prepare('SELECT * FROM USERS WHERE CODIGO = ?');
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    
    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
            
            if ($rowUsers['USER_STATUS']==2) { // utilizador bloqueado
                
                $mensagemErroSenha="Não é possível entrar no sistema. Contacte os nossos serviços para obter ajuda.";
                
            } else  if ($rowUsers['USER_STATUS']==0 ) { // Utilizador criou a conta mas não ativou
                
                $mensagemErroSenha=  $rowUsers['NOME'] . ", ainda não ativou a sua conta. A mensagem com o código inicial de ativação de conta foi enviada para a sua caixa de correio. Caso não a encontre na sua caixa de entrada, verifique também o seu correio não solicitado ou envie-nos um email para ativarmos a sua conta. Obrigado.";
                
            } else  if ( password_verify($senha, $rowUsers["PASSWORD"])) {
                
                $_SESSION["UTILIZADOR"]=$rowUsers["CODIGO"];
                $_SESSION["NIVEL_UTILIZADOR"]=$rowUsers["NIVEL"];
                $_SESSION["NOME_UTILIZADOR"]= $rowUsers["NOME"];
                $_SESSION["EMAIL_UTILIZADOR"]= $rowUsers["EMAIL"];
                
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                header("Location: index.php");
            } else {
                $mensagemErroSenha = "Senha incorreta!";
            }
        }
    } else {
        $mensagemErroCodigo = "O código de utilizador não existe na nossa base de dados!";
    }
    
    $stmt->free_result();
    $stmt->close();
    
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">

  </head>

  <body>

<section class="ftco-section">
      <div class="container">
          <div class="row justify-content-center">
          <div class="col-md-6 text-center mb-5">
          <h2 class="heading-section">Login </h2>
      </div>
      </div>
           <div class="row justify-content-center">
                  <div class="col-md-6 col-lg-5">
                  <div class="login-wrap p-4 p-md-5">
                    <div class="icon d-flex align-items-center justify-content-center">
                    <span class="fa fa-user-o"></span>
                </div>
                <h3 class="text-center mb-4">Have an account?</h3>

<!-- interface para entrar no sistema -->


 
  <p class="w3-center w3-large">Exclusivo para utilizadores registados.</p>
   
    
  <div style="margin-top:48px">

    <form action="#" method="POST">

    Código de utilizador:
      <p><input class="form__field" type="text" style="width:300px" placeholder="Código de utilizador"  name="formCodigo" value="<?php echo $codigo;?>"></p>
      <p class="w3-large w3-text-red"><?php echo $mensagemErroCodigo;?></p>
 
    Senha:      
      <p><input class="form__field" type="password" placeholder="Senha"  name="formSenha" value="<?php echo $senha;?>"></p>
      <p class="w3-large w3-text-red"><?php echo $mensagemErroSenha;?></p>
 
      <p>
        <button class="form__field" name="botao-iniciar-sessao" type="submit">INICIAR SESSÃO</button>
        <button class="form__field" name="botao-cancelar-entrada" type="submit">CANCELAR</button>
      </p>
    </form>
  </div>
  
  
   <p> <a href="userRecuperarSenha.php" class="w3-hover-text-green"> Esqueceu-se da senha?</a></p>
   <p><a href="userCriarConta.php" class="w3-hover-text-green" >Criar conta? </a></p>
</div>

<!--  -->

</body>
</html>

