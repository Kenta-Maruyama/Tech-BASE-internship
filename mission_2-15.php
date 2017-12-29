<?php
//データベースへの接続
$dsn='mysql:dbname=co_1021_it_99sv_coco_com;host=localhost';
$user='co-1021.it.99sv-';
$password='nIaBxA3';
$pdo = new PDO($dsn,$user,$password);
$stmt = $pdo->query('SET NAMES utf8');
//2-8テーブルをつくる 名前・コメント・パスが入る
$sql="CREATE TABLE ramen".
		"("."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"."name char(32),"."comment TEXT,"."pass TEXT,"."time TIMESTAMP".");";
$stmt=$pdo->query($sql);
//2-9
/*//テーブル一覧を表示する
$sql = 'SHOW TABLES;';
$result = $pdo->query($sql);
foreach($result as $row){
	echo $row[0];
	echo $row[1];
	echo $row[2];
	echo '<br>';
	}
	
echo "<hr>";*/
//2-10
/*//show create table
$sql ='SHOW CREATE TABLE ramen;';
$result = $pdo->query($sql);
foreach($result as $row){
	print_r($row);
}
echo "<hr>";*/
//2-11 書き込み
//変数受け取り
$name= $_POST['name'];
$comment = $_POST['com1'];
$pass = $_POST['pass'];
$intime = date("Y/m/d H:i");
$doEdi = $_POST['doEdi'];
$ediNumber = $_POST['edit_num'];
$delNumber = $_POST['delete_num'];
$pass_edi = $_POST['pass_edi'];
$pass_del = $_POST['pass_del'];
/*//変数デバッグ
echo "name|".$name."|";
echo "com|".$comment."|";
echo "pass|".$pass."|";
echo "time|".$intime."|<hr>";*/
//書き込み---------------------------------------------------------------------------------
//名前・コメント・パスがあるとき書き込み
if(!empty($name) && !empty($comment) && !empty($pass) && empty($ediNumber) && empty($delNumber) && empty($doEdi)){
	//PDOでINSETを利用してカラムに値を代入する
	$sql = $pdo -> prepare("INSERT INTO ramen (name,comment,pass,time) VALUES(:name,:comment,:pass,:time)");
	$sql->bindParam(':name',$name,PDO::PARAM_STR);
	$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
	$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
	$sql->bindValue(':time',$intime,PDO::PARAM_STR);
	$sql->execute();
	
	//デバッグ
	//echo "書き込み内容|".$name.",".$comment.",".$intime."|<hr>";
}
//編集機能---------------------------------------------------------------------------------
//編集番号とパスワードがあるとき、編集する名前とコメントを取得する
if(!empty($ediNumber) && !empty($pass_edi)){
	//編集番号をidに
	$id = $_POST['edit_num'];
	//編集する内容を受け取る whereで探す
	$stmt =	$pdo->prepare("select name, comment, pass from ramen where id = :id");
	$stmt -> bindParam(':id',$id, PDO::PARAM_INT);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();
//編集パスワードがあっているならDBにある名前・コメントを変数に格納する
if($pass_edi == $result['pass']){
	$EdiName = $result['name'];
	$EdiCom = $result['comment'];
	/*//デバッグ
	echo "Ediname|".$EdiName."|";
	echo "Edicom|".$EdiCom."|<hr>";*/
}
	if($pass_edi !== $result['pass']){
		
		$edi_error = "パスワードが間違っています";
	}
}
//名前・コメント・hiddenがある
if(!empty($doEdi)){
	//2-13
	//idを変数に入れる
	$id = $doEdi;
	//書き換える内容を変数に入れる	
	$edi_name = $name;
	$edi_com = $comment;
	//idを検索し、updateで変数入れ替える
	$sql = "update utau set name='$edi_name',comment='$edi_com' where id = '$id';";
	$result = $pdo->query($sql);
}
//削除機能---------------------------------------------------------------------------------
//削除番号とパスワードが送信された
if(!empty($delNumber) && !empty($pass_del)){
	//削除番号と一致するidの行を抜き出す
	$id = $delNumber;
	$stmt =	$pdo->prepare("select name, comment, pass from ramen where id = :id");
	$stmt -> bindParam(':id',$id, PDO::PARAM_INT);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();
	
	if($pass_del == $result['pass']){
		$id = $delNumber;
		$sql = "delete from ramen where id ='$id';";
		$result = $pdo->query($sql);
		
	}else{
		$del_error = "パスワードが間違っています";
	}
}
?>

<!DOCTYPE html>
<html>
		<head>
			<meta charset="utf-8">
			<title>ラーメンブログ</title>
		</head>
		<body>
			<!-見出し->
			<font color="green"><h2>ラーメンブログ</h2></font>
		</script>
		
		<!-入力フォームを表示->
		<form action="" method="post">
		<p>・書き込みをする<br><?php if(!empty($ediNumber)){ ?><font color="blue">！編集モード<br></font><?php } ?>
		<!-フォームの内容->
		名前：<input type="text" name="name" size="30" value = "<?php echo $EdiName; ?>" >
		<p>コメント：<textarea cols="25" rows="2" name="com1"><?php echo $EdiCom; ?></textarea><br>
		パスワード：<input type="password" name="pass"></p>
		<!-- //空欄かどうか -->
			<?php if(empty($name) && empty($comment) && empty($delNumber) && empty($ediNumber)){ ?>
				<font color="red">名前,コメント,パスワードを入力</font><br>
			<?php } ?>
			<!-- //パスワードがあるか -->
			<?php if(empty($pass) && !empty($name) && !empty($comment)){ ?>
				<font color="red">パスワードを入力してください</font><br>
		<?php } ?>
		
		<!-送信ボタンをつくる->
		<p><input type="submit" value="送信">
		<!-- 編集モードのとき、編集であるとわかるhiddenを送る -->
		<?php if(!empty($ediNumber)){ ?>
			<input type='hidden' name='doEdi' value="<?php echo $ediNumber; ?>"></p>
		<?php } ?>
		
		
		
		<!-編集フォームを表示->
		<form action="" method="post">
		<p>・投稿を編集する<br>
		<!-フォームの内容->
		対象番号：<input type="text" name="edit_num" size="5"><br>
		パスワード：<input type="password" name="pass_edi"></p>
		<!-- //編集したいけどパスがない -->
			<?php if(!empty($ediNumber) && empty($pass_edi)){ ?>
				<font color="red">パスワードを入力してください</font><br>
			<?php } ?>
		<!-- パスワードが間違っている -->
		<?php if(!empty($edi_error)){ ?>
				<font color="red"><?php echo $edi_error; ?></font><br>
		<?php } ?>
			
		<!-編集ボタンをつくるhiddenで->
		<input type="submit" value="編集"></p>
	
	
	
		<!-削除フォームを表示->
		<form action="" method="post">
		<p>・投稿を削除する<br>
		<!-フォームの内容->
		対象番号：<input type="text" name="delete_num" size="5"><br>
		パスワード：<input type="password" name="pass_del"></p>
		<!-- //削除したいけどパスがない -->
			<?php if(!empty($delNumber) && empty($pass_del)){ ?>
				<font color="red">パスワードを入力してください</font><br>
			<?php } ?>
		<!-- パスワードが間違っている -->
		<?php if(!empty($del_error)){ ?>
				<font color="red"><?php echo $del_error; ?></font><br>
		<?php } ?>
		<!-削除ボタンをつくる->
		<input type="submit" value="削除"></p>
		
		<hr>
		
		<?php //2-12応用 書き込み内容の表示
		$sql = 'SELECT * FROM ramen ORDER BY id ASC;';
		$results = $pdo->query($sql); //実行・結果取得
		foreach($results as $row){
			//$rowの中にはテーブルのカラム名が入る
			echo "投稿番号:".$row['id'].' ';
			echo "名前：".$row['name'].' ';
			echo "コメント：".$row['comment'].' ';
			echo "投稿時間：".$row['time'].'<br>';
			}
 		?>
		
		</body>
</html>