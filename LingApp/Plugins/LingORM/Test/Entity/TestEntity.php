<?php
namespace LingORM\Test\Entity;

/**
 * @Table (name="test",database="love")
 */
class TestEntity
{
    /**
     * @Column (type="int",isGenerated=1,primaryKey=1)
     */
    public $testId;
    
    /**
     * @Column (type="string")
     */
	public $testName;
	
	/**
	 * @Column (type="datetime")
	 */
	public $testTime;
	
	
}