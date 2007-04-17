<?php
/*
 * Copyright (c)  2006, Universal Diagnostic Solutions, Inc. 
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

	include('../includes/header.inc.php');
	$this->RenderBegin();
?>
<!-- Begin Header Menu -->
<?php 
	$this->ctlHeaderMenu->Render();
?>
<!-- End Header Menu -->

<!-- Begin Shortcut Menu -->
<?php
	include('shortcut_menu.inc.php');
?>
<!-- End Shortcut Menu -->

		</td>
		<td>
			<img src="../images/empty.gif" width="10">
		</td>
		<td width="100%" valign="top">
		<div class="title">Shipping/Receiving</div>	
		<table>
			<tr>
				<td><?php $this->lstCompany->RenderDesigned(); ?></td>
				<td><?php $this->btnSave->Render(); ?></td>
			</tr>
		</table>
		<br class="item_divider" />
		<div class="title">FedEx&reg; Shipping</div>	
		<table>
			<tr>
				<td><?php $this->lstFedexAccount->RenderDesigned(); ?></td>
				<td><?php $this->btnFedexSave->Render(); ?></td>
			</tr>		
		</table>
		<br class="item_divider" />		
		<div class="title"><?php _t('Shipping Accounts'); ?></div>		
		<br class="item_divider" />	
		<?php $this->btnNew->Render() ?>
		<br class="item_divider" />
		<?php $this->dtgShippingAccount->Render() ?>

	<?php $this->RenderEnd() ?>
	<?php require_once('../includes/footer.inc.php'); ?>	