<?php
namespace LingORM\Test\TestSample;

require_once dirname(dirname(__DIR__)).'/AutoLoader/AutoLoader.php';

use LingORM\Test\Entity\TestEntity;
use LingORM\ORM\Query;

class Test
{ 

    public function test()
    {
//         $this->batchUpdate();
        $this->camelize("aa_bb.cc", "_");
    }
    
    private function camelize($str,$separator)
    {
        $reg = "/[$separator]+([^$separator]{1})/";
        $str = preg_replace_callback($reg, function ($matches)
        {
            return strtoupper($matches[1]);
        }, $str);
        $result = ucfirst($str);
        return $result;
    }

    public function insert()
    {
        $entity = new TestEntity();
        $entity->testName = "my name100";
        $entity->testTime = "2016-09-01";
        
        $query = new Query("love");
        $query->createQuery()->insert($entity);
        echo "insert success";
    }

    public function batchInsert()
    {
        $entityArr = array();
        for($i = 0; $i < 2; $i ++)
        {
            $entity = new TestEntity();
            $entity->testName = "my name " . $i;
            $entity->testTime = "2016-11-01";
            array_push($entityArr, $entity);
        }
        
        $query = new Query("love");
        $query->createQuery()->batchInsert($entityArr);
        echo "batche insert success";
    }

    public function update()
    {
        $entity = new TestEntity();
        $entity->testId = 1;
        $entity->testName = "test name1";
        $entity->testTime = "2016-11-01";
        
        $query = new Query("love");
        $result = $query->createQuery()->update($entity,true);
        var_dump($result);
        echo "update success";
    }

    public function batchUpdate()
    {
        $entityArr = array();
        for($i = 6; $i < 9; $i ++)
        {
            $entity = new TestEntity();
            $entity->testId = $i;
            $entity->testName = "my name2 " . $i;
            $entity->testTime = null;
            array_push($entityArr, $entity);
        }
        
        $query = new Query("love");
        $query->createQuery(new TestEntity())->batchUpdate($entityArr,true);
        
        echo "batch update success";
    }

    public function delete()
    {
        $entity = new TestEntity();
        $entity->testId = 4;
        
        $query = new Query("love");
        $query->createQuery()->delete($entity);
        echo "delete success";
    }
    
    public function deleteBy()
    {
        $query = new Query("love");
        $testTable = $query->createTable(new TestEntity());
        $wh = $query->createWhere();
        $wh->setOr(
                $testTable->testId->eq(3)
        );
        $result = $query->createQuery()->deleteBy($testTable, $wh);
        var_dump($result);
        exit();
    }

    public function fetchOne()
    {
        $query = new Query("love");
        $testTable = $query->createTable($testTable = new TestEntity());
        $wh = $query->createWhere();
        $wh->setOr(
            	$testTable->testId->lt(2),$testTable->testId->gt(3)
            );
        $order=$query->createOrder()
            ->orderBy($testTable->testId, "desc")
            ->orderBy($testTable->testName, "asc");
        
        $result = $query->createQuery()->fetchOne($testTable, $wh, $order);
        var_dump($result);
        exit();
    }
    
    public function fetchAll()
    {
        $query = new Query("love");
        $testTable = $query->createTable($testTable = new TestEntity());
        $wh = $query->createWhere();
        $wh->setOr(
                $testTable->testId->lt(2),$testTable->testId->gt(3)
        );
        $order=$query->createOrder()
            ->orderBy($testTable->testId, "desc")
            ->orderBy($testTable->testName, "asc");
    
        $result = $query->createQuery()->fetchAll($testTable, $wh, $order);
        var_dump($result);
        exit();
    }
    
    public function selectBuilder()
    {
        $query = new Query("love");
        $testTable = $query->createTable($testTable = new TestEntity());
        $where = $query->createWhere();
        $where->setOr(
                $testTable->testId->lt(2),$testTable->testId->gt(3)
        );
        
        $queryBuilder = $query->createQueryBuilder();
        $queryBuilder->select($testTable->testId->count()->alias("num"),$testTable,$testTable->testTime)
            ->from($testTable)
            ->where($where)
            ->orderBy($testTable->testId, "desc");
        $result = $queryBuilder->getResult($testTable);
        
        var_dump($result);
        exit();
    }
    
    public function selectPage()
    {
        $query = new Query("love");
        
        $testTable = $query->createTable(new TestEntity());
        
        $where = $query->createWhere();
        $where->setOr(
                $testTable->testId->lt(2),$testTable->testId->gt(3)
        );
        
        $queryBuilder = $query->createQueryBuilder();
        $queryBuilder->select($testTable)
        ->from($testTable)
        ->where($where)
        ->orderBy($testTable->testId, "desc");
        $result = $queryBuilder->getPageResult(2, 1,$testTable);
        
        var_dump($result);
        exit();
    }
    
    public function exeSql()
    {
        $query = new Query("love");
        $sql="update love.test set testName='first name' where testId=:testId";
        $paramArr=array("testId"=>1);
        $result = $query->createSql()->excute($sql, $paramArr);
        var_dump($result);
        exit();
    }
    
    public function getSqlResult()
    {
        $query = new Query("love");
        $sql="select * from love.test where testId=:testId";
        $paramArr=array("testId"=>1);
        $pageSize=1;
        $pageIndex=1;
        $result = $query->createSql()->getPageResult($sql, $paramArr, $pageIndex, $pageSize, new TestEntity());
        var_dump($result);
        exit();
        
    }
    
    public function aaa()
    {
        $testTable=(new TestEntity())->createObject();
        
    }
}

(new Test())->test();