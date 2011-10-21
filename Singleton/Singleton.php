<?php
/**
 * @name Singleton
 * @author Travis Brown <tmbrown6@gmail.com>
 * @copyright 2011 Travis Brown
 * @license GPLv3
 * @description This class takes any normal class
 * and creates a singleton out of it.
**?
class Singleton {
	////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected static $aInstances = array();	// This holds our set instances
	////////////////////////////////////////////////////////////////////////
	/// Protected Static //////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method adds an instance to the
	 * array of existing instances
	 * @static
	 * @access protected
	 * @param string $sClass
	 * @param object $oInstance
	 * @return Singleton self
	**/
	protected static function AddInstance($sClass, $oInstance) {
		// Append the instance
		self::$aInstances[$sClass] = $oInstance;
		// Return instance of self
		return self;
	}
	/**
	 * This method removes an instance from
	 * the array of existing instances
	 * @static
	 * @access protected
	 * @param string $sClass
	 * @return Singleton self
	**/
	protected static function RemoveInstance($sClass) {
		// Check for existing instance
		if (empty(self::$aInstances[$sClass]) === false) {
			// Remove the instance
			unset(self::$aInstances[$sClass]);
		}
		// Return instance of self
		return self;
	}
	////////////////////////////////////////////////////////////////////////
	/// Public Static /////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method gets a singleton instance
	 * of a traditional class
	 * @static
	 * @access public
	 * @param string $sClass
	 * @return object $sClass
	**/
	public static function Get($sClass) {
		// Check to see if the instance has
		// already been loaded
		if (empty(self::$aInstances[$sClass]) === true) {
			// Check for the class
			if (class_exists($sClass)) {
				// Create the instance
				$oInstance = new $sClass();
				// Add the instance to the
				// array of instances
				self::AddInstance($sClass, $oInstance);
			} else {
				// Throw a new exception because
				// the class does not exist or
				// has yet to be loaded
				throw new Exception("Class \"{$sClass}\" does not exist.");
			}
		} else {
			// Set the instance
			$oInstance = self::$aInstances[$sClass];
		}
		// Return the instance
		return $oInstance;
	}
	/**
	 * This method is simply a wrapper for a
	 * @method ResetInstance as this could be
	 * more useful than for just removing instances
	 * @static
	 * @access public
	 * @param string $sClass
	 * @return Singleton self
	**/
	public static function Reset($sClass) {
		// Reset the instance
		return self::RemoveInstance($sClass);
	}
	/**
	 * This is yet another wrapper with
	 * greater potential
	 * @static
	 * @access public
	 * @param string $sClass
	 * @param object $oInstance
	 * @return object $oInstance
	**/
	public static function Set($sClass, $oInstance) {
		// Make sure we are trying to set
		// the instance to the proper class
		if ($oInstance instanceof $sClass) {
			// Set the instance
			self::AddClass($sClass, $oInstance);
			// Return the instance
			return self::$aInstances[$sClass];
		}
	}
}
