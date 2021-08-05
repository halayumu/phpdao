<?php

/**
 * PH34 課題2 マスタテーブル管理  *
 * @author Ayumu ISHIDA
 *
 * ファイル名=goEmpAdd.php
 * フォルダ=/ph34/scottadmin/public/emp/ 
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/Conf.php"); //サーバー変数
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Emp.php"); //エンティティ
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/EmpDAO.php"); //エンティティ
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Dept.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/DeptDAO.php");

$emp = new Emp(); //入力データが入る
if (isset($_SESSION["emp"])) {
    $emp = $_SESSION["emp"];
    $emp = unserialize($emp); //下記とセットで使う
    unset($_SESSION["emp"]); //セッションを消してる

    $getdeptId = $emp->getdeptId();
    $getdato = $emp->getemHiredate();

    //--------------------[返ってきた上司番号]--------------------//
    //----[返ってきた上司番号取得]----//
    $getemMgr = $emp->getemMgr();

    //--------------------[雇用日処理1]--------------------//
    $getdato = $emp->getemHiredate();
    $Hiredate = explode("-",  $getdato); //分解した年,月,日が代入//年月日を分解
    $yeraint = intval($Hiredate[0]); //年
    $monthint = intval($Hiredate[1]); //月
    $dayint = intval($Hiredate[2]); //日


}

$validationMsgs = null;
if (isset($_SESSION["validationMsgs"])) {
    $validationMsgs = $_SESSION["validationMsgs"];
    unset($_SESSION["validationMsgs"]); //セッションの値を消す
}

try {
    $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
    $empDAO = new EmpDAO($db);
    $deptDAO = new DeptDAO($db);

    if (!empty($getemMgr)) { //セッションから所属IDあるか判定
        //----[上司番号と名前抽出]----//
        $NoAndName = $empDAO->NoAndName($getemMgr); //em_noとem_name単体データ
        $NoAndNameAll = $empDAO->NoAndNameAll(); //em_noとem_name全件取得
    }

    //----[上司番号取得]----//
    $emnameidSQL = $empDAO->emnameSQL(); //上司番号と名前取得

    //--------------------[雇用日処理2]--------------------//
    //----[初期値の最小年取得]----//
    $sqlmin = $empDAO->sqlmin(); //sql実行結果取得
    $explode = explode("-", $sqlmin); //分解した年,月,日が代入//年月日を分解

    //----[年のドロップダウン]----//
    for ($i = 2021; $i >= $explode[0]; $i--) {
        $yera[] = $i;
    }
    //----[月のドロップダウン]----//
    for ($i = 1; $i <= 12; $i++) {
        $month[] = $i;
    }
    //----[日のドロップダウン]----//
    for ($i = 1; $i <= 31; $i++) {
        $day[] = $i;
    }

    //--------------------[所属部門ID処理]------------------//
    //----[部門番号と部門名取得]----//
    $deptList = $deptDAO->sqlid(); //全件取得

    //----[id最小値最大値取得処理]----//
    $dpMin = $deptDAO->dpmin(); //id最小値
    $dpMax = $deptDAO->dpmax(); //id最大値

    //----[全id取得]----//
    for ($i = $dpMin; $i <= $dpMax; $i++) { //idの全件ループ
        $dpId[] =  $i;
    }

    //--------------------[バリデーション所属部門ID処理]------------------//
    if (!empty($getdeptId)) {
        $dpsqlId = $deptDAO->dpsqlId($getdeptId);
    }
} catch (PDOException $ex) {
    var_dump($ex);
    print("DB接続に失敗しました。");
} finally {
    $db = null;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Shinzo SAITO">
    <title>従業員情報追加 | scottadmin Sample</title>
    <link rel="stylesheet" href="/ph34/scottadmindao/public/css/main.css" type="text/css">
</head>

<body>
    <h1>従業員情報追加</h1>
    <nav id="breadcrumbs">
        <ul>
            <li><a href="/ph34/scottadmindao/public/">TOP</a></li>
            <li><a href="/ph34/scottadmindao/public/emp/showEmpList.php">管理情報リスト</a></li>
            <li>従業員情報追加</li>
        </ul>
    </nav>
    <?php
    if (!is_null($validationMsgs)) {
    ?>
        <section id="errorMsg">
            <p>以下のメッセージをご確認ください。</p>
            <ul>
                <?php
                foreach ($validationMsgs as $msg) {
                ?>
                    <li><?= $msg ?></li>
                <?php
                }
                ?>
            </ul>
        </section>
    <?php
    }
    ?>
    <section>
        <p>
            情報を入力し、登録ボタンをクリックしてください。
        </p>
        <form action="/ph34/scottadmindao/public/emp/empAdd.php" method="post" class="box">
            <label for="addEmNo">
                従業員番号&nbsp;<span class="required">必須</span>
                <input type="number" min="1000" max="9999" step="0" id="addEmNo" name="addEmNo" value="<?= $emp->getemNo() ?>" required>
            </label><br>

            <label for="addEmName">
                従業員名&nbsp;<span class="required">必須</span>
                <input type="text" id="addEmName" name="addEmName" value="<?= $emp->getemName() ?>" required>
            </label><br>

            <label for="addEmJob">
                役職&nbsp;<span class="required">必須</span>
                <input type="text" id="addEmJob" name="addEmJob" value="<?= $emp->getemJob() ?>" required>
            </label><br>
            <?php
            if (empty($getdato)) {
            ?>
                <!--上司番号と名前を抽出-->
                上司番号<select name="addEmMgr" id="addEmMgr" required>
                    <option value="">選択してください</option>
                    <option value="0">0上司番号</option>
                    <?php
                    //年
                    for ($i = 0; $i < count($emnameidSQL); $i++) {
                    ?>
                        <option value="<?= $emnameidSQL[$i] ?>"><?= $emnameidSQL[$i] ?></option>
                    <?php
                    }
                    ?>
                </select><br>

                <!--雇用日年をループで出す-->
                雇用日<select name="Year" id="Year" required>
                    <option value="">選択してください</option>
                    <?php
                    //年
                    for ($i = 2021; $explode[0] <= $i; $i--) {
                    ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php
                    }
                    ?>
                </select>年

                <select name="Month" id="Month" required>
                    <option value="">選択してください</option>
                    <?php
                    //月
                    for ($i = 1; $i <= 12; $i++) {
                    ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php
                    }
                    ?>
                </select>月

                <select name="Day" id="Day" required>
                    <option value="">選択してください</option>
                    <?php
                    //日
                    for ($i = 1; $i <= 31; $i++) {

                    ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php
                    }
                    ?>
                </select>日<br>

                所属部門ID<select name="addDeptId" id="addDeptId" required>
                    <option value="">選択してください</option>
                    <?php
                    //所属部門ID
                    for ($i = 0; $i < count($deptList); $i++) {

                    ?>
                        <option value="<?= $dpId[$i] ?>"><?= $deptList[$i] ?></option>
                    <?php
                    }
                    ?>
                </select><br>
            <?php
            } else {
            ?>
                <!--[上司番号チェック処理]-->
                上司番号<select name="addEmMgr" id="addEmMgr">
                    <?php
                    for ($i = 0; $i < count($NoAndNameAll); $i++) {
                        if ($NoAndNameAll[$i] == $NoAndName[0]) {
                    ?>
                            <option value="<?= $NoAndNameAll[$i] ?> " selected><?= $NoAndNameAll[$i] ?></option>
                        <?php } else { ?>
                            <option value="<?= $NoAndNameAll[$i] ?>"><?= $NoAndNameAll[$i] ?></option>
                    <?php }
                    } ?>
                </select><br>
                <!-----[年の処理]----->
                雇用日<select name="Year" id="Year">
                    <?php
                    for ($i = 0; $i < count($yera); $i++) {
                        if ($yera[$i] == $yeraint) {
                    ?>
                            <option value="<?= $yera[$i] ?> " selected><?= $yera[$i] ?></option>
                        <?php } else { ?>
                            <option value="<?= $yera[$i] ?>"><?= $yera[$i] ?></option>
                    <?php }
                    } ?>
                </select>年
                <!-----[月の処理]----->
                <select name="Month" id="Month">
                    <?php
                    for ($i = 0; $i < count($month); $i++) {
                        if ($month[$i] == $monthint) {
                    ?>
                            <option value="<?= $month[$i] ?> " selected><?= $month[$i] ?></option>
                        <?php } else { ?>
                            <option value="<?= $month[$i] ?>"><?= $month[$i] ?></option>
                    <?php }
                    } ?>
                </select>月
                <!-----[日の処理]----->
                <select name="Day" id="Day">
                    <?php
                    for ($i = 0; $i < count($day); $i++) {
                        if ($day[$i] == $dayint) {
                    ?>
                            <option value="<?= $day[$i] ?> " selected><?= $day[$i] ?></option>
                        <?php } else { ?>
                            <option value="<?= $day[$i] ?>"><?= $day[$i] ?></option>
                    <?php }
                    } ?>
                </select>日<br>
                <!-----[所属部門IDの処理]----->
                所属部門ID<select name="addDeptId" id="addDeptId">
                    <?php
                    for ($i = 0; $i < count($deptList); $i++) {
                        if ($deptList[$i] == $dpsqlId[0]) {
                    ?>
                            <option value="<?= $dpId[$i] ?> " selected><?= $deptList[$i] ?></option>
                        <?php } else { ?>
                            <option value="<?= $dpId[$i] ?>"><?= $deptList[$i] ?></option>
                    <?php }
                    } ?>
                </select><br>
            <?php
            }
            ?>
            <label for="addEmSal">
                給料&nbsp;<span class="required">必須</span>
                <input type="number" min="0" max="" step="0" id="addEmSal" name="addEmSal" value="<?= $emp->getemSal() ?>" required>
            </label><br>
            <button type="submit">登録</button>
        </form>
    </section>
</body>

</html>