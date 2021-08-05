<?php

/**
 * PH34 サンプル4 マスタテーブル管理DAO版 Src11/13
 * 部門情報編集。
 *
 * @author Shinzo SAITO
 *
 * ファイル名=deptEdit.php
 * フォルダ=/ph34/scottadmindao/public/dept/
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/Conf.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Dept.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/DeptDAO.php");

$editDpId = $_POST["editDpId"];
$editDpNo = $_POST["editDpNo"];
$editDpName = $_POST["editDpName"];
$editDpLoc = $_POST["editDpLoc"];
$editDpName = str_replace("　", " ", $editDpName);
$editDpLoc = str_replace("　", " ", $editDpLoc);
$editDpName = trim($editDpName);
$editDpLoc = trim($editDpLoc);

$dept = new Dept();
$dept->setId($editDpId);
$dept->setDpNo($editDpNo);
$dept->setDpName($editDpName);
$dept->setDpLoc($editDpLoc);

$validationMsgs = [];

if (empty($editDpName)) {
    $validationMsgs[] = "部門名の入力は必須です。";
}

try {
    $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
    $deptDAO = new DeptDAO($db);
    $deptDB = $deptDAO->findByDpNo($dept->getDpNo());
    if (!empty($deptDB) && $deptDB->getId() != $editDpId) {
        $validationMsgs[] = "その部門番号はすでに使われています。別のものを指定してください。";
    }
    if (empty($validationMsgs)) {
        $result = $deptDAO->update($dept);
        if (!$result) {
            $_SESSION["errorMsg"] = "情報更新に失敗しました。もう一度はじめからやり直してください。";
        }
    } else {
        $_SESSION["dept"] = serialize($dept);
        $_SESSION["validationMsgs"] = $validationMsgs;
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
    header("Location: /ph34/scottadmindao/public/dept/prepareDeptEdit.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Shinzo SAITO">
    <title>部門情報編集完了 | ScottAdminDAO Sample</title>
    <link rel="stylesheet" href="/ph34/scottadmindao/public/css/main.css" type="text/css">
</head>

<body>
    <h1>部門情報編集完了</h1>
    <nav id="breadcrumbs">
        <ul>
            <li><a href="/ph34/scottadmindao/">TOP</a></li>
            <li><a href="/ph34/scottadmindao/public/dept/showDeptList.php">部門リスト</a></li>
            <li>部門情報編集</li>
            <li>部門情報編集完了</li>
        </ul>
    </nav>
    <section>
        <p>
            以下の部門情報を更新しました。
        </p>
        <dl>
            <dt>ID</dt>
            <dd><?= $dept->getId() ?></dd>
            <dt>部門番号</dt>
            <dd><?= $dept->getDpNo() ?></dd>
            <dt>部門名</dt>
            <dd><?= $dept->getDpName() ?></dd>
            <dt>所在地</dt>
            <dd><?= $dept->getDpLoc() ?></dd>
        </dl>
        <p>
            部門リストに<a href="/ph34/scottadmindao/public/dept/showDeptList.php">戻る</a>
        </p>
    </section>
</body>

</html>