<?php

/**
 * PH34 サンプル4 マスタテーブル管理DAO版 Src06/13
 *
 * @author Shinzo SAITO
 *
 * ファイル名=Dept.php
 * フォルダ=/ph34/scottadmindao/classes/dao/
 */

/**
 * deptテーブルへのデータ操作クラス。
 */
class DeptDAO
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
     * 主キーidによる検索。
     *
     * @param integer $id 主キーであるid。
     * @return Dept 該当するDeptオブジェクト。ただし、該当データがない場合はnull。
     */
    public function findByPK(int $id): ?Dept
    {
        $sql = "SELECT * FROM depts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        $dept = null;
        if ($result && $row = $stmt->fetch()) {
            $idDb = $row["id"];
            $dpNo = $row["dp_no"];
            $dpName = $row["dp_name"];
            $dpLoc = $row["dp_loc"];

            $dept = new Dept();
            $dept->setId($idDb);
            $dept->setDpNo($dpNo);
            $dept->setDpName($dpName);
            $dept->setDpLoc($dpLoc);
        }
        return $dept;
    }

    /**
     * 部門番号による検索。
     *
     * @param integer $dpNo 主キーであるid。
     * @return Dept 該当するDeptオブジェクト。ただし、該当データがない場合はnull。
     */
    public function findByDpNo(int $dpNo): ?Dept
    {
        $sql = "SELECT * FROM depts WHERE dp_no = :dp_no";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":dp_no", $dpNo, PDO::PARAM_INT);
        $result = $stmt->execute();
        $dept = null;
        if ($result && $row = $stmt->fetch()) {
            $id = $row["id"];
            $dpNoDB = $row["dp_no"];
            $dpName = $row["dp_name"];
            $dpLoc = $row["dp_loc"];

            $dept = new Dept();
            $dept->setId($id);
            $dept->setDpNo($dpNoDB);
            $dept->setDpName($dpName);
            $dept->setDpLoc($dpLoc);
        }
        return $dept;
    }

    /**
     * 全部門情報検索。
     *
     * @return array 全部門情報が格納された連想配列。キーは部門番号、値はDeptエンティティオブジェクト。
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM depts ORDER BY dp_no";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();
        $deptList = [];
        while ($row = $stmt->fetch()) {
            $id = $row["id"];
            $dpNo = $row["dp_no"];
            $dpName = $row["dp_name"];
            $dpLoc = $row["dp_loc"];

            $dept = new Dept();
            $dept->setId($id);
            $dept->setDpNo($dpNo);
            $dept->setDpName($dpName);
            $dept->setDpLoc($dpLoc);
            $deptList[$id] = $dept;
        }
        return $deptList;
    }

    /**
     * 部門情報登録。
     *
     * @param Dept $dept 登録情報が格納されたDeptオブジェクト。
     * @return integer 登録情報の連番主キーの値。登録に失敗した場合は-1。
     */
    public function insert(Dept $dept): int
    {
        $sqlInsert = "INSERT INTO depts (dp_no, dp_name, dp_loc) VALUES (:dp_no, :dp_name, :dp_loc)";
        $stmt = $this->db->prepare($sqlInsert);
        $stmt->bindValue(":dp_no", $dept->getDpNo(), PDO::PARAM_INT);
        $stmt->bindValue(":dp_name", $dept->getDpName(), PDO::PARAM_STR);
        $stmt->bindValue(":dp_loc", $dept->getDpLoc(), PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result) {
            $dpId = $this->db->lastInsertId();
        } else {
            $dpId = -1;
        }
        return  $dpId;
    }

    /**
     * 部門情報更新。更新対象は1レコードのみ。
     *
     * @param Dept $dept 更新情報が格納されたDeptオブジェクト。主キーがこのオブジェクトのidの値のレコードを更新する。
     * @return boolean 登録が成功したかどうかを表す値。
     */
    public function update(Dept $dept): bool
    {
        $sqlUpdate = "UPDATE depts SET dp_no = :dp_no, dp_name = :dp_name, dp_loc = :dp_loc WHERE id = :id";
        $stmt = $this->db->prepare($sqlUpdate);
        $stmt->bindValue(":dp_no", $dept->getDpNo(), PDO::PARAM_INT);
        $stmt->bindValue(":dp_name", $dept->getDpName(), PDO::PARAM_STR);
        $stmt->bindValue(":dp_loc", $dept->getDpLoc(), PDO::PARAM_STR);
        $stmt->bindValue(":id", $dept->getId(), PDO::PARAM_INT);
        $result = $stmt->execute();
        return $result;
    }

    /**
     * 部門情報削除。削除対象は1レコードのみ。
     *
     * @param integer $id 削除対象の主キー。
     * @return boolean 登録が成功したかどうかを表す値。
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM depts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        return $result;
    }

    //--------------------[empで使用]--------------------//
    public function sqlid()
    {
        $sql = "SELECT dp_no,dp_name FROM depts";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dpNo = $row["dp_no"];
            $dpName = $row["dp_name"];

            $dpList[] =  $dpNo . ":" . $dpName;
        }
        return $dpList;
    }

    public function dpsqlId($deptId) //送られてきたidから部門idと役職名を取得
    {
        $sql = "SELECT dp_no,dp_name FROM depts WHERE id= :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $deptId, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dpNo = $row["dp_no"];
            $dpName = $row["dp_name"];

            $dpList[] =  $dpNo . ":" . $dpName;
        }
        return $dpList;
    }

    public function dpid($depid) //送られてきたid検索する
    {
        $sql = "SELECT id FROM depts WHERE id= :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $depid, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
            return $id;
        }
    }
    public function dpnotNOTId($depid) //送られ的たid以外のidを全件取得
    {
        $sql = "SELECT id FROM depts WHERE id NOT IN(:id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $depid, PDO::PARAM_INT);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dpId = $row["id"];

            $dpnotId[] =  $dpId;
        }
        return $dpnotId;
    }


    public function notinIn($deptId) //送られてきたidの部門idと所属部署
    {
        $sql = "SELECT dp_no,dp_name FROM depts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $deptId, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dpNo = $row["dp_no"];
            $dpName = $row["dp_name"];

            $dpnoName[] =  $dpNo . ":" . $dpName;
        }
        return $dpnoName;
    }
    public function notin($deptId) //送られてきたid以外のidを取得する
    {
        $sql = "SELECT dp_no,dp_name FROM depts WHERE id NOT IN(:id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $deptId, PDO::PARAM_INT);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dpNo = $row["dp_no"];
            $dpName = $row["dp_name"];

            $dpList[] =  $dpNo . ":" . $dpName;
        }
        return $dpList;
    }
    //--------------------[所属部門ID取得処理]--------------------//
    //----[ID最小値取得]----//
    public function dpmin()
    {
        $sql = "SELECT MIN(id) FROM depts";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["MIN(id)"];
            $idMin =  $id;
        }
        return $idMin;
    }

    //----[ID最大値取得]----//
    public function dpmax()
    {
        $sql = "SELECT MAX(id) FROM depts";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["MAX(id)"];
            $idMax =  $id;
        }
        return $idMax;
    }

    //----[所属部門ID全件取得]----//
    public function dpNoName()
    {
        $sql = "SELECT dp_no,dp_name FROM depts";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dpNo = $row["dp_no"];
            $dpName = $row["dp_name"];
            $dpList[] =  $dpNo . ":" . $dpName;
        }
        return $dpList;
    }
}
