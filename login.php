<?php 
	include "sql.php";
	session_start();
	function generateSalt()
		{
			$salt = '';
			$saltLength = 8;
			for($i=0; $i<$saltLength; $i++) {
				$salt .= chr(mt_rand(33,126));
			}
			return $salt;
		}
	?>
<!doctype html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Личный кабинет</title>
	</head>
	<body>
		<?php if(isset($_GET['page']) == "register" && $_GET['page'] == "register") {
			
			if (!empty($_SESSION['auth']) && $_SESSION['auth']) {
				
			echo '<meta http-equiv="refresh" content="0;url=/login.php?page=settings">';
				
			}
			
			if (isset($_POST["register"]) && isset($_POST['email']) &&
				isset($_POST['login']) && preg_match("/^(?=.*[A-Za-z]).{1,}$/i", $_POST['login']) &&
				isset($_POST['fio']) &&
				!empty($_POST["password"]) && !empty($_POST["password2"]) && trim($_POST['password']) == trim($_POST['password2']) && preg_match("/^(?=.*?[0-9])(?=.*[A-Za-z]).{6,}$/i", $_POST['password'])
				) {
				$login = trim(mb_strtolower($_POST['login']));
				$email = trim(mb_strtolower($_POST['email']));
				$fio = trim($_POST['fio']);
				$password = trim($_POST['password']); 
				
				$query = 'SELECT * FROM users WHERE login="'.$login.'"';
				$isLoginFree = mysqli_fetch_assoc(mysqli_query($mysqli, $query));
					
				$query2 = 'SELECT * FROM users WHERE email="'.$email.'"';
				$isEmailFree = mysqli_fetch_assoc(mysqli_query($mysqli, $query2));
				
				if(empty($isLoginFree) && empty($isEmailFree)) {
						
					$salt = generateSalt();
					$saltedPassword = md5($password.$salt);
						
					$query3 = "INSERT INTO `users` (`email`, `login`, `fio`, `password`, `salt`, `cookie`) VALUES ('".$email."', '".$login."', '".$fio."', '".$saltedPassword."', '".$salt."', '')";
					mysqli_query($mysqli, $query3); 
					echo '<meta http-equiv="refresh" content="0;url=/login.php">';
						
					} else {
						
						$message[] = "Логин или Email занят.";
						
					}
				
				} elseif (isset($_POST["register"]) && empty($_POST['email'])) {
					
					$message[] = "Введите Email.";
					
				} elseif (isset($_POST["register"]) && empty($_POST['fio'])) {
					
					$message[] = "Введите ФИО.";
					
				} elseif(isset($_POST["register"]) && trim($_POST['password']) != trim($_POST['password2']) && preg_match("/^(?=.*?[0-9])(?=.*[A-Za-z]).{6,}$/i", $_POST['password'])) {
				
					$message[] = "Пароли не совпадают.";
				
				} elseif(isset($_POST["register"]) && trim($_POST['password']) == trim($_POST['password2']) && !preg_match("/^(?=.*?[0-9])(?=.*[A-Za-z]).{6,}$/i", $_POST['password'])) {
				
					$message[] = "Пароль введен невалидно.";
				
				} elseif (isset($_POST["register"]) && isset($_POST['login']) && !preg_match("/^(?=.*[A-Za-z]).{1,}$/i", $_POST['login'])) {
					
					
					$message[] = "Введите логин латинскими буквами.";
					
				}
				
				?>
		<form action="/login.php?page=register" method="post">
			<table>
				<tbody>
					<?php if(!empty($message)) { 
						foreach($message as $value) {
							echo "<ul><li color=red> $value </li></ul>";
						}
						} ?>
					<tr>
						<td>Email: </td>
						<td><input name="email" type="email" placeholder="Email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,25}$" value="" required></td>
					</tr>
					<tr>
						<td>Логин: </td>
						<td><input name="login" type="text" placeholder="Логин" pattern="^[a-zA-Z0-9]{2,150}" value="" required></td>
					</tr>
					<tr>
						<td>ФИО: </td>
						<td><input name="fio" type="text" value="" placeholder="ФИО" required></td>
					</tr>
					<tr>
						<td>Пароль: </td>
						<td><input name="password" type="password" value="" placeholder="Пароль" required></td>
					</tr>
					<tr>
						<td>Повторите пароль: </td>
						<td><input name="password2" type="password" placeholder="Повторите пароль" value="" required></td>
					</tr>
					<tr>
						<td colspan="2"><input name="register" type="submit" value="Регистрация"></td>
					</tr>
					<tr>
						<td colspan="2"><a href="/login.php">Вход</a></td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php } elseif(isset($_GET['page']) == "settings" && $_GET['page'] == "settings") {
			$login = $_SESSION['login'];
			$query = 'SELECT * FROM users WHERE login="'.$login.'"';
			$result = mysqli_query($mysqli,$query);
			$user = mysqli_fetch_assoc($result);
				
			if(isset($_POST["save"]) && !empty($_POST['fio']) && !empty($_POST["password"]) && !empty($_POST["password2"]) && trim($_POST['password']) == trim($_POST['password2']) && preg_match("/^(?=.*?[0-9])(?=.*[A-Za-z]).{6,}$/i", $_POST['password'])) {
				
			$password = trim($_POST['password']);
			$salt = generateSalt();
			$saltedPassword = md5($password.$salt);
			$fio = trim($_POST['fio']);
			
			$query = 'UPDATE users SET fio="'.$fio.'", password="'.$saltedPassword.'", salt="'.$salt.'" WHERE login="'.$login.'"';
			mysqli_query($mysqli, $query); 
			echo '<meta http-equiv="refresh" content="0;url=/login.php?page=settings">';
			
			} elseif(isset($_POST["save"]) && ! empty($_POST['fio']) && empty($_POST["password"]) && empty($_POST["password2"])) {
				
			$fio = trim($_POST['fio']);
			
			$query = 'UPDATE users SET fio="'.$fio.'" WHERE login="'.$login.'"';
			mysqli_query($mysqli, $query); 
			echo '<meta http-equiv="refresh" content="0;url=/login.php?page=settings">';
			
			
			} elseif(isset($_POST["save"]) && trim($_POST['password']) != trim($_POST['password2']) && preg_match("/^(?=.*?[0-9])(?=.*[A-Za-z]).{6,}$/i", $_POST['password'])) {
				
				$message[] = "Пароли не совпадают.";
				
			} elseif(isset($_POST["save"]) && trim($_POST['password']) == trim($_POST['password2']) && !preg_match("/^(?=.*?[0-9])(?=.*[A-Za-z]).{6,}$/i", $_POST['password'])) {
				
				$message[] = "Пароль введен невалидно.";
				
			} elseif(isset($_POST["save"]) && empty($_POST['fio'])) {
				
				$message[] = "Введите ФИО.";
				
			}
				
				?>
		<form action="/login.php?page=settings" method="post">
			<table>
				<tbody>
					<?php if(!empty($message)) { 
						foreach($message as $value) {
							echo "<ul><li color=red> $value </li></ul>";
						}
						} ?>
					<tr>
						<td>ФИО: </td>
						<td><input name="fio" type="text" value="<?php echo $user['fio']; ?>" required></td>
					</tr>
					<tr>
						<td>Пароль: </td>
						<td><input name="password" type="password" value=""></td>
					</tr>
					<tr>
						<td>Повторите пароль: </td>
						<td><input name="password2" type="password" value=""></td>
					</tr>
					<tr>
						<td colspan="2"><input name="save" type="submit" value="Сохранить"></td>
					</tr>
					<tr>
						<td colspan="2"><a href="/login.php?page=logout">Выход</a></td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php } elseif(isset($_GET['page']) == "logout" && $_GET['page'] == "logout") {
			
			if (empty($_SESSION['auth'])) {
				
			echo '<meta http-equiv="refresh" content="0;url=/login.php">';
				
			}
			
			if (!empty($_SESSION['auth']) && $_SESSION['auth']) {
			session_destroy();
			
			setcookie('login', '', time());
			setcookie('key', '', time());
			echo '<meta http-equiv="refresh" content="0;url=/login.php">';
			}
			
			} else {
			
			if (!empty($_SESSION['auth']) && $_SESSION['auth']) {
				
			echo '<meta http-equiv="refresh" content="0;url=/login.php?page=settings">';
				
			}	
			
			if ( isset($_POST["input"]) && !empty($_POST['password']) && !empty($_POST['login']) ) {
			$login = $_POST['login']; 
			$password = $_POST['password']; 
			$query = 'SELECT * FROM users WHERE login="'.$login.'"';
			$result = mysqli_query($mysqli,$query);
			$user = mysqli_fetch_assoc($result); 
			if (!empty($user)) {
				$salt = $user['salt'];
				$saltedPassword = md5($password.$salt);
				if ($user['password'] == $saltedPassword) {
					$_SESSION['auth'] = true; 
					$_SESSION['id'] = $user['id']; 
					$_SESSION['login'] = $user['login'];
			if ( !empty($_POST['remember']) and $_POST['remember'] == 1 ) {
				$key = generateSalt();
				setcookie('login', $user['login'], time()+60*60*24*30);
				setcookie('key', $key, time()+60*60*24*30);
				$query = 'UPDATE users SET cookie="'.$key.'" WHERE login="'.$login.'"';
				mysqli_query($mysqli, $query);
			}
					
					echo '<meta http-equiv="refresh" content="0;url=/login.php?page=settings">';
				}
				else {
					$message[] = "Неправильный логин или пароль.";
				}
			} else {
				$message[] = "Такого логина нет.";
			}
			}
			if (empty($_SESSION['auth']) or $_SESSION['auth'] == false) {
			if ( !empty($_COOKIE['login']) and !empty($_COOKIE['key']) ) {
				$login = $_COOKIE['login']; 
				$key = $_COOKIE['key'];
				$query = 'SELECT * FROM users WHERE login="'.$login.'" AND cookie="'.$key.'"';
				$result = mysqli_query($mysqli,$query);
				$user = mysqli_fetch_assoc($result); ; 
				if (!empty($result)) {
					$_SESSION['auth'] = true; 
					$_SESSION['id'] = $user['id']; 
					$_SESSION['login'] = $user['login']; 
				}
			}
			}
			
			
			?>
		<table>
		<tbody>
			<?php if(!empty($message)) { 
				foreach($message as $value) {
					echo "<ul><li color=red> $value </li></ul>";
				}
				} ?>
			<form action="/login.php" method="post">
				<tr>
					<td><input type="text" name="login" value="" placeholder="Логин" pattern="^[a-zA-ZА-Яа-яЁё-]{2,150}" required></td>
				</tr>
				<tr>
					<td><input type="password" name="password" value="" placeholder="Пароль" required></td>
				</tr>
				<tr>
					<td><input name='remember' type='checkbox' value='1'> Запомнить</td>
				</tr>
				<tr>
					<td colspan="2"><input name="input" type="submit" value="Войти" /></td>
				</tr>
				<tr>
					<td colspan="2"><a href="/login.php?page=register">Регистрация</a></td>
				</tr>
			</form>
			<?php }?>
	</body>
</html>