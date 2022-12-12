<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class chieff_currencies extends CModule {

    public $arResponse = [
        "STATUS" => true,
        "MESSAGE" => ""
    ];

    public function setResponse($status, $message = "") {
        $this->arResponse["STATUS"] = $status;
        $this->arResponse["MESSAGE"] = $message;
    }

    function __construct() {

        $arModuleVersion = array();

        require (__DIR__."/version.php");

        $this->exclusionAdminFiles = array(
            '..',
            '.',
            'menu.php',
            'operation_description.php',
            'task_description.php'
        );

        $this->MODULE_ID = "chieff.currencies";

        $this->COMPONENTS_PATH = $_SERVER["DOCUMENT_ROOT"] . "/local/components";

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("CHIEFF_CURRENCIES_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("CHIEFF_CURRENCIES_MODULE_DESCRIPTION");

        $this->PARTNER_NAME = Loc::getMessage("CHIEFF_CURRENCIES_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("CHIEFF_CURRENCIES_PARTNER_URI");

        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
        $this->MODULE_GROUP_RIGHTS = "Y";

    }

    function installDB() {
        Loader::includeModule($this->MODULE_ID);
        if (!Application::getConnection(\chieff\currencies\CurrenciesTable::getConnectionName())->isTableExists(Base::getInstance("\chieff\currencies\CurrenciesTable")->getDBTableName()))
            Base::getInstance("\chieff\currencies\CurrenciesTable")->createDbTable();
    }

    function installFiles() {
        $this->unInstallFiles();
        $resMsg = "";
        $res = CopyDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true,
            true
        );
        if (!$res)
            $resMsg = Loc::getMessage("CHIEFF_CURRENCIES_INSTALL_ERROR_FILES_ADM");
        if (!is_dir($this->COMPONENTS_PATH))
            mkdir($this->COMPONENTS_PATH, 0777, true);
        $res = CopyDirFiles(
            __DIR__ . "/components",
            $this->COMPONENTS_PATH,
            true,
            true
        );
        if (!$res)
            $resMsg = ($resMsg) ? $resMsg . "; " . Loc::getMessage("CHIEFF_CURRENCIES_INSTALL_ERROR_FILES_COM") : Loc::getMessage("CHIEFF_CURRENCIES_INSTALL_ERROR_FILES_COM");
        if ($resMsg) {
            $this->setResponse(false, $resMsg);
            return false;
        }
        $this->setResponse(true);
        return true;
    }

    function installAgents() {
        \CAgent::AddAgent(
            "\chieff\currencies\Agent::currenciesGetter();",
            $this->MODULE_ID,
            "N",
            86400,
            "",
            "Y",
            "",
            1
        );
    }

    function DoInstall() {
        global $APPLICATION;
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
        $this->installEvents();
        $this->installAgents();
        if (!$this->installFiles())
            $APPLICATION->ThrowException($this->arResponse["MESSAGE"]);
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies/install/step.php"))
            $APPLICATION->IncludeAdminFile(Loc::getMessage("CHIEFF_CURRENCIES_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies/install/step.php");
        else
            $APPLICATION->IncludeAdminFile(Loc::getMessage("CHIEFF_CURRENCIES_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/chieff.currencies/install/step.php");
    }

    function unInstallFiles() {
        $res = true;
        $resMsg = "";
        DeleteDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
        );
        if (is_dir($this->COMPONENTS_PATH . "/" . $this->MODULE_ID))
            $res = DeleteDirFilesEx("/local/components/" . $this->MODULE_ID);
        if (!$res)
            $resMsg = Loc::getMessage("CHIEFF_CURRENCIES_UNINSTALL_ERROR_FILES_COM");
        if ($resMsg) {
            $this->setResponse(false, $resMsg);
            return false;
        }
        $this->setResponse(true);
        return true;
    }

    function unInstallDB() {
        Loader::includeModule($this->MODULE_ID);
        Application::getConnection(\chieff\currencies\CurrenciesTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\chieff\currencies\CurrenciesTable")->getDBTableName());
    }

    function unInstallAgents() {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    function DoUninstall() {
        global $APPLICATION;
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        if ($request["step"] < 2) {
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies/install/unstep1.php"))
                $APPLICATION->IncludeAdminFile(Loc::getMessage("CHIEFF_CURRENCIES_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies/install/unstep1.php");
            else
                $APPLICATION->IncludeAdminFile(Loc::getMessage("CHIEFF_CURRENCIES_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/chieff.currencies/install/unstep1.php");
        } elseif ($request["step"] == 2) {
            $this->unInstallAgents();
            if ($request["savedata"] != "Y")
                $this->unInstallDB();
            if (!$this->unInstallFiles())
                $APPLICATION->ThrowException($this->arResponse["MESSAGE"]);
            ModuleManager::unRegisterModule($this->MODULE_ID);
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies/install/unstep2.php"))
                $APPLICATION->IncludeAdminFile(Loc::getMessage("CHIEFF_CURRENCIES_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies/install/unstep2.php");
            else
                $APPLICATION->IncludeAdminFile(Loc::getMessage("CHIEFF_CURRENCIES_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/chieff.currencies/install/unstep2.php");
        }
    }

    function GetModuleRightList() {
        return array(
            "reference_id" => Array("D", "K", "S", "W"),
            "reference" => Array(
                "[D] " . Loc::getMessage("CHIEFF_CURRENCIES_DENIED"),
                "[K] " . Loc::getMessage("CHIEFF_CURRENCIES_READ_COMPONENT"),
                "[S] " . Loc::getMessage("CHIEFF_CURRENCIES_WRITE_SETTINGS"),
                "[W] " . Loc::getMessage("CHIEFF_CURRENCIES_FULL"),
            )
        );
    }

}

?>