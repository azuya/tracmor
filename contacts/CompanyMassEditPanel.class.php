<?php
/*
 * Copyright (c)  2009, Tracmor, LLC 
 *
 * This file is part of Tracmor.  
 *
 * Tracmor is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. 
 *	
 * Tracmor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tracmor; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
?>

<?php
// Include the classfile for CompanyEditPanelBase
require(__PANELBASE_CLASSES__ . '/CompanyEditPanelBase.class.php');

class CompanyMassEditPanel extends CompanyEditPanelBase {

	// Specify the Location of the Template (feel free to modify) for this Panel
	protected $strTemplate = '../contacts/CompanyMassEditPanel.tpl.php';
	// An array of custom fields

	// Primary Address inputs for new company
	    public $txtLongDescription;

	    public $arrCompaniesToEdit = array();

	public function __construct($objParentObject, $strClosePanelMethod, $arrayCompanyId) {

		try {
			parent::__construct($objParentObject, $strClosePanelMethod);
		} catch (QCallerException $objExc) {
			$objExc->IncrementOffset();
			throw $objExc;
		}
        $this->arrCompaniesToEdit = $arrayCompanyId;
		$this->txtLongDescription_Create();
		$this->strOverflow = QOverflow::Auto;
		//$this->btnSave->CausesValidation = QCausesValidation::SiblingsOnly;
	}

	// Create and Setup txtShortDescription
		protected function txtLongDescription_Create() {
		$this->txtLongDescription = new QTextBox($this);
		$this->txtLongDescription->Name = QApplication::Translate('Description');
	}

	// Save Button Click Actions
		public function btnSave_Click($strFormId, $strControlId, $strParameter) {

			$strQuery = sprintf("
				UPDATE `company`
				SET `long_description`='%s'
				WHERE `company_id` IN (%s)
			", $this->txtLongDescription->Text,
				implode(",", $this->arrCompaniesToEdit));

			$objDatabase = QApplication::$Database[1];
			$objDatabase->NonQuery($strQuery);


		$this->ParentControl->RemoveChildControls(true);
		$this->CloseSelf(true);
	    QApplication::Redirect('');
	}

	// Cancel Button Click Action
	public function btnCancel_Click($strFormId, $strControlId, $strParameter) {
		$this->ParentControl->RemoveChildControls(true);
		$this->CloseSelf(true);
	}
}
?>