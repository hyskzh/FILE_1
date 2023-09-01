    <?php

        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //データベース内にテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS board"    //作成済みのtableを創ろうとするエラーを防ぐ
        . "("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"          //現在格納されている最大の数値に１追加した数値で自動格納
        . "name char (32),"                             //name
        . "comment TEXT,"                               //comment
        . "date DATETIME,"                              //date
        . "pas TEXT"                                    //pas
        . ");";
        $stmt = $pdo->query($sql);                      //目的のデータを抽出するquery関数
        
    //データレコードの挿入
    
        //$name
        if(isset($_POST["name"])){
            $name=$_POST["name"];
        }else{$name="";}
        //$comment(コメント)
        if(isset($_POST["comment"])){
            $comment=$_POST["comment"];
        }else{$comment="";}
        //$date(日付)
        $date = date("Y/m/d H:i:s");
        //$pas(パスワード)
        if(isset($_POST["pas"])){
            $pas=$_POST["pas"];
        }else{$pas="";}
        //$edinum(編集番号)
        if(isset($_POST["edinum"])){
            $edinum=$_POST["edinum"];
        }else{$edinum="";}
        
        //$del(消去番号)
        if(isset($_POST["del"])){
            $del=$_POST["del"];
        }else{$del="";}
        //$delpas(消去パスワード)
        if(isset($_POST["delpas"])){
            $delpas=$_POST["delpas"];
        }
        
        //$edi(編集)
        if(isset($_POST["edi"])){
            $edi=$_POST["edi"];
        }
        //$edipas(編集パスワード)
        $edipas="";
        if(isset($_POST["edipas"])){
            $edipas=$_POST["edipas"];
        }
        
        //$ediname&$edicomment&$edipass
        $ediname="";
        $edicomment="";
        $edipass="";
        $edinumm="";
       
        

        if(!empty($name) && !empty($comment) && !empty($pas)){
            if(!empty($edinum)){                                        //編集後のデータ入力
                $id = $edinum;
                $name = $name;
                $comment = $comment;
                $date = $date;
                $pas = $pas;
                $sql = 'UPDATE board SET name=:name,comment=:comment, date=:date, pas=:pas WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pas', $pas, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }else{                                                      //データ入力
                $sql = "INSERT INTO board(name, comment, date, pas) VALUES(:name, :comment, :date, :pas)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pas', $pas, PDO::PARAM_STR);
                $stmt->execute();
            }
        }elseif(!empty($del) && !empty($delpas)){                       //削除機能
            $id = $del ; // idがこの値のデータだけを抽出したい、とする
            $sql = 'SELECT * FROM board WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                    $pasd=$row['pas'];
                }
            if($delpas == $pasd){
                $id = $del;
                $sql = 'delete from board where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }else{
                echo "パスワードが違います";
                
            }
        }elseif(!empty($edi) && !empty($edipas)){                       //編集機能(フォームに該当データを表示)
                $id = $edi ; // idがこの値のデータだけを抽出したい、とする
                $sql = 'SELECT * FROM board WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // ←SQLを実行する。
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    $pasp=$row['pas'];
                }
            if($edipas == $pasp){
                foreach ($results as $row){
                    $edinumm=$row['id'];
                    $ediname=$row['name'];
                    $edicomment=$row['comment'];
                    $edipass=$row['pas'];
                }
            }else{
                echo "パスワードが違います";
            }
        }
    ?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>m5-1</title>
</head>
<body>
    <hr>
    
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php echo $ediname ;?>"><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $edicomment ;?>"><br>
        <input type="text" name="pas" placeholder="パスワード" value="<?php echo $edipass ;?>"><br>
        <input type="hidden" name="edinum" placeholder="編集番号" value="<?php echo $edinumm;?>">
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <input type="text" name="del" placeholder="消去対象番号" ><br>
        <input type="text" name="delpas" placeholder="パスワード"><br>
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <input type="text" name="edi" placeholder="編集対象番号" ><br>
        <input type="text" name="edipas" placeholder="パスワード"><br>
        <input type="submit" name="submit">
    </form>
    <hr>


    


</body>
</html>

    <?php
     //bindParamの因数名(:nameなど)はテーブルのカラム名と一緒にする
        $sql = 'SELECT * FROM board';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchALL();
        foreach ($results as $row){
            echo $row['id'].'　';
            echo $row['name'].'　';
            echo $row['comment'].'　';
            echo $row['date']."<br>";
        }
        echo "<hr>";
        
    ?>
