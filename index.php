<?php
include "./php_file/check_sessions.php";
//проверка сессии
if(check_login_pas($_SESSION['login'],$_SESSION['password'])==true) header("Location: ./php_file/vhod.php"); else session_destroy();
?>
<html>
<head>
<meta http-equiv="Content-Type"  content="text/html; charset=utf-8" />
<link 	type="text/css" 	 	rel="stylesheet"		href="./css/config.css" /> <!-- стиль всего сайта-->
<link 	type="text/css" 		rel="stylesheet" 	href="./../css/jquery.alerts.css"  />
<script type="text/javascript" src="./script/jquery.min.js"></script> 
</head><body>
<script type='text/javascript'>

$(document).ready(function() {
						      
							$("#ok").click(function(){
login=$("#login").val();
password=$("#password").val();
		var exp = /^[a-zA-Z0-9_]+$/g;
		var resLogi = login.search(exp);
		var paLogi = password.search(exp);
					if(resLogi == 0 && paLogi == 0 && login.length > 4 && password.length > 6 ){
			$.ajax({
			url: "./php_file/check_input.php",
			type: "POST",
			data: "pas="+password+"&logi="+login,
			cache: false,
			success: function(response){
			//alert('ss'+response);
				if(response == true){
				$("#login").removeClass().addClass("inputGreen");
				$("#password").removeClass().addClass("inputGreen");
				location.reload();
								}else{
				$("#login").removeClass().addClass("inputRed");
				$("#password").removeClass().addClass("inputRed").val('');	
										}
											}		
					});			
																	}else{				
				$("#login").removeClass().addClass("inputRed").val('');
				$("#password").removeClass().addClass("inputRed").val('');	
				exit;
												}	
														});


});

</script>


<?php 

echo '<div class="reg_container">
<h2 align="center">Вход в cистему</h2>
<form>
    <table width="180" border="0" align="center" valign="center">
      <tr>
        <td>
            <label for="login" class=tabb width="1000" style="margin: 0 auto;">Логин</label>
            <input type="text" name="login" id="login" style="margin: 0 auto;" class=tabb required>
        </td>
       
      </tr>
      <tr>
        <td><span id="sprypassword1">
          <label for="password" class=tabb style="margin: 0 auto;">Пароль</label>
          <input type="password" name="password" style="margin: 0 auto;" id="password" class=tabb required>
        </td>
      </tr>
      <tr>
        <td>
        <input type="button" name="ok" id="ok" class="knop" value="Вход" style="margin: 0 auto;">
        </td>
      </tr>
	  </table>
</form>
</div>';

?>

</body>
</html>

