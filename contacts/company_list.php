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

	require_once('../includes/prepend.inc.php');
	QApplication::Authenticate(4);
	require_once(__FORMBASE_CLASSES__ . '/CompanyListFormBase.class.php');
	require('../contacts/CompanyMassEditPanel.class.php');
	/**
	 * This is a quick-and-dirty draft form object to do the List All functionality
	 * of the Company class.  It extends from the code-generated
	 * abstract CompanyListFormBase class.
	 *
	 * Any display custimizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 *
	 * Additional qform control objects can also be defined and used here, as well.
	 *
	 * @package Application
	 * @subpackage FormDraftObjects
	 *
	 */
	class CompanyListForm extends CompanyListFormBase {

		// Header Tabs
		protected $ctlHeaderMenu;

		// Shortcut Menu
		protected $ctlShortcutMenu;

		// Basic Inputs
		protected $txtShortDescription;
		protected $txtCity;
		protected $lstStateProvince;
		protected $lstCountry;

		// Buttons
		protected $btnSearch;
		protected $blnSearch;
		protected $btnClear;

		// Advanced Label/Link
		protected $lblAdvanced;
		// Boolean that toggles Advanced Search display
		protected $blnAdvanced;
		// Advanced Search Composite control
		protected $ctlAdvanced;
		// Custom Fields array
		protected $arrCustomFields;

		// Search Values
		protected $strShortDescription;
		protected $strCity;
		protected $intStateProvinceId;
		protected $intCountryId;
		protected $strDateModified;
		protected $strDateModifiedFirst;
		protected $strDateModifiedLast;
		protected $blnAttachment;

		// Mass Action controls
		protected $dlgMassDelete;
		protected $dlgMassEdit;
		protected $btnMassDelete;
		protected $btnMassEdit;
		protected $btnMassDeleteConfirm;
		protected $btnMassDeleteCancel;
		protected $lblMassActionError;

		protected $arrToDelete = array();

		protected function Form_Create() {

			// Create the Header Menu
			$this->ctlHeaderMenu_Create();
			// Create the Shortcut Menu
			$this->ctlShortcutMenu_Create();

			$this->txtShortDescription_Create();
			$this->txtCity_Create();
			$this->lstStateProvince_Create();
			$this->lstCountry_Create();
			$this->btnSearch_Create();
			$this->btnClear_Create();
			$this->ctlAdvanced_Create();
			$this->lblAdvanced_Create();
			$this->dtgCompany_Create();

			// Mass Action
			$this->btnMassDelete_Create();
			$this->btnMassEdit_Create();
			$this->lblMassActionError_Create();
			$this->dlgDelete_Create();
			$this->dlgMassEdit_Create();
			$this->btnMassDeleteConfirm_Create();
			$this->btnMassDeleteCancel_Create();
		}

		protected function dtgCompany_Bind() {

			// Assing the search values given from the form input
			if ($this->blnSearch) {
				$this->assignSearchValues();
			}

			// Assign the class member values to local variables
			$strShortDescription = $this->strShortDescription;
			$strCity = $this->strCity;
			$intStateProvinceId = $this->intStateProvinceId;
			$intCountryId = $this->intCountryId;
			$strDateModifiedFirst = $this->strDateModifiedFirst;
			$strDateModifiedLast = $this->strDateModifiedLast;
			$strDateModified = $this->strDateModified;
			$blnAttachment = $this->blnAttachment;
			$arrCustomFields = $this->arrCustomFields;

			// Expand to include the primary address, State/Province, and Country
			$objExpansionMap[Company::ExpandAddress][Address::ExpandStateProvince] = true;
			$objExpansionMap[Company::ExpandAddress][Address::ExpandCountry] = true;

			$this->dtgCompany->TotalItemCount = Company::CountBySearchHelper($strShortDescription, $strCity, $intStateProvinceId, $intCountryId, $arrCustomFields, $strDateModified, $strDateModifiedFirst, $strDateModifiedLast, $blnAttachment, $objExpansionMap);
			if ($this->dtgCompany->TotalItemCount == 0) {
				$this->dtgCompany->ShowHeader = false;
			}
			else {
				$this->dtgCompany->DataSource = Company::LoadArrayBySearchHelper($strShortDescription, $strCity, $intStateProvinceId, $intCountryId, $arrCustomFields, $strDateModified, $strDateModifiedFirst, $strDateModifiedLast, $blnAttachment, $this->dtgCompany->SortInfo, $this->dtgCompany->LimitInfo, $objExpansionMap);
				$this->dtgCompany->ShowHeader = true;
			}
			$this->blnSearch = false;
		}

  	// Create and Setup the Header Composite Control
  	protected function ctlHeaderMenu_Create() {
  		$this->ctlHeaderMenu = new QHeaderMenu($this);
  	}

  	// Create and Setp the Shortcut Menu Composite Control
  	protected function ctlShortcutMenu_Create() {
  		$this->ctlShortcutMenu = new QShortcutMenu($this);
  	}

		// Setup the Short Description (Company Name) Search Input
	  protected function txtShortDescription_Create() {
	    $this->txtShortDescription = new QTextBox($this);
			$this->txtShortDescription->Name = 'Company Name';
      $this->txtShortDescription->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
      $this->txtShortDescription->AddAction(new QEnterKeyEvent(), new QTerminateAction());
	  }

	  // Setup the City Search Input
	  protected function txtCity_Create() {
	    $this->txtCity = new QTextBox($this);
			$this->txtCity->Name = 'City';
      $this->txtCity->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
      $this->txtCity->AddAction(new QEnterKeyEvent(), new QTerminateAction());
	  }

	  // Setup the State/Province Search Input
	  protected function lstStateProvince_Create() {
      $this->lstStateProvince = new QListBox($this);
			$this->lstStateProvince->Name = 'State/Province';
			$this->lstStateProvince->AddItem('- ALL -', null);
			foreach (StateProvince::LoadAll() as $objStateProvince) {
				$this->lstStateProvince->AddItem($objStateProvince->ShortDescription, $objStateProvince->StateProvinceId);
			}
	  }

	  // Setup the Country Search Input
	  protected function lstCountry_Create() {
      $this->lstCountry = new QListBox($this);
			$this->lstCountry->Name = 'Country';
			$this->lstCountry->AddItem('- ALL -', null);
			foreach (Country::LoadAll() as $objCountry) {
				$this->lstCountry->AddItem($objCountry->ShortDescription, $objCountry->CountryId);
			}
			// Add actions for when this input is changed
			$this->lstCountry->AddAction(new QChangeEvent(), new QServerAction('lstCountry_Select'));
			$this->lstCountry->AddAction(new QEnterKeyEvent(), new QServerAction('lstCountry_Select'));
			$this->lstCountry->AddAction(new QEnterKeyEvent(), new QTerminateAction());
	  }

	  /**************************
	   *	CREATE BUTTON METHODS
	  **************************/
		// Create the Search Button
	  protected function btnSearch_Create() {
			$this->btnSearch = new QButton($this);
			$this->btnSearch->Name = 'search';
			$this->btnSearch->Text = 'Search';
			$this->btnSearch->AddAction(new QClickEvent(), new QServerAction('btnSearch_Click'));
			$this->btnSearch->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->btnSearch->AddAction(new QEnterKeyEvent(), new QTerminateAction());
	  }

	  // Create the Clear Button
	  protected function btnClear_Create() {
	  	$this->btnClear = new QButton($this);
			$this->btnClear->Name = 'clear';
			$this->btnClear->Text = 'Clear';
			$this->btnClear->AddAction(new QClickEvent(), new QServerAction('btnClear_Click'));
			$this->btnClear->AddAction(new QEnterKeyEvent(), new QServerAction('btnClear_Click'));
			$this->btnClear->AddAction(new QEnterKeyEvent(), new QTerminateAction());
	  }

	  // Create the Advanced Search Label
	  protected function lblAdvanced_Create() {
	  	$this->lblAdvanced = new QLabel($this);
	  	$this->lblAdvanced->Name = 'Advanced';
	  	$this->lblAdvanced->Text = 'Advanced Search';
	  	$this->lblAdvanced->AddAction(new QClickEvent(), new QToggleDisplayAction($this->ctlAdvanced));
	  	$this->lblAdvanced->AddAction(new QClickEvent(), new QAjaxAction('lblAdvanced_Click'));
	  	// Make it appear like a link even though it is actually a control
	  	$this->lblAdvanced->SetCustomStyle('text-decoration', 'underline');
	  	$this->lblAdvanced->SetCustomStyle('cursor', 'pointer');
	  }

	  // Create the Advanced Search Composite Control
  	protected function ctlAdvanced_Create() {
  		$this->ctlAdvanced = new QAdvancedSearchComposite($this, 7);
  		$this->ctlAdvanced->Display = false;
  	}

	  // Create the Company Datagrid
  	protected function dtgCompany_Create() {
			$this->dtgCompany = new QDataGrid($this);
			$this->dtgCompany->Name = 'company_list';
  		$this->dtgCompany->CellPadding = 5;
  		$this->dtgCompany->CellSpacing = 0;
  		$this->dtgCompany->CssClass = "datagrid";

      // Disable AJAX for the datagrid
      $this->dtgCompany->UseAjax = false;

      // Allow for column toggling
      $this->dtgCompany->ShowColumnToggle = true;

      // Allow for CSV Export
      $this->dtgCompany->ShowExportCsv = true;

      // Enable Pagination
      $objPaginator = new QPaginator($this->dtgCompany);
      $this->dtgCompany->Paginator = $objPaginator;
      $this->dtgCompany->ItemsPerPage = QApplication::$TracmorSettings->SearchResultsPerPage;

      // Add Checkbox Column
	  $this->dtgCompany->AddColumn(new QDataGridColumnExt('<?= $_CONTROL->chkSelectAll_Render() ?>', '<?=$_CONTROL->chkSelected_Render($_ITEM->CompanyId) ?>', 'CssClass="dtg_column"', 'HtmlEntities=false'));

	  $this->dtgCompany->AddColumn(new QDataGridColumnExt('ID', '<?= $_ITEM->CompanyId ?>', array('OrderByClause' => QQ::OrderBy(QQN::Company()->CompanyId), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Company()->CompanyId, false), 'CssClass' => "dtg_column", 'HtmlEntities' => false)));
      $this->dtgCompany->AddColumn(new QDataGridColumnExt('<img src=../images/icons/attachment_gray.gif border=0 title=Attachments alt=Attachments>', '<?= Attachment::toStringIcon($_ITEM->GetVirtualAttribute(\'attachment_count\')); ?>', 'SortByCommand="__attachment_count ASC"', 'ReverseSortByCommand="__attachment_count DESC"', 'CssClass="dtg_column"', 'HtmlEntities="false"'));
      $this->dtgCompany->AddColumn(new QDataGridColumnExt('Company Name', '<?= $_ITEM->__toStringWithLink("bluelink") ?>', 'SortByCommand="short_description ASC"', 'ReverseSortByCommand="short_description DESC"', 'CssClass="dtg_column"', 'HtmlEntities=false'));
      $this->dtgCompany->AddColumn(new QDataGridColumnExt('City', '<?= $_ITEM->__toStringCity() ?>', 'Width=200', 'SortByCommand="company__address_id__city ASC"', 'ReverseSortByCommand="company__address_id__city DESC"', 'CssClass="dtg_column"'));
      $this->dtgCompany->AddColumn(new QDataGridColumnExt('State/Province', '<?= $_ITEM->__toStringStateProvince() ?>', 'SortByCommand="company__address_id__state_province_id__short_description ASC"', 'ReverseSortByCommand="company__address_id__state_province_id__short_description DESC"', 'CssClass="dtg_column"'));
      $this->dtgCompany->AddColumn(new QDataGridColumnExt('Country', '<?= $_ITEM->__toStringCountry() ?>', 'SortByCommand="company__address_id__country_id__short_description ASC"', 'ReverseSortByCommand="company__address_id__country_id__short_description DESC"', 'CssClass="dtg_column"'));

      // Add the custom field columns with Display set to false. These can be shown by using the column toggle menu.
      $objCustomFieldArray = CustomField::LoadObjCustomFieldArray(7, false);
      if ($objCustomFieldArray) {
      	foreach ($objCustomFieldArray as $objCustomField) {
      		//Only add the custom field column if the role has authorization to view it.
      		if($objCustomField->objRoleAuthView && $objCustomField->objRoleAuthView->AuthorizedFlag)
      			$this->dtgCompany->AddColumn(new QDataGridColumnExt($objCustomField->ShortDescription, '<?= $_ITEM->GetVirtualAttribute(\''.$objCustomField->CustomFieldId.'\') ?>', 'SortByCommand="__'.$objCustomField->CustomFieldId.' ASC"', 'ReverseSortByCommand="__'.$objCustomField->CustomFieldId.' DESC"','HtmlEntities="false"', 'CssClass="dtg_column"', 'Display="false"'));
      	}
      }

      $this->dtgCompany->SortColumnIndex = 2;
    	$this->dtgCompany->SortDirection = 0;

      $objStyle = $this->dtgCompany->RowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#FFFFFF';
      $objStyle->FontSize = 12;

      $objStyle = $this->dtgCompany->AlternateRowStyle;
      $objStyle->BackColor = '#EFEFEF';

      $objStyle = $this->dtgCompany->HeaderRowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#EFEFEF';
      $objStyle->CssClass = 'dtg_header';

      $this->dtgCompany->SetDataBinder('dtgCompany_Bind');
  	}

	  protected function btnSearch_Click() {
	  	$this->blnSearch = true;
			$this->dtgCompany->PageNumber = 1;
	  }

	  protected function btnClear_Click() {

  		// Set controls to null
	  	$this->txtShortDescription->Text = '';
	  	$this->txtCity->Text = '';
	  	$this->lstStateProvince->SelectedIndex = 0;
	  	$this->lstCountry->SelectedIndex = 0;
	  	$this->ctlAdvanced->ClearControls();

	  	// Set search variables to null
	  	$this->strShortDescription = null;
	  	$this->strCity = null;
	  	$this->intStateProvinceId = null;
	  	$this->intCountryId = null;
	  	$this->strDateModified = null;
	  	$this->strDateModifiedFirst = null;
	  	$this->strDateModifiedLast = null;
	  	$this->blnAttachment = false;
  		if ($this->arrCustomFields) {
	  		foreach ($this->arrCustomFields as $field) {
	  			$field['value'] = null;
	  		}
	  	}
	  	$this->blnSearch = false;
  	}

  	// Display or Hide the advanced search composite control
	  protected function lblAdvanced_Click() {
	  	if ($this->blnAdvanced) {
	  		$this->blnAdvanced = false;
	  		$this->lblAdvanced->Text = 'Advanced Search';

	  		$this->ctlAdvanced->ClearControls();
	  	}
	  	else {
	  		$this->blnAdvanced = true;
	  		$this->lblAdvanced->Text = 'Hide Advanced';
	  	}
	  }

		// Update state/province list when country is selected
		protected function lstCountry_Select($strFormId, $strControlId, $strParameter) {

			// Save the currently selected StateProvince
			$intStateProvinceId = $this->lstStateProvince->SelectedValue;
			// Clear out the items from lstAddress
			$this->lstStateProvince->RemoveAllItems();
			if ($this->lstCountry->SelectedValue) {
				// Load the selected country
				$objCountry = Country::Load($this->lstCountry->SelectedValue);
				// Get all available state/provinces for that company
				$objStateProvinceArray = $objCountry->GetStateProvinceArray();
			}
			else {
				// Or load all addresses for all companies
				$objStateProvinceArray = StateProvince::LoadAll();
			}
			$this->lstStateProvince->AddItem('- Select One -', null);
			if ($objStateProvinceArray) foreach ($objStateProvinceArray as $objStateProvince) {
				$objListItem = new QListItem($objStateProvince->__toString(), $objStateProvince->StateProvinceId);
				if ($intStateProvinceId == $objStateProvince->StateProvinceId)
					$objListItem->Selected = true;
				$this->lstStateProvince->AddItem($objListItem);
				$this->lstStateProvince->Enabled = true;
			}
			else {
				$this->lstStateProvince->Enabled = false;
			}
		}

	  protected function assignSearchValues() {

			$this->strShortDescription = $this->txtShortDescription->Text;
			$this->strCity = $this->txtCity->Text;
			$this->intStateProvinceId = $this->lstStateProvince->SelectedValue;
			$this->intCountryId = $this->lstCountry->SelectedValue;
			$this->strDateModified = $this->ctlAdvanced->DateModified;
			$this->strDateModifiedFirst = $this->ctlAdvanced->DateModifiedFirst;
			$this->strDateModifiedLast = $this->ctlAdvanced->DateModifiedLast;
			$this->blnAttachment = $this->ctlAdvanced->Attachment;

			$this->arrCustomFields = $this->ctlAdvanced->CustomFieldArray;
			if ($this->arrCustomFields) {
				foreach ($this->arrCustomFields as &$field) {
					if ($field['input'] instanceof QListBox) {
						$field['value'] = $field['input']->SelectedValue;
					}
					elseif ($field['input'] instanceof QTextBox) {
						$field['value'] = $field['input']->Text;
					}
				}
			}
	  }


		/**
		 * Mass Action Methods
		 *
		 */

		protected function btnMassDelete_Create(){
			$this->btnMassDelete = new QButton($this);
			$this->btnMassDelete->Name = 'Delete';
			$this->btnMassDelete->Text = 'Delete';
			$this->btnMassDelete->AddAction(new QClickEvent(),
				                            new QConfirmAction("Are you sure you want to delete these objects?"));
			$this->btnMassDelete->AddAction(new QClickEvent(), new QAjaxAction('btnMassDelete_Click'));
			$this->btnMassDelete->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassDelete_Click'));
			$this->btnMassDelete->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function btnMassEdit_Create(){
			$this->btnMassEdit = new QButton($this);
			$this->btnMassEdit->Name = 'Edit';
			$this->btnMassEdit->Text = 'Edit';
			$this->btnMassEdit->AddAction(new QClickEvent(),
				                          new QConfirmAction("Are you sure you want to edit these objects?"));
			$this->btnMassEdit->AddAction(new QClickEvent(), new QAjaxAction('btnMassEdit_Click'));
			$this->btnMassEdit->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassEdit_Click'));
			$this->btnMassEdit->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function lblMassActionError_Create(){
			$this->lblMassActionError = new QLabel($this);
			$this->lblMassActionError->Text = '';
			$this->lblMassActionError->CssClass = "warning";
		}

		protected function dlgMassEdit_Create(){
			$this->dlgMassEdit = new QDialogBox($this);
			$this->dlgMassEdit->AutoRenderChildren = true;
			$this->dlgMassEdit->Width = '440px';
			$this->dlgMassEdit->Overflow = QOverflow::Auto;
			$this->dlgMassEdit->Padding = '10px';
			$this->dlgMassEdit->Display = false;
			$this->dlgMassEdit->BackColor = '#FFFFFF';
			$this->dlgMassEdit->MatteClickable = false;
			$this->dlgMassEdit->CssClass = "modal_dialog";
		}

		protected function dlgDelete_Create(){
			$this->dlgMassDelete = new QDialogBox($this);
			$this->dlgMassDelete->AutoRenderChildren = true;
			$this->dlgMassDelete->Width = '440px';
			$this->dlgMassDelete->Overflow = QOverflow::Auto;
			$this->dlgMassDelete->Padding = '10px';
			$this->dlgMassDelete->Display = false;
			$this->dlgMassDelete->BackColor = '#FFFFFF';
			$this->dlgMassDelete->MatteClickable = false;
			$this->dlgMassDelete->CssClass = "modal_dialog";
		}

		protected function btnMassDeleteConfirm_Create(){
			$this->btnMassDeleteConfirm = new QButton($this->dlgMassDelete);
			$this->btnMassDeleteConfirm->Text = "Confirm";
			$this->btnMassDeleteConfirm->AddAction(new QClickEvent(), new QAjaxAction('btnMassDeleteConfirm_Click'));
			$this->btnMassDeleteConfirm->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassDeleteConfirm_Click'));
			$this->btnMassDeleteConfirm->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function btnMassDeleteCancel_Create(){
			$this->btnMassDeleteCancel = new QButton($this->dlgMassDelete);
			$this->btnMassDeleteCancel->Text = "Cancel";
			$this->btnMassDeleteCancel->AddAction(new QClickEvent(), new QAjaxAction('btnMassDeleteCancel_Click'));
			$this->btnMassDeleteCancel->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassDeleteCancel_Click'));
			$this->btnMassDeleteCancel->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function btnMassDelete_Click($strFormId, $strControlId, $strParameter){
			$items = $this->dtgCompany->getSelected('CompanyId');
			// Show confirm "Are you sure you want to {delete/edit} these objects?"
			if(count($items)>0){
				$this->lblMassActionError->Text = "";
				if (!$this->dlgMassDelete->Display) {
					$arrToBeSkipped = array();
					foreach ($items as $item){
						// Check if any Shipments or Receipts include selected contact
						if(Shipment::hasCompany($item)/*||Receipt::hasCompany($item)*/){
							// append $item to  be skipped
							$arrToBeSkipped[] = $item;
						}
						else{
							$this->arrToDelete[] = $item;
						}
					}
					if(count($arrToBeSkipped)>0){
						// Show dialog box "There are {number} {entity_type}s that are not able to be deleted. Would you like to continue the deletion process, skipping these items?"
						$this->dlgMassDelete->Text =sprintf("There are %s companies that are not able to be deleted. Would you like to continue the deletion process, skipping these items?<br />",implode(",",$arrToBeSkipped));
						$this->dlgMassDelete->ShowDialogBox();
					}
					else{
						if (count($this->arrToDelete)>0){
							foreach ($this->arrToDelete as $intCompanyId){
								Company::Load($intCompanyId)->Delete();
							}
							$this->arrToDelete = array();
							QApplication::Redirect('');
						}
					}

					//	print_r(get_class_methods(get_class($this->dlgDelete)));exit;//$this->dlgDelete->ShowDialogBox() ;
				}
			}
			else{
				$this->lblMassActionError->Text = "You haven't chosen any Company to Delete" ;
			}
		}

		protected function btnMassEdit_Click(){
			$this->lblMassActionError->Text = "";
			$items = $this->dtgCompany->getSelected('CompanyId');
			if(count($items)>0){
				if (!$this->dlgMassEdit->Display) {

					// Create the panel, assigning it to the Dialog Box
					$pnlCompanyEdit = new CompanyMassEditPanel($this->dlgMassEdit, 'dlgMassEdit_Close', $items);
					// Show the dialog box
					$this->dlgMassEdit->ShowDialogBox();
				}
			}
			else{
				$this->lblMassActionError->Text = "You haven't chosen any Company to Edit" ;
			}
		}

		public function dlgMassEdit_Close(){
			$this->dlgMassEdit->HideDialogBox();
		}
		protected function btnMassDeleteConfirm_Click(){

		}
		protected function btnMassDeleteCancel_Click(){
			$this->dlgMassDelete->HideDialogBox();
		}
	}

	// Go ahead and run this form object to generate the page and event handlers, using
	// generated/company_edit.php.inc as the included HTML template file
	CompanyListForm::Run('CompanyListForm', __DOCROOT__ . __SUBDIRECTORY__ . '/contacts/company_list.tpl.php');
?>
