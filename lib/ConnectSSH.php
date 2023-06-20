<?php

namespace Medialine\Deploy;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);


class ConnectSSH
{

    const MODULE_ID = 'medialine.deploy';

    private $arDefaultOptions = [];


    public function __construct()
    {
        Loader::includeModule(self::MODULE_ID);

        $this->arDefaultOptions = Option::getDefaults(self::MODULE_ID);
    }

    public function testConnect()
    {
        system('ssh -p ' .$this->arDefaultOptions['DEV_REMOTE_PORT']. ' ' . $this->arDefaultOptions['DEV_REMOTE_HOST'], $status);
        return $status;
    }

    public function testDB()
    {
        system('ssh -p ' .$this->arDefaultOptions['DEV_REMOTE_PORT']. ' ' . $this->arDefaultOptions['DEV_REMOTE_HOST'] . ' "mysql -u '.$this->arDefaultOptions['DEV_DATABASE_LOGIN'].' -p'.$this->arDefaultOptions['DEV_DATABASE_PASSWORD'].'"', $status);
        return $status;
    }

    public function testLocalDB()
    {
        system('mysql -u '.$this->arDefaultOptions['DEV_DATABASE_LOGIN'].' -p'.$this->arDefaultOptions['DEV_DATABASE_PASSWORD'], $status);
        return $status;
    }


    public function exec($cmd) {

//        if (!($stream = ssh2_exec($this->connection, $cmd))) {
//
//            throw new Exception('SSH command failed');
//
//        }

//        stream_set_blocking($stream, true);
//
//        $data = "";
//
//        while ($buf = fread($stream, 4096)) {
//
//            $data .= $buf;
//
//        }
//
//        fclose($stream);
//
//        return $data;

    }

    public function disconnect() {

        $this->exec('echo "EXITING" && exit;');

        $this->connection = null;

    }

    public function __destruct() {

        $this->disconnect();

    }
}