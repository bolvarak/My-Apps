<?php
// Load the Xml library
require_once('Xml.php');
// Create the XML string
$sXml = Xml::Encode(array(
	'sName'       => 'TestRootNode', 
	'@attributes' => array(
		'ExampleAttributeOne' => true, 
		'ExampleAttributeTwo' => 'Testing'
	)
), array(
	'Node1' => 'Foo', 
	'Node2' => 'Bar', 
	'Alice' => array(
		'Pills' => array(
			'Blue' => true, 
			'Red'  => false
		)
	), 
	'@attributes' => array(
		'Alice' => array(
			'Pills' => array(
				'Blue' => array(
					'Description' => 'This one makes you forget'
				), 
				'Red' => array(
					'Description' => 'This one frees you'
				)
			)
		)
	)
));
// Display the XML String
print("\n\n{$sXml}\n\n");
// Now decode the XML into an object
print_r(Xml::Decode($sXml));
// Now decode the XML into an array
print_r(Xml::Decode($sXml, true)); // Alternatively you can use the class constants Xml::RETURN_OBJECT and Xml::RETURN_ARRAY
