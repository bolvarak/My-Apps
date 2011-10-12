<?php
/**
 * @name Xml
 * @author Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.txt>
 * @copyright 2011 Travis Brown
 * @description This class provides an easy to use wrapper around
 * the SimpleXML class to make communicating with XML as simple as
 * communitcating with JSON
**/
class Xml {
	////////////////////////////////////////////////////////////////////////
	//////////      Constants    ///////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	const RETURN_ARRAY  = true;
	const RETURN_OBJECT = false;
	////////////////////////////////////////////////////////////////////////
	//////////      The Properties    /////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected static $oXml;
	////////////////////////////////////////////////////////////////////////
	//////////      Public Static    //////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method encodes arrays and objects
	 * into an XML object and returns it as a string
	 * @static
	 * @access public
	 * @param array $aRootNode
	 * @param array|object $mData
	 * @return string
	**/
	public static function Encode($aRootNode, $mData) {
		// Create a new SimpleXML object
		self::$oXml = new SimpleXMLElement("<{$aRootNode['sName']}/>");
		// Check to see if we need to add any attributes
		if (empty($aRootNode['@attributes']) === false) {
			// Append the attributes
			self::AppendAttributes($aRootNode['@attributes'], self::$oXml);
		}
		// Parse the childdren
		self::encodeChildren($mData, self::$oXml);
		// Return the XML string
		return self::$oXml->asXML();
	}
	/**
	 * This method decodes and XML string 
	 * into an object or an array
	 * @static
	 * @access public
	 * @param string $sXml
	 * @param integer [$iReturnType]
	 * @return SimpleXMLElement|array
	**/
	public static function Decode($sXml, $iReturnType = self::RETURN_OBJECT) {
		// Decode the XML
		$oXml = new SimpleXMLElement($sXml);
		// See if we need to convert it
		if ($iReturnType === self::RETURN_ARRAY) {
			return self::ConvertObjectToArray($oXml);
		} else {
			// Return the object
			return $oXml;
		}
	}
	////////////////////////////////////////////////////////////////////////
	//////////      Protected Static    ///////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method adds attributes to XML nodes
	 * @static
	 * @access protected
	 * @param array|object $mAttributes
	 * @param SimpleXMLElement $oXml
	 * @return void
	**/
	protected static function AppendAttributes($aAttributes, $oXml) {
		// Loop through the attributes
		foreach ($aAttributes as $sName => $sValue) {
			// Make sure we have valid data
			if ((is_scalar($sName) === true) && (is_scalar($sValue) === true)) {
				// Add the attribute
				$oXml->addAttribute($sName, $sValue);
			}
		}
	}
	/**
	 * This method converts an object into an array
	 * @static
	 * @access protected
	 * @param object $oData
	 * @return array $aData
	**/
	protected static function ConvertObjectToArray($oData) {
		// Cast the data
		$aData = (array) $oData;
		// Loop throuth the keys
		foreach ($aData as $sName => $mValue) {
			// Determine if we have a nested object
			if (is_object($mValue)) {
				$aData[$sName] = self::ConvertObjectToArray($mValue);
			}
		}
		// Return the array
		return $aData;
	}
	/**
	 * This method encodes arrays and objects
	 * into XML child nodes
	 * @static
	 * @access protected
	 * @param array|object $mData
	 * @param SimpleXMLElement $oXml
	 * @param array|object $mAttributePoint
	 * @return void
	**/
	protected static function EncodeChildren($mData, SimpleXMLElement $oXml, $mAttributePoint = null) {
		// Set the attribute point
		if ((empty($mAttributePoint) === true) && (empty($mData['@attributes']) === false)) {
			$mAttributePoint = $mData['@attributes'];
		}
		// Ensure we have valid data
		if ((is_array($mData) === true) || (is_object($mData) === true)) {
			// Loop through the data
			foreach ($mData as $sName => $mValue) {
				// Skip over attributes
				if ($sName !== '@attributes') {
					// Determine the data type
					if ((is_array($mValue)) || (is_object($mValue))) {
						// Execute this method again 
						// with the new data
						$oXml->{$sName} = null;
						self::EncodeChildren($mValue, $oXml->{$sName}, (is_array($mAttributePoint) ? $mAttributePoint[$sName] : $mAttributePoint->{$sName}));
					} else {
						// Set the child
						$oXml->{$sName} = $mValue;
					}
					// Check for attributes
					if (empty($mAttributePoint[$sName]) === false) {
						// Append the attributes
						self::AppendAttributes($mAttributePoint[$sName], $oXml->{$sName});
					} elseif (empty($mAttributePoint->{$sName}) === false) {
						// Append the attributes
						self::AppendAttributes($mData['@attributes']->{$sName}, $oXml->{$sName});
					}
				}
			}
		}
	}
}
