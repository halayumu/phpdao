<?php

/**
 * PH34 サンプル3 マスタテーブル管理 Src07/12
 * 部門情報登録画面表示。
 *
 * @author Shinzo SAITO
 *
 * ファイル名=prepareEmpEdit.php
 * フォルダ=/ph34/scottadmindao/public/emp/
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/Conf.php"); //サーバー変数
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Emp.php"); //エンティティ
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/EmpDAO.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/entity/Dept.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/ph34/scottadmindao/classes/dao/DeptDAO.php");

$emp = new Emp();
$validationMsgs = null;

if (isset($_POST["editEmpId"])) {
    $editEmpId = $_POST["editEmpId"];

    try {
        //--------------------[DB接続]--------------------//
        //----[接続処理]----//
        $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
        $empDAO = new EmpDAO($db);
        $deptDAO = new DeptDAO($db);
        //----[ユーザ検索]----//
        $emp = $empDAO->findByPK($editEmpId);
        // $emp->getId();id取得

        //--------------------[上司番号処理]--------------------//
        //----[上司番号取得処理]----//
        $emMgr = $empDAO->sqlMgrNo($emp->getId()); //上司番号単体取得

        //----[従業員番号,名前取得処理]----//
        $MgrName = $empDAO->MgrName(); //名前を全件取得
        $emNoAll = $empDAO->emNoAll(); //従業員番号全件取得


        //--------------------[雇用日処理]--------------------//
        //----[雇用日抽出]----//
        $getemHiredate = $emp->getemHiredate();

        //----[雇用日分解]----//
        $getemHiredate = explode('-', $getemHiredate);
        $year = $getemHiredate[0]; //年
        $month = $getemHiredate[1]; //月
        $day = $getemHiredate[2]; //日

        //----[年の年下取得処理]----//
        $sqlmin = $empDAO->sqlmin();
        $sqlmin = explode('-', $sqlmin);

        //----[年,月,日ループ素材処理]----//
        for ($i = 2021; $i >= $sqlmin[0]; $i--) { //年
            $yearAll[] = $i;
        }

        for ($i = 01; $i <= 12; $i++) { //月
            $monthAll[] = $i;
        }

        for ($i = 01; $i <= 31; $i++) { //日
            $dayAll[] = $i;
        }

        //--------------------[所属部門ID処理]------------------//
        //----[dept_id取得]----//
        $dpmin = $deptDAO->dpmin(); //最大値
        $dpmax = $deptDAO->dpmax(); //最小値

        //----[所属部門ID素材]----//
        for ($i = $dpmin; $i <= $dpmax; $i++) {
            $dpIdAll[] = $i; //idの全件
        }

        //----[現id検索]----//
        $getdeptId = $emp->getdeptId(); //現在のid

        //----[depsテーブルidとname]----//
        $dpNoNameAll = $deptDAO->dpNoName(); ////番号と名前全件取得

        //--------------------[各エラー処理]--------------------//
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
} else {
    if (isset($_SESSION["emp"])) {
        $emp = $_SESSION["emp"];
        $emp = unserialize($emp);
        unset($_SESSION["emp"]);

        try {
            //--------------------[DB接続]--------------------//
            //----[接続処理]----//
            $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
            $empDAO = new EmpDAO($db); //EmpDAOインスタンス
            $deptDAO = new DeptDAO($db); //DeptDAOインスタンス

            //--------------------[上司番号バリデーション処理]--------------------//
            //----[上司番号取得処理]----//
            $emMgr = $emp->getemMgr(); //上司番号取得

            //----[従業員番号,名前取得処理]----//
            $MgrName = $empDAO->MgrName(); //名前を全件取得
            $emNoAll = $empDAO->emNoAll(); //従業員番号全件取得


            //--------------------[雇用日バリデーション処理]--------------------//
            //----[年月日分解処理]----//
            $getdato = $emp->getemHiredate(); //雇用日取得
            $Hiredate = explode("-",  $getdato); //分解
            $yeraint = intval($Hiredate[0]); //年
            $monthint = intval($Hiredate[1]); //月
            $dayint = intval($Hiredate[2]); //日

            //----[最小の年,月,日取得]----//
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

            //----[所属部門ID単体取得]----//
            $getdeptId = $emp->getdeptId(); //所属部門id取得
            $dpsqlId = $deptDAO->dpsqlId($getdeptId); //depsかた単体データの部門番号と部門名取得

            //----[id最小値最大値取得処理]----//
            $dpMin = $deptDAO->dpmin(); //id最小値
            $dpMax = $deptDAO->dpmax(); //id最大値

            //----[全id取得]----//
            for ($i = $dpMin; $i <= $dpMax; $i++) { //idの全件ループ
                $dpId[] =  $i;
            }
        } catch (PDOException $ex) {
            var_dump($ex);
            $_SESSION["errorMsg"] = "DB接続に失敗しました。";
        } finally {
            $db = null;
        }
    }
    // $db = new PDO(Conf::DB_DNS, Conf::DB_USERNAME, Conf::DB_PASSWORD);
    // $empDAO = new EmpDAO($db);
    // $empDAO->sqlmin();

    if (isset($_SESSION["validationMsgs"])) {
        $validationMsgs = $_SESSION["validationMsgs"];
        unset($_SESSION["validationMsgs"]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ph34/scottadmindao/public/css/main.css" type="text/css">

    <title>Document</title>
</head>

</html>

<body>
    <h1>従業員情報編集</h1>
    <nav id="breadcrumbs">
        <ul>
            <li><a href="/ph34/scottadmindao/public/">TOP</a></li>
            <li><a href="/ph34/scottadmindao/public/emp/showEmpList.php">管理情報リスト</a></li>
            <li>従業員情報編集</li>
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
    <p>
        情報を入力し、更新ボタンをクリックしてください。
    </p>
    <form action="/ph34/scottadmindao/public/emp/empEdit.php" method="post" class="box">
        従業員ID:&nbsp;<?= $emp->getId() ?><br>
        <input type="hidden" name="editEmId" value="<?= $emp->getId() ?>">

        <label for="editEmNo">
            従業員番号&nbsp;<span class="required">必須</span>
            <input type="number" min="1000" max="9999" step="0" id="editEmNo" name="editEmNo" value="<?= $emp->getemNo() ?>" required>
        </label><br>

        <label for="editEmName">
            従業員名&nbsp;<span class="required">必須</span>
            <input type="text" id="editEmName" name="editEmName" value="<?= $emp->getemName() ?>" required>
        </label><br>

        <label for="editEmJob">
            役職&nbsp;<span class="required">必須</span>
            <input type="text" id="editEmJob" name="editEmJob" value="<?= $emp->getemJob() ?>" required>
        </label><br>
        <?php
        if (empty($validationMsgs)) {
        ?>
            <!-- [上司番号チェック処理] -->
            上司番号<select name="editEmMgr" id="editEmMgr">
                <option value="0">0上司番号</option>
                <?php
                for ($i = 0; $i < count($emNoAll); $i++) {
                    if ($emNoAll[$i] == $emMgr) {
                ?>
                        <option value="<?= $emNoAll[$i] . ":" . $MgrName[$i] ?> " selected><?= $emNoAll[$i] . ":" . $MgrName[$i] ?></option>
                    <?php } else { ?>
                        <option value="<?= $emNoAll[$i] . ":" . $MgrName[$i] ?>"><?= $emNoAll[$i] . ":" . $MgrName[$i] ?></option>
                <?php }
                } ?>
            </select><br>
            <!--------------------[雇用日ドロップダウン処理]-------------------->
            雇用日<select name="Year" id="Year">
                <?php
                for ($i = 0; $i < count($yearAll); $i++) {
                    if ($yearAll[$i] == $year) {
                ?>
                        <option value="<?= $yearAll[$i] ?> " selected><?= $yearAll[$i] ?></option>
                    <?php } else { ?>
                        <option value="<?= $yearAll[$i] ?>"><?= $yearAll[$i] ?></option>
                <?php }
                } ?>
            </select>年

            <select name="Month" id="Month">
                <?php
                for ($i = 0; $i < count($monthAll); $i++) {
                    if ($monthAll[$i] == $month) {
                ?>
                        <option value="<?= $monthAll[$i] ?> " selected><?= $monthAll[$i] ?></option>
                    <?php } else { ?>
                        <option value="<?= $monthAll[$i] ?>"><?= $monthAll[$i] ?></option>
                <?php }
                } ?>
            </select> 月

            <select name="Day" id="Day">
                <?php
                for ($i = 0; $i < count($dayAll); $i++) {
                    if ($dayAll[$i] == $day) {
                ?>
                        <option value="<?= $dayAll[$i] ?> " selected><?= $dayAll[$i] ?></option>
                    <?php } else { ?>
                        <option value="<?= $dayAll[$i] ?>"><?= $dayAll[$i] ?></option>
                <?php }
                } ?>
            </select>日<br>
            <!--[所属部門ID]-->
            所属部門ID<select name="editDeptId" id="editDeptId">
                <?php
                for ($i = 0; $i < count($dpNoNameAll); $i++) {
                    if ($dpIdAll[$i] == $getdeptId) {
                ?>
                        <option value="<?= $dpIdAll[$i] ?> " selected><?= $dpNoNameAll[$i] ?></option>
                    <?php } else { ?>
                        <option value="<?= $dpIdAll[$i] ?>"><?= $dpNoNameAll[$i] ?></option>
                <?php }
                } ?>
            </select><br>

            <label for="editEmSal">
                給料&nbsp;<span class="required">必須</span>
                <input type="number" min="0" max="" step="0" id="editEmSal" name="editEmSal" value="<?= $emp->getemSal() ?>" required>
            </label>
        <?php } else { ?>
            <!--[上司番号チェック処理]-->
            上司番号<select name="editEmMgr" id="editEmMgr">
                <option value="0">0上司番号</option>
                <?php
                for ($i = 0; $i < count($emNoAll); $i++) {
                    if ($emNoAll[$i] == $emMgr) {
                ?>
                        <option value="<?= $emNoAll[$i] . ":" . $MgrName[$i] ?> " selected><?= $emNoAll[$i] . ":" . $MgrName[$i] ?></option>
                    <?php } else { ?>
                        <option value="<?= $emNoAll[$i] . ":" . $MgrName[$i] ?>"><?= $emNoAll[$i] . ":" . $MgrName[$i] ?></option>
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
            所属部門ID<select name="editDeptId" id="editDeptId">
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


            <label for="editEmSal">
                給料&nbsp;<span class="required">必須</span>
                <input type="number" min="0" max="" step="0" id="editEmSal" name="editEmSal" value="<?= $emp->getemSal() ?>" required>
            </label><br>
        <?php } ?>

        <button type="submit">更新</button>
    </form>
</body>

</html>