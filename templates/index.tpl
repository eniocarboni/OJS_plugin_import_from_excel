{**
 * plugins/importexport/xls2native/templates/index.tpl
 *
 * Copyright (c) 2022 Enio Carboni - Quoll tech
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * List of operations this plugin can perform
 *}
{extends file="layouts/backend.tpl"}

{block name="page"}
	<h1 class="app__pageHeading">
		{$pageTitle|escape}
	</h1>

<script type="text/javascript">
	// Attach the JS file tab handler.
	$(function() {ldelim}
		$('#fromXls2Native').pkpHandler('$.pkp.controllers.TabHandler');
		$('#fromXls2Native').tabs('option', 'cache', true);
	{rdelim});
</script>
<div id="fromXls2Native">
	<ul>
		<li><a href="#xls2native-tab">{translate key="plugins.importexport.xls2native.import"}</a></li>
	</ul>
	<div id="xls2native-tab">
		<script type="text/javascript">
			$(function() {ldelim}
				// Attach the form handler.
				$('#importXmlForm').pkpHandler('$.pkp.controllers.form.FileUploadFormHandler',
					{ldelim}
						$uploader: $('#plupload'),
							uploaderOptions: {ldelim}
								uploadUrl: {plugin_url|json_encode path="uploadImportXML" escape=false},
								baseUrl: {$baseUrl|json_encode}
							{rdelim}
					{rdelim}
				);
			{rdelim});
		</script>
		{if $xlsx2csv }
		<form id="importXmlForm" class="pkp_form" action="{plugin_url path="importBounce"}" method="post">
			{csrf}
			{fbvFormArea id="importForm"}
				{* Container for uploaded file *}
				<input type="hidden" name="temporaryFileId" id="temporaryFileId" value="" />

				{fbvFormArea id="file"}
					{fbvFormSection title="plugins.importexport.xls2native.import.instructions"}
						{include file="controllers/fileUploadContainer.tpl" id="plupload"}
					{/fbvFormSection}
				{/fbvFormArea}

				{fbvFormButtons submitText="plugins.importexport.xls2native.import" hideCancel="true"}
			{/fbvFormArea}
		</form>
		{else}
			<div class="error">
				<p>{translate key="plugins.importexport.xls2native.import.err.cli"}</p>
				<p class="error">{translate key="plugins.importexport.xls2native.import.err.xlsx2csv"}</p>
			</div>
		{/if}
	</div>
</div>

{/block}
