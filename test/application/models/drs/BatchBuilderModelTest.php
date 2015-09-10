<?
/**
 * BatchBuilderModelTest - Test the user functionalities
 * 
 * @author
 * @version 
 */
require_once 'BatchBuilderAssistant.php';

class BatchBuilderModelTest extends PHPUnit_Framework_TestCase {

	const SAMPLE_IMAGES_PATH = "/home/acorn/dev/public/sampleimages";
	const SOURCE_PATH = "/home/acorn/dev/public/drs2sourceimages";
	const IMAGE_NAME = "testimage.jpg";
	const BB1_TEST_BATCH = "TEST_BATCH_BB1";
	const BB2_TEST_BATCH_TEMPLATE = "TEST_BATCH_BB2_TEMPLATE";
	const BB2_TEST_BATCH = "TEST_BATCH_BB2";
	const BB1_BATCH_XML = "/home/acorn/dev/public/drsstagingfiles/TEST_BATCH_BB1/batch.xml";
	//const BB2_OBJECT_XML = "/home/acorn/dev/public/drs2stagingfiles/_aux/TEST_BATCH_BB2/testimage/object.xml";
	const BB2_BATCH_XML = "/home/acorn/dev/public/drs2stagingfiles/testBB2project/TEST_BATCH_BB2/batch.xml";
	const BB2_DESCRIPTOR_XML = "/home/acorn/dev/public/drs2stagingfiles/testBB2project/TEST_BATCH_BB2/testimage/descriptor.xml";
	const BB2_PROJECT_NAME = "testBB2project";
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();	
		if (!file_exists(self::SOURCE_PATH))
		{
			mkdir(self::SOURCE_PATH);
		}
		//move images into the 'source' directory
		copy(self::SAMPLE_IMAGES_PATH . "/" . self::IMAGE_NAME, self::SOURCE_PATH . "/" . self::IMAGE_NAME);
	}
	
	protected function tearDown() {
		parent::tearDown();
	}

	
	/**
	 * Tests that the batch directories get properly created for BB1
	 */
	public function testDirectoryCreationForBB1()
	{
		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
		$config->__set(DRSDropperConfig::DRS_VERSION, DRSDropperConfig::DRS);
		BatchBuilderAssistant::createBB1DirectoryAndCopyFiles(self::SOURCE_PATH, self::IMAGE_NAME, self::BB1_TEST_BATCH, $config);
		//Verify that the directories were made and the file was copied
		$this->assertTrue(file_exists("/home/acorn/dev/public/drsstagingfiles/TEST_BATCH_BB1/deliverable/testimage.jpg"), "copy was unsuccessful: /home/acorn/dev/public/drsstagingfiles/TEST_BATCH_BB1/deliverable/testimage.jpg");
		
	}
	
	/**
	 * Tests that the batch directories get properly created for BB2
	 */
	public function testDirectoryCreationForBB2()
	{
		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
		$config->__set(DRSDropperConfig::DRS_VERSION, DRSDropperConfig::DRS2);
		$sourcefiles = array(self::IMAGE_NAME => self::IMAGE_NAME);
		BatchBuilderAssistant::prepareTemplateDirectory(self::SOURCE_PATH, $sourcefiles, self::BB2_PROJECT_NAME, $config);
		//Verify that the directories were made and the file was copied
		$this->assertTrue(file_exists("/home/acorn/dev/public/drs2stagingfiles/testBB2project"), "project directory creation was unsuccessful: /home/acorn/dev/public/drs2stagingfiles/testBB2project");
	}
	
	/**
	 * Tests that the batch file is created for BB1
	 */
	public function testBatchFilesForBB1()
	{
		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
		$config->__set(DRSDropperConfig::DRS_VERSION, DRSDropperConfig::DRS);
		$batchbuilderassistant = new BatchBuilderAssistant($config->getStagingFileDirectory(), $config->getBbClientPath(), $config->getBbScriptName(), $config->getDRSVersion());
		$batchbuilderassistant->execute(self::BB1_TEST_BATCH); 
		
		//Verify that the batch.xml file was created
		$this->assertTrue(file_exists(self::BB1_BATCH_XML), "batch.xml was not created properly here: " . self::BB1_BATCH_XML);
	
	}
	
	/**
	 * Tests that the batch file and descriptor file are created for BB2
	 */
	/*public function testProcessBatchForBB2()
	{
		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
		$config->__set(DRSDropperConfig::DRS_VERSION, DRSDropperConfig::DRS2);
		$batchbuilderassistant = new BatchBuilderAssistant($config->getStagingFileDirectory() . "/" . self::BB2_PROJECT_NAME, $config->getBbClientPath(), $config->getBbScriptName(), $config->getDRSVersion());
		$batchbuilderassistant->execute(self::BB2_TEST_BATCH); 
		
		//Verify that the batch.xml file was created
		$this->assertTrue(file_exists(self::BB2_BATCH_XML), "batch.xml was not created properly here: " . self::BB2_BATCH_XML);
		//Verify that the descriptor.xml file was created
		$this->assertTrue(file_exists(self::BB2_DESCRIPTOR_XML), "descriptor.xml was not created properly here: " . self::BB2_DESCRIPTOR_XML);
		
	}*/
}
?>
