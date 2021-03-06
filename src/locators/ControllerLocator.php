<?php
/**
 * Locates controller based on page requested by client and values set in DD.
 */
final class ControllerLocator {
	private $strClassName;
	
	/**
	 * @param Application $objApplication
	 * @param string $strPagePath
	 */
	public function __construct(Application $objApplication, $strPagePath) {
		$this->setClassName($objApplication, $strPagePath);
	}

	/**
	 * Sets controller class name.
	 * 
	 * @param Application $objApplication
	 * @param string $strPagePath
	 * @throws ServletException
	 */
	private function setClassName(Application $objApplication, $strPagePath) {
		// get controller class folder
		$strFolder = $objApplication->getControllersPath();
	
		// gets page url
		$strURL = $strPagePath;
	
		// get controller class name
		$strClass = "";
		if(!$objApplication->getAutoRouting()) {
			$strClass = $objApplication->getRouteInfo($strURL)->getController();
		} else {
			$strClass = str_replace(" ","",ucwords(str_replace("/"," ",strtolower($strURL))))."Controller";
		}
	
		// loads controller class
		$strFile = $strFolder."/".$strClass.".php";
		if(!file_exists($strFile)) throw new ServletException("Controller not found on disk: ".$strFile);
		require_once($strFile);
	
		// instances controller class
		if(!class_exists($strClass)) throw new ServletException("Controller class not found: ".$strClass);
		
		// checks if it is a subclass of Controller
		if(!is_subclass_of($strClass, "Controller")) throw new ServletException($strClass." must be a subclass of Controller");
			
		$this->strClassName = $strClass;
	}

	/**
	 * Gets controller class name.
	 *
	 * @return string
	 */
	public function getClassName() {
		return $this->strClassName;
	}
}