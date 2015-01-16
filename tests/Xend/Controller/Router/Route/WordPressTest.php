<?php

namespace Xend\Tests\Controller\Router\Route;

/**
 * WordPressTest
 *
 * @author Dagan
 */
class WordPressTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Data Provider for Modules
	 * 
	 * Parses the TestModules directory for test cases. Each directory is taken
	 * as a module's controller directory and the directory name is used as the
	 * module name. Test cases are provided via a testCases array defined in
	 * provider.php file inside the directory.
	 * 
	 * @return array
	 */
	public function matchProvider()
	{
		$tests = array();

		$modules = opendir(dirname(__FILE__) . '/TestModules');
		while ($module = readdir($modules)) {
			if (in_array($module, array('.', '..'))) {
				continue;
			}
			
			$path = dirname(__FILE__) . '/TestModules/' . $module;
			include($path . '/provider.php');
			
			$i = 0;
			foreach ($testCases as $testCase) {
				$key = sprintf('%s[%d]', $module, $i++);
				$tests[$key] = array_merge(array($module, $path), $testCase);
			}
		}
		
		return $tests;
	}

	/**
	 * @dataProvider matchProvider
	 */
	public function testMatch($module, $controllerDir, $queryType, $querySubType, $expectedController, $expectedAction)
	{
		// Set up a query object with the provided query type and subtype
		$query = $this->getMock('\Xend\WordPress\Query', array('getQueryType'));
		$query->expects($this->once())
			  ->method('getQueryType')
			  ->with(true)
			  ->will($this->returnValue(array(
			  		$queryType,
			  		$querySubType,
			  		'type' => $queryType,
			  		'subtype' => $querySubType)));
			  
		// Set up a request object using the query
		$request = new \Xend\Controller\Request($query);
		
		// Create a dispatcher, add the test module, and set the default module
		$dispatcher = new \Zend_Controller_Dispatcher_Standard();
		$dispatcher->setControllerDirectory($controllerDir, $module);
		$dispatcher->setDefaultModule($module);
		$dispatcher->setParam('prefixDefaultModule', true); // Necessary to namespace the test groups
		
		// Match the request
		$fixture = new \Xend\Controller\Router\Route\WordPress($dispatcher);
		$params = $fixture->match($request);
		
		// Test the result
		$this->assertTrue(is_array($params), 'The request did not match the route!');
		$this->assertArrayNotHasKey('module', $params, 'The request was expected to route to the default module but did not');
		$this->assertArrayHasKey('controller', $params);
		$this->assertEquals($expectedController, $params['controller'], 'The routed controller did not match the expected module');
		$this->assertArrayHasKey('action', $params);
		$this->assertEquals($expectedAction, $params['action'], 'The routed action did not match the expected module'); 
	}    
}
