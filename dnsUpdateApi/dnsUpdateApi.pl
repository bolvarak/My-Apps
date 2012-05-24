#!/usr/bin/perl
 #############################################################################
### REQUIREMENTS ##############################################################
 #############################################################################
 ### CGI
 ### CGI::Carp
 ### JSON
 #############################################################################
### USAGE #####################################################################
 #############################################################################
 ### 1) Place this file in a web accessible directory
 ### 2) Make sure the zone/domain/subdomain exists before trying to update it
 ### 3) Make a GET request using your favorite library (cURL, wget, etc) to 
 ###    the following url, replacing the examples with real data, of course
 ###    
 ###    http://www.example.com/dnsUpdateApi.pl?key=123456&host=host.example.com
 ###
 ###    You're done, now setup a cron to do this automagically
 #############################################################################
### NOTES #####################################################################
 #############################################################################
 ### 1) The "key" is completely arbitrary, you should probably put in some sort
 ###    of key authenticator into the system, it doesn't have to be anything 
 ###    crazy or extravogant, just enough to disallow everyone from using this
 ### 2) There is very minimal error handling, I just needed something quick and
 ###     dirty, so I set this up.  Feel free to modify however you see fit
 #############################################################################
### The Goods #################################################################
 #############################################################################
# Use strict syntax
use strict;
# Use warnings
use warnings;
# Use the CGI library
use CGI;
# Use Carp to print errors to the browser
use CGI::Carp("fatalsToBrowser");
# Use the JSON library
use JSON;
# Instantiate the CGI module
my($oCgi) = CGI->new;
# Set the path to the zone file container
my($sZoneFileContainer) = "/var/named/openpanel";
# Print headers
print $oCgi->header("application/json");
# Create a JSON instance
my($oJson) = JSON->new->allow_nonref;
# Response placeholder
my($oResponseJson);
# Make sure we have an API key
if ($oCgi->param("key")) {
	# Check for a host to replace
	if ($oCgi->param("host") and ($oCgi->param("host") =~ m/([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)\.([a-zA-Z\.]{2,5})/)) {
		# Set the zone file name
		my($sZoneFileName) = $sZoneFileContainer."/$2.$3.zone";
		# Set the host name to change
		my($sHost)         = $1;
		# Set the new IP
		my($sIpAddress)    = $ENV{"REMOTE_ADDR"};
		# Open the dns file
		open(my $hZoneFile, "<$sZoneFileName") 
			or die "Unable to open DNS entry file";
		# New entry placeholder
		my($sNewZoneFileContents) = "";
		# Read the file
		while (<$hZoneFile>) {
			# Set the new line
			my($sNewLine) = $_;
			# Test the line
			if ($sNewLine =~ m/${sHost}/) {
				# Replace the IP
				$sNewLine =~ s/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/${sIpAddress}/;
			}
			# Add the line to the new file
			$sNewZoneFileContents .= $sNewLine;
		}
		# Close the file
		close($hZoneFile);
		# Remove the old previous file
		unlink("$sZoneFileName.prev");
		# Move the old file
		rename($sZoneFileName, "$sZoneFileName.prev");
		# Write the new file
		open(my $hNewZoneFile, "+>$sZoneFileName");
		# Write the contents
		print $hNewZoneFile $sNewZoneFileContents;
		# Close the file
		close($hNewZoneFile);
		# Reload Bind
		system("/etc/init.d/bind9 reload");
		# Set the response
		$oResponseJson = {
			"sMessage" => "The DNS zone file has been successfully updated",
			"bSuccess" => 1, 
			"sZone"    => $sNewZoneFileContents
		};
	} else {
		# Set the response
		$oResponseJson = {
			"sMessage" => "The DNS zone file could not be found because no host was provided or the provided host was invalid.", 
			"bSuccess" => 0
		};
	}
} else {
	$oResponseJson = {
		"sMessage" => "There was no API key provided or the they that was provided was invalid.", 
		"bSuccess" => 0
	};
}
# Send the response
print $oJson->pretty->encode($oResponseJson);
# And ... We're done!

