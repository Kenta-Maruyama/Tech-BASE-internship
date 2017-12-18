<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head><title>ラーメンブログ</title></head>
<body>

<?php
//文字化け防止
header('Content-Type: text/html; charset=UTF-8');
//ファイルを変数に格納
$filename='mission_2-6.txt';
//変数にデータを格納
$name = $_POST['name'];
$com1 = $_POST['com1'];
$time = date("Y年m月d日G時i分");
$delete = $_POST['delete'];
$doEdi = $_POST['do_edi'];
$ediNumber = $_POST['edit_num'];
$ediWrite = $_POST['ediWrite'];
$pass = $_POST['pass'];
$pass_del = $_POST['pass_del'];
$pass_edi = $_POST['pass_edi'];

//名前、コメントがないと削除、編集できる
if(!empty($name) && !empty($com1) && !empty($pass) && empty($ediNumber) && empty($delete) && empty($doEdi) && empty($ediWrite)){
	//配列に格納
	$array = file($filename, FILE_IGNORE_NEW_LINES);
	//数えて
	$n=count($array)+1;
	//書き込みデータ
	$newdata ="$n<>$name<>$com1<>$time<>$pass<>\n"; 
	
	$fp = fopen($filename,"a");
	fwrite($fp,$newdata);
	$fp = fclose($fp);
}
	
//削除機能
//削除番号とパスワードが送信された
if(!empty($delete) && !empty($pass_del)){
		//fileで配列に読み込む
		$array = file($filename);
		$fp=fopen($filename,"w");
		
	   //配列の数だけループ
	   foreach($array as $value){
	   
	   //explodeで分割してlistで変数に格納したものを変数に格納
	   list($nu,$na,$com,$ti,$pa,$n) = explode("<>",$value);
	   //$nuと$deleteが一致かつパスが一致していたら改行に書き換える
			if($nu == $delete && $pa == $pass_del){
				fwrite($fp,"\n");
			}else{//一致していないとき、そのまま書き込み
				fwrite($fp,$value);
			}
		if($nu == $delete && $pa !== $pass_del){
		$ertxt_d = "パスワードが間違っています";
		}
		}
		$fp=fclose($fp);
			
			//ファイル上書き
			$rtxt = file_get_contents('mission_2-6.txt');
			$fp = fopen($filename,"w");
			fwrite($fp,$rtxt);
			$fp = fclose($fp);
	}
//編集機能
//上でまとめ$doEdiは受け取っていて、htmlでif文は動くはず
//なので、パスワードが正しかったときで分岐する
if(!empty($ediNumber) && !empty($pass_edi)){
	//fileで配列に読み込む
	$array = file($filename);
	$fp=fopen($filename,"r+");
	//配列の数だけループ
	foreach($array as $value){
		//explodeで分割してlistで変数に格納したものを変数に格納
		$line = list($nu,$na,$com,$ti,$pa,$n) = explode("<>",$value);
		//$nuと$deleteが一致しているか
		if($nu == $ediNumber && $pa == $pass_edi){
			array_shift($line);
			array_splice($line,2);
			
			
			$ediName=$line[0];
			$ediCom=$line[1];
		}
		if($nu == $ediNumber && $pa !== $pass_edi){
		$ertxt_e = "パスワードが間違っています";
	}
	}
		$fp = fclose($fp);
}
//名前、コメントがあり編集
if(!empty($ediWrite)){
	
	//fileで配列に読み込む
	$array = file($filename);
	
	//空にして開く
	$fp=fopen($filename,"w+");
	
	//配列の数だけループ
	foreach($array as $value){
		//explodeで分割してlistで変数に格納したものを変数に格納
		list($nu,$na,$com,$ti,$pa,$n) = explode("<>",$value);
		//$nuと$deleteが一致しているか
		if($nu == $ediWrite){
		//書き込みデータ
		$ediData ="$nu<>$name<>$com1<>$time<>$pass<>\n";
		//echo "check_edit|".$ediData."|";//デバッグ
		fwrite($fp,$ediData);
		}else{
		fwrite($fp,$value);
	}
	}
	$fp = fclose($fp);
}
?>


<!-見出し->
<font color="green"><h2>ラーメンブログ</h2></font>
</script>

<!-入力フォームを表示->
<form action="" method="post">
<p>
  <!-フォームの中身->
		名前：
　　　　　　　　<input type="text" name="name" value = "<?php echo $ediName; ?>" >
		<p>コメント：<textarea cols="25" rows="2" name="com1"><?php echo $ediCom; ?></textarea><br />
		パスワード：<input type="password" name="pass"></p>
<!-- //空欄かどうか -->
<?php if(empty($name) && empty($com1) && empty($delete) && empty($ediNumber)){ ?>
<font>名前、コメント、パスワードを入力</font><br>
<?php } ?>
<!-- //パスワードがあるか -->
<?php if(empty($pass) && !empty($name) && !empty($com1)){ ?>
<font color="red">パスワードを入力してください</font><br>
<?php } ?>
<!-- echo "コメント用パス"; -->
<!-送信ボタンをつくる->
<p><input type="submit" value="送信">


<!-- 編集モードのとき、編集であるとわかるhiddenを送る -->
<?php if(!empty($ediNumber)){ ?>
<input type='hidden' name='ediWrite' value="<?php echo $ediNumber; ?>"></p>
<?php } ?>

</form>	
	
<!-編集フォームを表示->
<form action="" method="post">
<p>
	<!-フォームの内容->
		編集対象番号：<input type="text" name="edit_num" size="5"><br />
		パスワード：<input type="password" name="pass_edi"></p>
<?php if(!empty($ertxt_e)){ ?>
<font color="red"><?php echo $ertxt_e; ?></font><br>
<?php } ?>

<!-- //編集したいけどパスがない -->
<?php if(!empty($ediNumber) && empty($pass_edi)){ ?>
<font color="red">パスワードを入力してください</font><br>
<?php } ?>
<!-- //echo "編集用パス"; -->
	<!-編集ボタンをつくるhiddenで->
	<input type="submit" value="編集"></p>

</form>	


<!-削除フォームを表示->
<form action="" method="post">
<p>
	<!-フォームの内容->
	削除対象番号：<input type="text" name="delete" size="5"><br />
	パスワード：<input type="password" name="pass_del"></p>

	<?php if(!empty($ertxt_d)){ ?>
		<font color="red"><?php echo $ertxt_d; ?></font><br>
	<?php } ?>

<!-- //削除したいけどパスがない -->
<?php if(!empty($delete) && empty($pass_del)){ ?>
<font color="red">パスワードを入力してください</font><br>
<?php } ?>
	<!-削除ボタンをつくる->
	<input type="submit" value="削除"></p>

</form>
<br>
<hr/>
<br>

<?php
//投稿内容表示
//テキストをfileで配列として読み込み、変数に格納
$array = file($filename,FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
//配列の数だけループ
foreach($array as $value){
//explodeとlistできれいに表示
list($nu,$na,$com,$ti) = explode("<>",$value);
echo "投稿番号：$nu お名前：$na コメント：$com 投稿時間：$ti<br>";
}
?>


</html>
