<?php
/**
 * @name GitHub Interface
 * @description This class encapsulates the GitHub API
 * @language PHP
 * @author Travis Brown <travis@travismbrown.com>
 * @copyright 2011 Travis Brown 
 * @license GPL v3.0
 * ------------------------------------------------------------------------
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * ------------------------------------------------------------------------
**/
class GitHub {
	////////////////////////////////////////////////////////////////////////
	//////////      The Constants    //////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	const GITHUB_BASE_URI = 'https://github.com/api/v2/json';
	////////////////////////////////////////////////////////////////////////
	//////////      The Properties    /////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected static $oInstance = null; // This is our instance container
	protected $sBranch          = null; // This is the user's chosen branch
	protected $aBranches        = null; // This is a list of the branches associated with a repository
	protected $sPassword        = null; // This is the user's password
	protected $aRepositories    = null; // This is a list of the user's repositories
	protected $aRepository      = null; // This is the user's chosen repository
	protected $aUser            = null; // This is the container for the current user
	protected $sUsername        = null; // This is the user's username
	////////////////////////////////////////////////////////////////////////
	//////////      The Singleton Experience    ///////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This sets the singleton pattern instance
	 * @return GitHub self
	**/
	public static function setInstance() {
		// Try to set an instance
		try {
			// Set instance to new self
			self::$oInstance = new self();
			// Catch any exceptions
		} catch (Exception $oException) {
			// Set error string
			throw new Exception("ERROR:  {$oException->getMessage()}");
		}
		// Return instance of class
		return self::$oInstance;
	}
	/**
	 * This gets the singleton instance
	 * @return GitHub self
	**/
	public static function getInstance() {
		// Check to see if an instance has already
		// been created
		if (is_null(self::$oInstance)) {
			// If not, return a new instance
			return self::setInstance();
		} else {
			// If so, return the previously created
			// instance
			return self::$oInstance;
		}
	}
	/**
	 * This resets the singleton instance to null
	 * @return void
	**/
	public static function resetInstance() {
		// Reset the instance
		self::$oInsance = null;
	}
	////////////////////////////////////////////////////////////////////////
	//////////      The Constructor    ////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * Put anything here that needs to
	 * be initialized once the class
	 * is initialized
	 * @return GitHub $this
	**/
	public function __construct() {
		// Return instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	//////////      Public    /////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method loads the branches
	 * associated with the current 
	 * working repository
	 * @return GitHub $this
	**/
	public function loadBranches() {
		// Check for a user and repository
		if (empty($this->aUser) === false && empty($this->aRepository) === false) {
			// Load the branches
			$aBranches       = $this->callResource("repos/show/{$this->sUsername}/{$this->aRepository['name']}/branches", 'branches');
			// Set the branches
			$this->aBranches = (array) $aBranches;
		}
		// Return instance
		return $this;
	}
	/**
	 * This method loads the list of
	 * repositories associated with
	 * the current working user
	 * @return GitHub $this
	**/
	public function loadRepositories() {
		// Check for a user
		if (empty($this->aUser) === false) {
			// Load the repositories
			$aRepositories = $this->callResource("repos/show/{$this->sUsername}", 'repositories');
			// Set the repositories
			$this->aRepositories = (array) $aRepositories;
		}
		// Return instance
		return $this;
	}
	/**
	 * This method loads a specific repository
	 * associated with the current working user
	 * @param string $sName
	 * @return GitHub $this
	**/
	public function loadRepository($sName) {
		/// Check for a user
		if (empty($this->aUser) === false) {
			// Load the repositories
			$aRepository = $this->callResource("repos/show/{$this->sUsername}/{$sName}", 'repository');
			// Set the repositories
			$this->aRepository = (array) $aRepository;
		}
		// Return instance
		return $this;
	}
	/**
	 * This method logs a user in and grabs
	 * the data associated with the account
	 * @param string $sUsername
	 * @param string $sPassword
	 * @return GitHub $this
	**/
	public function login($sUsername, $sPassword) {
		// Set the username
		$this->sUsername = (string) $sUsername;
		// Set the password
		$this->sPassword = (string) $sPassword;
		// Try to make the call
		if ($aUser = $this->callResource("user/show/{$sUsername}", 'user')) {
			// Set the user
			$this->setUser($aUser);
		}
		// Return instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	//////////      Protected    //////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method makes a call to the 
	 * GitHub API and returns the response
	 * @param string $sMethod
	 * @param array [$aData]
	 * @param string [$sReturnKey]
	 * @return array
	**/
	protected function callResource($sMethod, $sReturnKey = null, $aData = array()) {
		// Setup the URL
		$sUrl    = (string) self::GITHUB_BASE_URI.'/'.$sMethod;
		// Setup the cURL request
		$rHandle = curl_init($sUrl);
		// Set the username and password
		curl_setopt($rHandle, CURLOPT_USERPWD, "{$this->getUsername()}:{$this->getPassword()}");
		// We don't need the headers
		curl_setopt($rHandle, CURLOPT_HEADER, false);
		// We want the data returned
		curl_setopt($rHandle, CURLOPT_RETURNTRANSFER, true);
		// Execute the handle
		$sResponse = curl_exec($rHandle);
		// Decode the response
		$aResponse = json_decode($sResponse, true);
		// Check for an error
		if (empty($aResponse['error']) === false) {
			// Throw a new exception
			throw new Exception("ERROR:  {$aResponse['error']}");
			// Return 
			return;
		}
		// Check for a return key
		if (empty($sReturnKey) === false) {
			// Set the new response
			$aResponse = $aResponse[$sReturnKey];
		}
		// Return the response
		return $aResponse;
	}
	////////////////////////////////////////////////////////////////////////
	//////////      Getters    ////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method grabs the current
	 * working branch for the current
	 * working repository from the system
	 * @return string
	**/
	public function getBranch() {
		// Return the current branch
		return $this->sBranch;
	}
	/**
	 * This method grabs the list of
	 * branches associated with the 
	 * current working repository
	 * from the system
	 * @return array
	**/
	public function getBranches() {
		// Return the current 
		// list of branches
		return $this->aBranches;
	}
	/**
	 * This method grabs the current 
	 * user password from the system
	 * @return string
	**/
	public function getPassword() {
		// Return the current password
		return $this->sPassword;
	}
	/**
	 * This method grabs the list of
	 * repositories associated with 
	 * the current user from the system
	 * @return array
	**/
	public function getRepositories() {
		// Return the current 
		// list of repositories
		return $this->aRepositories;
	}
	/**
	 * This method grabs the current
	 * working repository from the system
	 * @return aray
	**/
	public function getRepository() {
		// Return the current repository
		return $this->aRepository;
	}
	/**
	 * This method grabs the data
	 * associated with the current
	 * working user from the system
	 * @return array
	**/
	public function getUser() {
		// Return the user data
		return $this->aUser;
	}
	/**
	 * This method grabs the current 
	 * working username from the system
	 * @return string
	**/
	public function getUsername() {
		// Return the current username
		return $this->sUsername;
	}
	////////////////////////////////////////////////////////////////////////
	//////////      Setters    ////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This methos sets the current 
	 * working branch into the sysem
	 * @param string $sBranch
	 * @return GitHub $this
	**/
	public function setBranch($sBranch) {
		// Set the branch
		$this->sBranch = (string) $sBranch;
		// Return instance
		return $this;
	}
	/**
	 * This method sets the current list
	 * of branches into the system
	 * @param array $aBranches
	 * @return GitHub $this
	**/
	public function setBranches($aBranches) {
		// Set the branches
		$this->aBranches = (array) $aBranches;
		// Return instance
		return $this;
	}
	/**
	 * This method sets the current working
	 * password into the system
	 * @param string $sPassword
	 * @return GitHub $this
	**/
	public function setPassword($sPassword) {
		// Set the password 
		$this->sPassword = (string) $sPassword;
		// Return instance
		return $this;
	}
	/**
	 * This method sets the current list
	 * of repositories into the system
	 * @param array $aRepositories
	 * @return GitHub $this
	**/
	public function setRepositories($aRepositories) {
		// Set the repositories
		$this->aRepositories = (array) $aRepositories;
		// Return instance
		return $this;
	}
	/**
	 * This method sets the current working
	 * repository into the system
	 * @param array $aRepository
	 * @return GitHub $this
	**/
	public function setRepository($aRepository) {
		// Set the repository
		$this->aRepository = (array) $aRepository;
		// Return instance
		return $this;
	}
	/**
	 * This method sets the current 
	 * working user into the system
	 * @param array $aUser
	 * @return GitHub $this
	**/
	public function setUser($aUser) {
		// Set the user
		$this->aUser = (array) $aUser;
		// Return instance
		return $this;
	}
	/**
	 * This method sets the current working
	 * username into the systen
	 * @param string $sUsername
	 * @param GitHub $this
	**/
	public function setUsername($sUsername) {
		// Set the username
		$this->sUsername = (string) $sUsername;
		// Return instance
		return $this;
	}
}
