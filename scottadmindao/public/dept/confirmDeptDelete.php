<?php

/**
 * PH34 サンプル4 マスタテーブル管理DAO版 Src12/13
 * 部門情報削除確認画面表示。
 *
 * @author Shinzo SAITO
 *
 * ファイル名=confirmDeptDelete.php
 * フォルダ=/ph34/scottadmindao/public/dept/
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/Conf.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Dept.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/DeptDAO.php");

$deleteDeptId = $_POST["deleteDeptId"];

try {
    $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
    $deptDAO = new DeptDAO($db);
    $dept = $deptDAO->findByPK($deleteDeptId);
    if (empty($dept)) {
        $_SESSION["errorMsg"] = "部門情報の取得に失敗しました。";
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
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Shinzo SAITO">
    <title>部門情報削除 | ScottAdminDAO Sample</title>
    <link rel="stylesheet" href="/ph34/scottadmindao/public/css/main.css" type="text/css">
</head>

<body>
    <h1>部門情報削除</h1>
    <nav id="breadcrumbs">
        <ul>
            <li><a href="/ph34/scottadmindao/public/">TOP</a></li>
            <li><a href="/ph34/scottadmindao/public/dept/showDeptList.php">部門リスト</a></li>
            <li>部門情報削除確認</li>
        </ul>
    </nav>
    <section>
        <p>
            以下の部門情報を削除します。<br>
            よろしければ、削除ボタンをクリックしてください。
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
        <form action="/ph34/scottadmindao/public/dept/deptDelete.php" method="post">
            <input type="hidden" id="deleteDeptId" name="deleteDeptId" value="<?= $dept->getId() ?>">
            <button type="submit">削除</button>
        </form>
    </section>
</body>

</html>