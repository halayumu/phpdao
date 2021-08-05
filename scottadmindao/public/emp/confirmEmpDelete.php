<?php

/**
 * PH34 サンプル3 マスタテーブル管理 Src07/12
 * 部門情報登録画面表示。
 *
 * @author Shinzo SAITO
 *
 * ファイル名=confirmEmpDelete.php
 * フォルダ=/ph34/scottadmin/public/emp/
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmin/classes/Conf.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmin/classes/entity/Emp.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/EmpDAO.php");

$deleteEmpId = $_POST["deleteEmpId"];

try {
    $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
    $empDAO = new EmpDAO($db);

    $emp = $empDAO->findByPK($deleteEmpId);
    if (empty($emp)) {
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
    <title>従業員情報削除 | ScottAdmin Sample</title>
    <link rel="stylesheet" href="/ph34/scottadmin/public/css/main.css" type="text/css">
</head>

<body>
    <h1>従業員情報削除</h1>
    <nav id="breadcrumbs">
        <ul>
            <li><a href="/ph34/scottadmin/public/">TOP</a></li>
            <li><a href="/ph34/scottadmin/public/emp/showEmpList.php">管理情報リスト</a></li>
            <li>従業員情報削除確認</li>
        </ul>
    </nav>
    <section>
        <p>
            以下の部門情報を削除します。<br>
            よろしければ、削除ボタンをクリックしてください。
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
            <dd><?= $emp->getdeptId() ?></dd>
        </dl>
        <form action="/ph34/scottadmindao/public/emp/empDelete.php" method="post">
            <input type="hidden" id="deleteEmpId" name="deleteEmpId" value="<?= $emp->getId() ?>">
            <button type="submit">削除</button>
        </form>
    </section>
</body>

</html>