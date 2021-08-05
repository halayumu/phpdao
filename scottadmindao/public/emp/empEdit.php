<?php

/**
 * PH34 サンプル3 マスタテーブル管理 Src07/12
 * 部門情報登録画面表示。
 *
 * @author Shinzo SAITO
 *
 * ファイル名=EmpEdit.php
 * フォルダ=/ph34/scottadmindao/public/Emp/
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/Conf.php"); //サーバー変数
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Emp.php"); //エンティティ
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/EmpDAO.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Dept.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/DeptDAO.php");
//---------------------------[ドロップダウン値加工処理]-----------------------//
//----[上司番号加工]----//
$editEmMgr = $_POST["editEmMgr"]; //上司番号取得
$editEmMgrEx = explode(':', $editEmMgr); //上司番号と名前の間の記号を省く
$editEmMgr = intval($editEmMgrEx[0]); //数値に変換ß

//----[雇用日加工]----//
$_POST["Year"]; //年
$_POST["Month"]; //月
$_POST["Day"]; //月
$date = array($_POST["Year"], $_POST["Month"], $_POST["Day"]);
$editEmHiredate  = implode("-", $date);
$editEmHiredate = preg_replace("/( |　)/", "", $editEmHiredate);

//----[所属部門ID加工]----//
$editDeptId = intval($_POST["editDeptId"]); //文字列を整数値変換

//---------------------------[入力値取得処理]----------------------------//
$editEmId = $_POST["editEmId"]; //hidduのidを取得
$editEmNo = $_POST["editEmNo"]; //従業員番号
$editEmName = $_POST["editEmName"]; //従業員名
$editEmJob = $_POST["editEmJob"]; //役所
$editEmMgr = $editEmMgr; //上司番号
$editEmHiredate = $editEmHiredate; //雇用日
$editEmSal = $_POST["editEmSal"]; //給料
$editDeptId = $editDeptId; //所属部門ID

//----[入力前後空白削除処理]---//
$editEmName = str_replace("　", " ", $editEmName); //文字列変換関数
$editEmJob = str_replace("　", " ", $editEmJob); //文字列変換関数(全角が入ってると入らない様な処理をする)
$editEmName = trim($editEmName); //従業員名
$editEmJob = trim($editEmJob); //役所

//-----------------------[Empインスタンス保管処理]-------------------//
$emp = new Emp();
$emp->setId($editEmId);
$emp->setEmNo($editEmNo); //従業員番号
$emp->setEmName($editEmName); //従業員名
$emp->setEmJob($editEmJob); //役所
$emp->setEmMgr($editEmMgr); //上司番号
$emp->setEmHiredate($editEmHiredate); //雇用日
$emp->setEmSal($editEmSal); //給料
$emp->setdeptId($editDeptId); //所属部門ID

$validationMsgs = [];

if (empty($editEmName)) {
    $validationMsgs[] = "従業員名の入力は必須です。";
}

if (empty($editEmJob)) {
    $validationMsgs[] = "役職は必須";
}

try {
    //-----------------------[SQL処理]--------------------------//
    $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
    $empDAO = new EmpDAO($db);
    $deptDAO = new DeptDAO($db); //所属部門ID
    $notinIn = $deptDAO->notinIn($emp->getdeptId());

    $empDB = $empDAO->findByEmNo($emp->getemNo());
    if (!empty($empDB) && $empDB->getId() != $editEmId) {
        $validationMsgs[] = "その部門番号はすでに使われています。別のものを指定してください。";
    }
    if (empty($validationMsgs)) {
        $result = $empDAO->update($emp);
        if (!$result) {
            $_SESSION["errorMsg"] = "情報更新に失敗しました。もう一度はじめからやり直してください。";
        }
    } else {
        $_SESSION["emp"] = serialize($emp);
        $_SESSION["validationMsgs"] = $validationMsgs;
    }
} catch (PDOException $ex) {
    var_dump($ex);
    $_SESSION["errorMsg"] = "DB接続に失敗しました。";
} finally {
    $db = null;
}

if (isset($_SESSION["errorMsg"])) {
    // header("Location: /ph34/scottadmindao/public/error.php");
    // exit;
} elseif (!empty($validationMsgs)) {
    // $emp->setemMgr(setemMgr);
    //家に返ったら名前を取得する方法はセットで入れてあげればいい
    header("Location: /ph34/scottadmindao/public/emp/prepareEmpEdit.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Shinzo SAITO">
    <title>従業員情報編集完了 | scottadmindao Sample</title>
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
            <bt>ID</bt>
            <dd><?= $emp->getid() ?></dd>
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