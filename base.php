<?php
date_default_timezone_set("Asia/Taipei");
session_start();

class DB
{

    private $dsn = "mysql:host=localhost;charset=utf8;dbname=db02";
    private $root = "root";
    private $password = "";
    private $table;
    private $pdo;

    //設定建構式
    public function __construct($table)
    {

        $this->table = $table;

        $this->pdo = new PDO($this->dsn, $this->root, $this->password);
    }

    public function all(...$arg)
    {

        $sql = "select * from $this->table ";

        if (!empty($arg[0]) && is_array($arg[0])) {
            foreach ($arg[0] as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            $sql = $sql . " where " . implode(" && ", $tmp);
        }

        if (!empty($arg[1])) {
            $sql = $sql . $arg[1];
        }

        //echo $sql;
        return $this->pdo->query($sql)->fetchAll();
    }

    public function find($arg)
    {

        $sql = "select * from $this->table ";

        if (is_array($arg)) {

            foreach ($arg as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            $sql = $sql . " where " . implode(" && ", $tmp);

        } else {
            $sql = $sql . " where `id`='$arg'";
        }

        //echo $sql;
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

//計算筆數
    public function count(...$arg)
    {

        $sql = "select count(*) from $this->table ";

        if (!empty($arg[0]) && is_array($arg[0])) {

            foreach ($arg[0] as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            $sql = $sql . " where " . implode(" && ", $tmp);
        }

        if (!empty($arg[1])) {
            $sql = $sql . $arg[1];
        }

        return $this->pdo->query($sql)->fetchColumn();

    }

    public function save($arg)
    {

        if (!empty($arg['id'])) {
            //更新
            foreach ($arg as $key => $value) {
                if ($key != 'id') {
                    $tmp[] = sprintf("`%s`='%s'", $key, $value);
                }
            }

            $sql = "update $this->table set " . implode(",", $tmp) . " where `id`='" . $arg['id'] . "'";

        } else {
            //新增
            $sql = "insert into $this->table (`" . implode("`,`", array_keys($arg)) . "`) values('" . implode("','", $arg) . "')";

        }

        return $this->pdo->exec($sql);
    }

    public function del($arg)
    {

        $sql = "delete from $this->table ";

        if (is_array($arg)) {

            foreach ($arg as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            $sql = $sql . " where " . implode(" && ", $tmp);

        } else {
            $sql = $sql . " where `id`='$arg'";
        }

        //echo $sql;
        return $this->pdo->exec($sql);
    }

    public function q($sql)
    {
        return $this->pdo->query($sql)->fetchAll();
    }

}

function to($url)
{
    header("location:" . $url);
}

$Visited=new DB('visited');
$News=new DB('news');
$Que=new DB('que');
$User=new DB('user');
$Log=new DB('log');

$visited=$Visited->find(['date'=>date("Y-m-d")]);
if(empty($visited)){
    $Visited->save(["date"=>date("Y-m-d"),"total"=>1]);
    $visited=$Visited->find(['date'=>date("Y-m-d")]);
    // $_SESSION["visited"]=1;
}

if(empty($_SESSION['visited'])){
$_SESSION['visited']=$visited['total'];

}

?>