
<?php
 

    session_start();
    include_once 'conexaobasedados.php';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); error_reporting(E_ALL);


    
    
    $ticket_user = $_SESSION["UTILIZADOR"];
    $ticket_descricao=$_SESSION["UTILIZADOR"];
    $ticket_local=$_SESSION["UTILIZADOR"];
    $ticket_nature=$_SESSION["UTILIZADOR"];

    
  

            if (mysqli_connect_error()){
                echo "alert('Error')";
                die('Connect Error ('. mysqli_connect_errno() .') '
                . mysqli_connect_error());
            }else{
                $sql = "INSERT INTO ticket(ticket_user,ticket_local,ticket_nature,ticket_descricao)
                values (?,?,?,?)";
                if ($stmt=mysqli_prepare($_conn,$sql)){
                    $stmt->bind_param('ssss',$ticket_user,$ticket_local,$ticket_nature,$ticket_descricao);
                   
                    $stmt->execute();
                    $stmt->free_result();
                    $stmt->close();
                    echo "alert('Success')"; //New record is inserted sucessfully
                }
            }
            



            

    
     
