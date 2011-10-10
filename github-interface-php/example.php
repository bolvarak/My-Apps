<?php
/**
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
// Load the library file
require_once('GitHub.php');
// Instantiate the class
$oGitHub = GitHub::getInstance(); // This is the singleton method
// To use the traditional method uncommment the following line
// $oGitHub = new GitHub();
// Load the user
$oGitHub->login('username', 'password');
// Load the user's list of repositories
$oGitHub->loadRepositories();
// Load a specific repository
$oGitHub->loadRepository('repositoryName');
// Load the repository's branches
$oGitHub->loadBranches();
// Dump the object
var_dump($oGitHub);