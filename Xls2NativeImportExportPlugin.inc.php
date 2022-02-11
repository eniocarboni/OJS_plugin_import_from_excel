<?php

/**
 * @file plugins/importexport/xls2native/Xls2NativeImportExportPlugin.inc.php
 *
 * Copyright (c) 2022 Enio Carboni - Quoll tech
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Xls2NativeImportExportPlugin
 * @ingroup plugins_importexport_xls2native
 *
 * @brief Wrapper for convert xls to XML native import/export plugin.
 */

import('lib.pkp.classes.plugins.ImportExportPlugin');

class Xls2NativeImportExportPlugin extends ImportExportPlugin {

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path, $mainContextId);
		$this->addLocaleData();
	//	$this->import('NativeImportExportDeployment');
		return $success;
	}

	/**
	 * Get the name of this plugin. The name must be unique within
	 * its category.
	 * @return String name of plugin
	 */
	function getName() {
		return 'Xls2NativeImportExportPlugin';
	}

	/**
	 * Get the display name.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.importexport.xls2native.displayName');
	}

	/**
	 * Get the display description.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.importexport.xls2native.description');
	}

	/**
	 * @copydoc ImportExportPlugin::getPluginSettingsPrefix()
	 */
	function getPluginSettingsPrefix() {
		return 'xls2native';
	}

	/**
	 * Display the plugin.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function display($args, $request) {
		parent::display($args, $request);
		$context = $request->getContext();
		switch (array_shift($args)) {
			case 'index':
			case '':
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->assign(array(
					'xlsx2csv' 	=> Config::getVar('cli', 'xlsx2csv')!='',
				));
				$templateMgr->display($this->getTemplateResource('index.tpl'));
				break;
			case 'uploadImportXML':
				$user = $request->getUser();
				import('lib.pkp.classes.file.TemporaryFileManager');
				$temporaryFileManager = new TemporaryFileManager();
				$temporaryFile = $temporaryFileManager->handleUpload('uploadedFile', $user->getId());
				if ($temporaryFile) {
					$json = new JSONMessage(true);
					$json->setAdditionalAttributes(array(
						'temporaryFileId' => $temporaryFile->getId()
					));
				} else {
					$json = new JSONMessage(false, __('common.uploadFailed'));
				}
				header('Content-Type: application/json');
				return $json->getString();
			case 'importBounce':
				if (!$request->checkCSRF()) throw new Exception('CSRF mismatch!');
				$json = new JSONMessage(true);
				$json->setEvent('addTab', array(
					'title' => __('plugins.importexport.xls2native.results'),
					'url' => $request->url(null, null, null, array('plugin', $this->getName(), 'import'), array('temporaryFileId' => $request->getUserVar('temporaryFileId'))),
				));
				header('Content-Type: application/json');
				return $json->getString();
			case 'import':
				$json = new JSONMessage(true,
					'<a href="'.$request->url(null, null, null, array('plugin', $this->getName(), 'import2'), array('temporaryFileId' => $request->getUserVar('temporaryFileId'))) .'">'.__('plugins.importexport.xls2native.clickdownload').'</a>');
				header('Content-Type: application/json');
				return $json->getString();
			case 'import2':
				$temporaryFileId = $request->getUserVar('temporaryFileId');
				$temporaryFileDao = DAORegistry::getDAO('TemporaryFileDAO'); /* @var $temporaryFileDao TemporaryFileDAO */
				$user = $request->getUser();
				$temporaryFile = $temporaryFileDao->getTemporaryFile($temporaryFileId, $user->getId());
				if (!$temporaryFile) {
					$json = new JSONMessage(true, __('plugins.importexport.xls2native.uploadFile'));
					header('Content-Type: application/json');
					return $json->getString();
				}
				$temporaryFilePath = $temporaryFile->getFilePath();
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename=from-xlsx-to-xml-' . strftime('%Y-%m-%d') . '.xml');
				header('Content-Type: text/xml; charset=utf-8');
				$xlsx2csv=Config::getVar('cli', 'xlsx2csv');
				passthru(strtr($xlsx2csv, array('{xlsx}' => $temporaryFilePath)), $returnValue);
				break;
			default:
				$dispatcher = $request->getDispatcher();
				$dispatcher->handle404();
		}
	}


	/**
	 * @copydoc PKPImportExportPlugin::usage
	 */
	function usage($scriptName) {
		echo __('plugins.importexport.xls2native.cliUsage', array(
			'scriptName' => $scriptName,
			'pluginName' => $this->getName()
		)) . "\n";
	}

	/**
	 * @see PKPImportExportPlugin::executeCLI()
	 */
	function executeCLI($scriptName, &$args) {
		$xlsxFile = array_shift($args);

		if (!$xlsxFile) {
			$this->usage($scriptName);
			return;
		}
		$xlsx2csv=Config::getVar('cli', 'xlsx2csv');
		passthru(strtr($xlsx2csv, array('{xlsx}' => $xlsxFile)), $returnValue);
	}

}
