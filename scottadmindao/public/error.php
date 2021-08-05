<?php

/**
 * PH34 サンプル4 マスタテーブル管理DAO版 Src02/13
 * エラー画面
 *
 * @author Shinzo SAITO
 *
 * ファイル名=error.php
 * フォルダ=/ph34/scottadmindao/public/
 */

$errorMsg = "もう一度始めから操作をお願いします。";
if (isset($_SESSION["errorMsg"])) {
    $errorMsg = $_SESSION["errorMsg"];
}
unset($_SESSION["errorMsg"]);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Shinzo SAITO">
    <title>Error | ScottAdminDAO Sample</title>
</head>

<body>
    <h1>Error</h1>
    <section>
        <h2>申し訳ございません。障害が発生しました。</h2>
        <p>
            以下のメッセージご確認ください。<br>
            <?= $errorMsg ?>
        </p>
    </section>
    <p><a href="/ph34/scottadmindao/public/">TOPへ戻る</a></p>
</body>

</html>