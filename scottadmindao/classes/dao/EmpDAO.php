<?php

/**
 * PH34 課題3 マスタテーブル管理DAO版
 *
 * @author Ayumu Ishida
 *
 * ファイル名=EmpDAO.php
 * フォルダ=/ph34/scottadmindao/classes/dao/
 */
class EmpDAO
{
    /**
     * @var PDO DB接続オブジェクト
     */
    private $db;

    /**
     * コンストラクタ
     *
     * @param PDO $db DB接続オブジェクト
     */
    public function __construct(PDO $db)
    {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db = $db;
    }

    /**
     * 全従業員情報検索
     * showEmplist.php
     * 
     * @return array 全従業員情報が格納された連想配列。キーは従業員番号、値はDeptエンティティオブジェクト。
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM emps ORDER BY em_no";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();
        $empList = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
            $emNo = $row["em_no"];
            $emName = $row["em_name"];
            $emJob = $row["em_job"];
            $emMgr = $row["em_mgr"];
            $emHiredate = $row["em_hiredate"];
            $emSal = $row["em_sal"];
            $deptId = $row["dept_id"];

            $emp = new Emp();
            $emp->setId($id);
            $emp->setemNo($emNo);
            $emp->setemName($emName);
            $emp->setemJob($emJob);
            $emp->setemMgr($emMgr);
            $emp->setemHiredate($emHiredate);
            $emp->setemSal($emSal);
            $emp->setdeptId($deptId);
            $empList[$id] = $emp;
        }
        return $empList;
    }

    /**
     * 部門番号による検索。
     *
     * @param integer $dpNo 主キーであるid。
     * @return Emp 該当するDeptオブジェクト。ただし、該当データがない場合はnull。
     */
    public function findByEmNo(int $emNo): ?Emp
    {
        //----[SQL文作成]----//
        $sql = "SELECT * FROM  emps WHERE em_no = :em_no";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":em_no", $emNo, PDO::PARAM_INT);
        $result = $stmt->execute();

        $emp = null;

        if ($result && $row = $stmt->fetch()) {
            $id = $row["id"];
            $emNo = $row["em_no"];
            $emName = $row["em_name"];
            $emJob = $row["em_job"];
            $emMgr = $row["em_mgr"];
            $emIredate = $row["em_hiredate"];
            $emSal = $row["em_sal"];
            $deptId = $row["dept_id"];

            $emp = new Emp();
            $emp->setId($id); //id
            $emp->setemNo($emNo); //従業員番号
            $emp->setemName($emName); //従業員名
            $emp->setemJob($emJob); //役所
            $emp->setemMgr($emMgr); //上司番号
            $emp->setemHiredate($emIredate); //雇用日
            $emp->setemSal($emSal); //給料
            $emp->setdeptId($deptId); //所属部門ID
        }
        return $emp;
    }

    /**
     * 従業員情報登録。
     *
     * @param Emp $emp 登録情報が格納されたEmpオブジェクト。
     * @return integer 登録情報の連番主キーの値。登録に失敗した場合は-1。
     */
    public function insert(Emp $emp): int
    {
        $sqlInsert = "INSERT INTO emps (em_no, em_name, em_job, em_mgr, em_hiredate, em_sal, dept_id)
        VALUES (:em_no, :em_name, :em_job, :em_mgr, :em_hiredate, :em_sal, :dept_id)";
        $stmt = $this->db->prepare($sqlInsert);
        $stmt->bindValue(":em_no", $emp->getemNo(), PDO::PARAM_INT);
        $stmt->bindValue(":em_name", $emp->getemName(), PDO::PARAM_STR);
        $stmt->bindValue(":em_job", $emp->getemJob(), PDO::PARAM_STR);
        $stmt->bindValue("em_mgr", $emp->getemMgr(), PDO::PARAM_INT);
        $stmt->bindValue("em_hiredate", $emp->getemHiredate(), PDO::PARAM_STR);
        $stmt->bindValue("em_sal", $emp->getemSal(), PDO::PARAM_INT);
        $stmt->bindValue("dept_id", $emp->getdeptId(), PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $emId = $this->db->lastInsertId();
        } else {
            $emId = -1;
        }
        return  $emId;
    }

    /**
     * 主キーidによる検索。
     *
     * @param integer $id 主キーであるid。
     * @return Emp 該当するEmpオブジェクト。ただし、該当データがない場合はnull。
     */
    public function findByPK(int $id): ?Emp
    {
        $sql = "SELECT * FROM emps WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        $emp = null;

        if ($result && $row = $stmt->fetch()) {
            $id = $row["id"];
            $emNo = $row["em_no"];
            $emName = $row["em_name"];
            $emJob = $row["em_job"];
            $emMgr = $row["em_mgr"];
            $emIredate = $row["em_hiredate"];
            $emSal = $row["em_sal"];
            $deptId = $row["dept_id"];

            $emp = new Emp();
            $emp->setId($id); //id
            $emp->setemNo($emNo); //従業員番号
            $emp->setemName($emName); //従業員名
            $emp->setemJob($emJob); //役所
            $emp->setemMgr($emMgr); //上司番号
            $emp->setemHiredate($emIredate); //雇用日
            $emp->setemSal($emSal); //給料
            $emp->setdeptId($deptId); //所属部門ID
        }
        return $emp;
    }

    /**
     * 従業員情報更新。更新対象は1レコードのみ。
     *
     * @param Emp $Emp 更新情報が格納されたDeptオブジェクト。主キーがこのオブジェクトのidの値のレコードを更新する。
     * @return boolean 登録が成功したかどうかを表す値。
     */
    public function update(Emp $emp): bool
    {
        $sqlUpdate = "UPDATE emps SET em_no = :em_no, em_name = :em_name, em_job = :em_job
        , em_mgr = :em_mgr , em_hiredate = :em_hiredate, em_sal = :em_sal, dept_id = :dept_id
        WHERE id = :id";

        $stmt = $this->db->prepare($sqlUpdate);
        $stmt->bindValue(":em_no", $emp->getemNo(), PDO::PARAM_INT);
        $stmt->bindValue(":em_name", $emp->getemName(), PDO::PARAM_STR);
        $stmt->bindValue(":em_job", $emp->getemJob(), PDO::PARAM_STR);
        $stmt->bindValue("em_mgr", $emp->getemMgr(), PDO::PARAM_INT);
        $stmt->bindValue("em_hiredate", $emp->getemHiredate(), PDO::PARAM_STR);
        $stmt->bindValue("em_sal", $emp->getemSal(), PDO::PARAM_INT);
        $stmt->bindValue("dept_id", $emp->getdeptId(), PDO::PARAM_INT);
        $stmt->bindValue("id", $emp->getId(), PDO::PARAM_INT);
        $result = $stmt->execute();
        return $result;
    }
    /**
     * 従業員削除。削除対象は1レコードのみ。
     *
     * @param integer $id 削除対象の主キー。
     * @return boolean 登録が成功したかどうかを表す値。
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM emps WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        return $result;
    }


    /**
     * 上司番号
     */
    public function emmgrSQL()
    {
        $sql = "SELECT em_mgr FROM emps";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emMgr = $row["em_mgr"];

            $emmgrSQL[] = $emMgr;
        }
        return $emmgrSQL;
    }


    /**
     * 上司番号と名前取得
     */
    public function emnameSQL()
    {
        $sql = "SELECT em_no,em_name FROM emps";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emName = $row["em_name"];
            $emMgr = $row["em_no"];

            $empList[] =  $emMgr . ":" . $emName;
        }
        return $empList;
    }


    /**
     * 雇用日を取得
     */
    public function sqlmin()
    {
        $sql = "SELECT MIN(em_hiredate) FROM emps"; //構文作成
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sqlmin = $row["MIN(em_hiredate)"];
        }
        return $sqlmin;
    }

    /**
     * リロード時上司番号から名前検索
     */
    public function sqlMgr(int $getemMgr)
    {
        $sql = "SELECT em_name,em_no FROM emps WHERE em_no = :em_no";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":em_no", $getemMgr, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emNo = $row["em_no"];
            $emName = $row["em_name"];
            $emNolist[] =  $emNo . ":" . $emName;
        }
        return $emNolist;
    }

    //------------------------------------------[編集処理まとめ]--------------------------------------//
    public function notin()
    {
        $sql = "SELECT em_name,em_no FROM emps";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emMgr = $row["em_no"];
            $emName = $row["em_name"];

            $empList[] =  $emMgr . ":" . $emName;
        }
        return $empList;
    }

    //----[追加上司番号検索処理]----//
    public function NoAndName(int $getemMgr)
    {
        $sql = "SELECT em_name,em_no FROM emps WHERE em_no = :em_no";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":em_no", $getemMgr, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emNo = $row["em_no"];
            $emName = $row["em_name"];
            $emNolist[] =  $emNo . ":" . $emName;
        }
        return $emNolist;
    }
    //----[編集上司番号]----//
    public function sqlMgrNo(int $id)
    {
        $sql = "SELECT em_mgr FROM emps WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emMgr = $row["em_mgr"];
        }
        return $emMgr;
    }
    //----[バリデーション編集上司番号]----//
    public function MgrName()
    {
        $sql = "SELECT em_name FROM emps";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emname[] = $row["em_name"];
        }
        return $emname;
    }
    //----[バリデーション編集上司番号]----//
    public function emNoAll()
    {
        $sql = "SELECT em_no FROM emps";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emno[] = $row["em_no"];
        }
        return $emno;
    }


    public function emMgrAllNo() //全件上司番号取得
    {
        $sql = "SELECT em_mgr FROM emps WHERE em_mgr NOT IN(:em_mgr)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":em_mgr", 0, PDO::PARAM_INT);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emMgrAll[] = $row["em_mgr"];
        }
        return $emMgrAll;
    }

    public function emMgrNameAll($emMgrAllNo) //上司番号から上司名検索全件取得
    {
        for ($i = 0; $i < count($emMgrAllNo); $i++) {
            $sql = "SELECT em_name,em_no FROM emps WHERE em_no = :em_no";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":em_no", $emMgr, PDO::PARAM_INT);
            $result = $stmt->execute();
        }
    }

    /**
     * 弾かれた時の値以外の値を全検索
     */
    public function NoAndNameAll()
    {
        $sql = "SELECT em_no,em_name FROM emps";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emName = $row["em_name"];
            $emMgr = $row["em_no"];

            $empList[] =  $emMgr . ":" . $emName;
        }
        return $empList;
    }
}
//返ってから所属部門idから登録インサートできてるか確認する