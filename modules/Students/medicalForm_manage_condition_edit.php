<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Students/medicalForm_manage_condition_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/medicalForm_manage.php'>" . __($guid, 'Manage Medical Forms') . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/medicalForm_manage_edit.php&&gibbonPersonMedicalID=" . $_GET["gibbonPersonMedicalID"] . "'>" . __($guid, 'Edit Medical Form') . "</a> > </div><div class='trailEnd'>" . __($guid, 'Edit Condition') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["return"])) { returnProcess($guid, $_GET["return"], null, null); }
	//Check if school year specified
	$gibbonPersonMedicalID=$_GET["gibbonPersonMedicalID"] ;
	$gibbonPersonMedicalConditionID=$_GET["gibbonPersonMedicalConditionID"] ;
	$search=$_GET["search"] ;
	if ($gibbonPersonMedicalID=="" OR $gibbonPersonMedicalConditionID=="") {
		print "<div class='error'>" ;
			print __($guid, "You have not specified one or more required parameters.") ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("gibbonPersonMedicalConditionID"=>$gibbonPersonMedicalConditionID); 
			$sql="SELECT * FROM gibbonPersonMedicalCondition WHERE gibbonPersonMedicalConditionID=:gibbonPersonMedicalConditionID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print __($guid, "The specified record cannot be found.") ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
			
			if ($search!="") {
				print "<div class='linkTop'>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Students/medicalForm_manage_edit.php&search=$search&gibbonPersonMedicalID=$gibbonPersonMedicalID'>" . __($guid, 'Back') . "</a>" ;
				print "</div>" ;
			}
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/medicalForm_manage_condition_editProcess.php?gibbonPersonMedicalID=$gibbonPersonMedicalID&gibbonPersonMedicalConditionID=$gibbonPersonMedicalConditionID&search=$search" ?>">
				<table class='smallIntBorder fullWidth' cellspacing='0'>	
					<tr>
						<td style='width: 275px'> 
							<b><?php print __($guid, 'Person') ?> *</b><br/>
							<span class="emphasis small"><?php print __($guid, 'This value cannot be changed.') ?></span>
						</td>
						<td class="right">
							<?php
							try {
								$dataSelect=array("gibbonPersonMedicalID"=>$row["gibbonPersonMedicalID"]); 
								$sqlSelect="SELECT surname, preferredName FROM gibbonPerson JOIN gibbonPersonMedical ON (gibbonPerson.gibbonPersonID=gibbonPersonMedical.gibbonPersonID) WHERE gibbonPersonMedicalID=:gibbonPersonMedicalID" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							$rowSelect=$resultSelect->fetch() ;
							?>	
							<input readonly name="personName" id="personName" maxlength=255 value="<?php print formatName("", $rowSelect["preferredName"], $rowSelect["surname"], "Student") ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Condition Name') ?> *</b><br/>
						</td>
						<td class="right">
							<select class="standardWidth" name="name" id="name">
								<?php
								print "<option value='Please select...'>" . __($guid, 'Please select...') . "</option>" ;
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT * FROM gibbonMedicalCondition ORDER BY name" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									 if ($row["name"]==$rowSelect["name"]) {
										print "<option selected value='" . htmlPrep($rowSelect["name"]) . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
									}
									 else {
										print "<option value='" . htmlPrep($rowSelect["name"]) . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
									}
								}
								?>				
							</select>
							<script type="text/javascript">
								var name2=new LiveValidation('name');
								name.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>	
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Risk') ?> *</b><br/>
						</td>
						<td class="right">
							<select name="gibbonAlertLevelID" id="gibbonAlertLevelID" class="standardWidth">
								<option value='Please select...'>Please select...</option>
								<?php
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT * FROM gibbonAlertLevel ORDER BY sequenceNumber" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								
								while ($rowSelect=$resultSelect->fetch()) {
									$selected="" ;
									if ($row["gibbonAlertLevelID"]==$rowSelect["gibbonAlertLevelID"]) {
										$selected="selected" ;
									}	
									print "<option $selected value='" . $rowSelect["gibbonAlertLevelID"] . "'>" . __($guid, $rowSelect["name"]) . "</option>" ; 
								}
								?>
							</select>
							<script type="text/javascript">
								var gibbonAlertLevelID=new LiveValidation('gibbonAlertLevelID');
								gibbonAlertLevelID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>	
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Triggers') ?></b><br/>
						</td>
						<td class="right">
							<input name="triggers" id="triggers" maxlength=255 value="<?php print htmlPrep($row["triggers"]) ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Reaction') ?></b><br/>
						</td>
						<td class="right">
							<input name="reaction" id="reaction" maxlength=255 value="<?php print htmlPrep($row["reaction"]) ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Response') ?></b><br/>
						</td>
						<td class="right">
							<input name="response" id="response" maxlength=255 value="<?php print htmlPrep($row["response"]) ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Medication') ?></b><br/>
						</td>
						<td class="right">
							<input name="medication" id="medication" maxlength=255 value="<?php print htmlPrep($row["medication"]) ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Last Episode Date') ?></b><br/>
							<span class="emphasis small"><?php print $_SESSION[$guid]["i18n"]["dateFormat"]  ?></span>
						</td>
						<td class="right">
							<input name="lastEpisode" id="lastEpisode" maxlength=10 value="<?php print dateConvertBack($guid, $row["lastEpisode"]) ?>" type="text" class="standardWidth">
							<script type="text/javascript">
								var lastEpisode=new LiveValidation('lastEpisode');
								lastEpisode.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } ); 
							</script>
							 <script type="text/javascript">
								$(function() {
									$( "#lastEpisode" ).datepicker();
								});
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Last Episode Treatment') ?></b><br/>
						</td>
						<td class="right">
							<input name="lastEpisodeTreatment" id="lastEpisodeTreatment" maxlength=255 value="<?php print htmlPrep($row["lastEpisodeTreatment"]) ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Comment') ?></b><br/>
						</td>
						<td class="right">
							<textarea name="comment" id="comment" rows=8 class="standardWidth"><?php print $row["comment"] ?></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<span class="emphasis small">* <?php print __($guid, "denotes a required field") ; ?></span>
						</td>
						<td class="right">
							<input name="gibbonPersonMedicalID" id="gibbonPersonMedicalID" value="<?php print $gibbonPersonMedicalID ?>" type="hidden">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="<?php print __($guid, "Submit") ; ?>">
						</td>
					</tr>
				</table>
			</form>
			<?php
		}
	}
}
?>