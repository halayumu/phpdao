<?php

/**
 * PH34 課題2 マスタテーブル管理  *
 * @author Ayumu ISHIDA
 *
 * ファイル名=empAdd.php
 * フォルダ=/ph34/scottadmindao/public/emp/ 
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/Conf.php"); //サーバー変数
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Emp.php"); //エンティティ
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/EmpDAO.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Dept.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/DeptDAO.php");

//---------------------------[ドロップダウン加工処理]----------------------------//
//----[雇用日結合]----//
$_POST["Year"];
$_POST["Month"];
$_POST["Day"];
$schedule = array($_POST["Year"], $_POST["Month"], $_POST["Day"]);
$str_schedule = implode("-", $schedule);
$str_schedule = preg_replace("/( |　)/", "", $str_schedule);

//----[上司番号分解]----//
$addEmMgr = explode(':', $_POST["addEmMgr"]); //上司番号抜き出し
$addEmMgr = intval($addEmMgr[0]); //数値に変換

// var_dump($_POST["addDeptId"]);

//----[所属部門ID]----//
//---------------------------[入力値取得処理]----------------------------//
$addEmNo = intval($_POST["addEmNo"]);
$addemNo = $addEmNo; //従業員番号
$addemName = $_POST["addEmName"]; //従業員名
$addemJob = $_POST["addEmJob"]; //役所
$addemMgr = $addEmMgr; //上司番号
$addemHiredate = $str_schedule; //雇用日
$addemSal = $_POST["addEmSal"]; //給料
$addDeptId = intval($_POST["addDeptId"]); //所属部門ID

//----[入力前後空白削除処理]---//
$addemName = trim($addemName); //従業員名
$addemJob = trim($addemJob); //役所
$addemName = str_replace("　", " ", $addemName); //文字列変換関数
$addemJob = str_replace("　", " ", $addemJob); //文字列変換関数(全角が入ってると入らない様な処理をする)
$addemName = trim($addemName); //前後の半角スペース
$addemJob = trim($addemJob); //前後の半角スペース

//-----------------------[empインスタンス保管処理]-------------------//
$emp = new Emp();
$emp->setemNo($addemNo); //従業員番号
$emp->setemName($addemName); //従業員名
$emp->setemJob($addemJob); //役所
$emp->setemMgr($addemMgr); //上司番号
$emp->setemHiredate($addemHiredate); //雇用日
$emp->setemSal($addemSal); //給料
$emp->setdeptId($addDeptId); //所属部門ID

$validationMsgs = []; //検証メッセージ挿入素材

// if (empty($addemName or $addemJob)) { //部門名の必須チェックなぜあるのか→//全角半角だけのデータがきても弾く流れが上記とこの行でやっている
//     $validationMsgs[] = "従業員名は必須です。";
// }
if (empty($addemName)) {
    $validationMsgs[] = "従業員名は必須";
}

if (empty($addemJob)) {
    $validationMsgs[] = "役職は必須";
}


try {
    //-------------------------------[DB処理]-------------------------//
    $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
    $empDAO = new EmpDAO($db);
    $deptDAO = new DeptDAO($db); //所属部門ID
    $notinIn = $deptDAO->notinIn($emp->getdeptId());

    $empDB = $empDAO->findByEmNo($emp->getemNo());

    if (!empty($empDB)) {
        $validationMsgs[] = "その部門番号はすでに使われています。別のものを指定してください。";
    }
    if (empty($validationMsgs)) {
        $epId = $empDAO->insert($emp);
        if ($epId === -1) {
            $_SESSION["errorMsg"] = "情報登録に失敗しました。もう一度はじめからやり直してください。";
        }
    } else { //バリデーションに文字が入ってたら
        $_SESSION["emp"] = serialize($emp); //入力した値が入ってくる//serializeはインスタンスかした値を代入可能関数
        $_SESSION["validationMsgs"] = $validationMsgs; //未入力でしたのメッセージを表示しる
    }
} catch (PDOException $ex) {
    var_dump($ex);
    $_SESSION["errorMsg"] = "DB接続に失敗しました。";
} finally {
    $db = null;
}
if (isset($_SESSION["errorMsg"])) {
    header("Location: /ph34/scottadmindao/public/error.php");
    exit;
} elseif (!empty($validationMsgs)) {
    header("Location: /ph34/scottadmindao/public/emp/goEmpAdd.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="author" content="Shinzo SAITO">
  <title>従業員情報追加完了 | scottadmindao Sample</title>
  <link rel="stylesheet" href="/ph34/scottadmindao/public/css/main.css" type="text/css">
</head>

<body>
  <h1>従業員情報追加完了</h1>
  <nav id="breadcrumbs">
    <ul>
      <li><a href="/ph34/scottadmindao/">TOP</a></li>
      <li><a href="/ph34/scottadmindao/public/emp/showEmpList.php">管理情報リスト</a></li>
      <li>従業員情報追加</li>
      <li>従業員情報追加完了</li>
    </ul>
  </nav>
  <section>
    <p>
      以下の従業員情報を登録しました。
    </p>
    <dl>
      <dt>ID(自動生成)</dt>
      <dd><?= $epId ?></dd>
      <dt>従業員番号</dt>
      <dd><?= $emp->getemNo() ?></dd>
      <dt>従業員名</dt>
      <dd><?= $emp->getemName() ?></dd>
      <dt>役職</dt>
      <dd><?= $emp->getemJob() ?></dd>
      <dt>上司番号</dt>
      <dd><?= $emp->getemMgr() ?></dd>
      <dt>雇用日</dt>
      <dd><?= $emp->getemHiredate() ?></dd>
      <dt>給料</dt>
      <dd><?= $emp->getemSal() ?></dd>
      <dt>所属部門ID</dt>
      <dd><?= $notinIn[0] ?></dd>
    </dl>
    <p>
      部門リストに<a href="/ph34/scottadmindao/public/emp/showEmpList.php">戻る</a>
    </p>
  </section>
</body>

</html>