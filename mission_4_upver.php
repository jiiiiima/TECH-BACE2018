<html>
<head>
	<title>mission_4</title>
	<meta charset="utf-8">
</head>

<body>
<?php
$dsn='データベース名'; //データベースへの接続
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //エラーを吐かせる処理
ini_set('display_errors', 1);
error_reporting(E_ALL);

try{
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//例外処理を投げるように設定
$pdo -> begintransaction();
$sql = "CREATE TABLE mission_4"	
."("
."id INT auto_increment primary key,"
."name char(32),"
."comment TEXT,"
."date DATETIME,"
."password char(32)"
.");";
$stmt = $pdo->query($sql);
$pdo->commit();
}
catch(PDOException $e){
$pdo -> rollBack();
}

///////////////////////////////////////////////////////////////////////ブラウザ表示を整える
	if(!empty($_POST['edit']) && !empty($_POST['editpass'])){
		$editid=$_POST['edit'];
		$editpass=$_POST['editpass'];
		$sql = $pdo -> prepare("SELECT * FROM mission_4 where id=:id");	//指定された番号の投稿を取得
		$sql -> bindParam(':id',$editid,PDO::PARAM_STR);
		$sql -> execute();

		$result = $sql -> fetch();
		$checkid = $result['id'];
		$editname = $result['name'];
		$editcomment = $result['comment'];
		$checkpass = $result['password'];
		if($checkid == $editid){	//投稿番号の確認
			if($checkpass == $editpass){	//パスワードの確認
			//合っていたらその投稿をフォームに返す
echo <<< EOT
					<form action="mission_4.php" method="post">		<!--編集フォーム-->
						<input type="text" name="editname" value="$editname"><br>
						<input type="text" name="editcomment" value="$editcomment">
					<!--	<input type="text" name="password" placeholder="パスワード"><br>  -->
						<input type="hidden" name="editmode" value="$editid">
						<input type="submit" value="送信"><br>
					</form>
					<form action="mission_4.php" method="post">		<!--削除フォーム-->
						<input type="text" name="delete" placeholder="削除対象番号"><br>
						<input type="text" name="deletepass" placeholder="パスワード">
						<input type="submit" value="削除"><br>
					</form>
					<form action="mission_4.php" method="post">		<!--編集番号選択フォーム-->
						<input type="text" name="edit" placeholder="編集対象番号"><br>
						<input type="text" name="editpass" placeholder="パスワード">
						<input type="submit" value="編集">
					</form>
EOT;
			}
			else{
				echo "編集エラー：パスワードが違います"."<br>";
				$form = new form();
				$form ->form_normal();
			}
		}
		else{
			echo "編集エラー：編集対象となる投稿が見つかりません"."<br>";
			$form = new form();
			$form ->form_normal();
		}
	}
	else{
		$form = new form();
		$form ->form_normal();
	}

class form {			//NOT編集時のフォームが冗長なのでクラスを書いてすっきりさせたい
	public function form_normal(){
echo <<< EOT
<form action="mission_4.php" method="post">
	<input type="text" name="name" placeholder="名前"><br>
	<input type="text" name="comment" placeholder="コメント"><br>
	<input type="text" name="password" placeholder="パスワード">
	<input type="hidden" name="editmode">
	<input type="submit" value="送信"><br>
</form>
<form action="mission_4.php" method="post">
	<input type="text" name="delete" placeholder="削除対象番号"><br>
	<input type="text" name="deletepass" placeholder="パスワード">
	<input type="submit" value="削除"><br>
</form>
<form action="mission_4.php" method="post">
	<input type="text" name="edit" placeholder="編集対象番号"><br>
	<input type="text" name="editpass" placeholder="パスワード">
	<input type="submit" value="編集">
</form>
EOT;
	}
}

//新しい変数でデータを受け取る
$date=date('Y-m-d H:i:s');
////////////////////////////////////////////////////////////////////////編集&追記処理
if(!empty($_POST['editmode'])){
try{
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//例外処理を投げるように設定
	$editedname=$_POST['editname'];
	$editedcomment=$_POST['editcomment'];
	$editmode=$_POST['editmode'];
//	echo $editedname;
//	echo $editedcomment;
	$sql = $pdo -> prepare("UPDATE mission_4 SET name =:name, comment =:comment WHERE id = :id");
	$sql-> bindParam(':name', $editedname, PDO::PARAM_STR);
	$sql-> bindParam(':comment', $editedcomment, PDO::PARAM_STR);
	$sql-> bindParam(':id', $editmode, PDO::PARAM_INT);
	$sql-> execute();
}
catch(PDOException $e){
	echo '捕捉した例外: ',  $e->getMessage(), "\n";
}
}

if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['password'])){
try{
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//例外処理を投げるように設定
$pdo -> begintransaction();
	$sql = $pdo -> prepare("INSERT INTO mission_4 (name,comment,date,password) VALUES (:name,:comment,:date,:password)");
	$name=$_POST['name'];
	$comment=$_POST['comment'];
	//dateは編集時にも利用するため前出
	$password=$_POST['password'];

	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
	$sql -> bindParam(':date', $date,PDO::PARAM_INT);
	$sql -> bindParam(':password', $password,PDO::PARAM_STR);
	$sql -> execute();
	$pdo->commit();
}
catch(PDOException $e){
	echo '捕捉した例外: ',  $e->getMessage(), "\n";
}
}
////////////////////////////////////////////////////////////////////////削除処理
if(!empty($_POST['delete'])){
	$delete=$_POST['delete'];
	$deletepass=$_POST['deletepass'];
	$sql = $pdo -> prepare("SELECT * FROM mission_4 where id=:id");	//指定された番号の投稿を取得
	$sql -> bindParam(':id',$delete,PDO::PARAM_STR);
	$sql -> execute();
	$result = $sql -> fetch();
	$checkid = $result['id'];
	$checkpass = $result['password'];
	if($checkid == $delete){	//投稿番号の確認
		if($checkpass == $deletepass){	//パスワードの確認
		//合っていたらその投稿を削除
			$sql = $pdo -> prepare("DELETE FROM mission_4 where id = :id");
			$sql -> bindParam(':id',$delete,PDO::PARAM_INT);
			$sql -> execute();
		}
		else{
		//違ったらパスワードエラーを表示
			echo	"削除エラー：パスワードが違います"."<br>";
		}
	}
	else{
	echo "削除エラー：削除対象となる投稿が見つかりません"."<br>";
	}
}
////////////////////////////////////////////////////////DBを表示する
$sql = $pdo -> prepare('SELECT * FROM mission_4');
$sql -> execute();
$result = $sql -> fetchall();
foreach($result as $row){
	echo $row['id'].',';
	echo $row['name'].',';
	echo $row['comment'].',';
	echo $row['date'].'<br>';
}

$sql=null;
$pdo=null;

?>

</body>
</html>
