<?php

/**
 * @file plugins/importexport/xls2native/index.php
 *
 * Copyright (c) 2022 Enio Carboni - Quoll tech
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_importexport_xls2native
 * @brief Wrapper for convert xls to XML native import/export plugin.
 *
 */

require_once('Xls2NativeImportExportPlugin.inc.php');

return new Xls2NativeImportExportPlugin();


