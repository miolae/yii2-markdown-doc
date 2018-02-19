<?php

namespace miolae\yii2\doc\helpers;

use Yii;
use yii\base\InvalidConfigException;

class FileHelper
{
    /**
     * @param $data
     * @param $saltKey
     *
     * @return string
     * @throws InvalidConfigException
     */
    public static function getHash($data, $saltKey)
    {
        $hash = hash_hmac(Yii::$app->getSecurity()->macHash, $data, $saltKey, false);

        if (!$hash) {
            throw new InvalidConfigException('Failed to generate HMAC with hash algorithm: ' . Yii::$app->getSecurity()->macHash);
        }

        return $hash;
    }

    /**
     * @param     $dir
     * @param     $saltKey
     * @param int $pad
     *
     * @return array
     * @throws InvalidConfigException
     */
    public static function scanDoc($directory, $saltKey, $pad = 0)
    {
        $list = [];
        if ($handle = opendir($directory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry === "." || $entry === ".." || $entry === 'README.md') {
                    continue;
                }

                $filename = $directory . DIRECTORY_SEPARATOR . $entry;

                $code = self::getEntryCode($entry);
                if (!$name = self::getEntryName($filename)) {
                    $name = $code;
                }

                $arKey = [];
                $arPath = explode(DIRECTORY_SEPARATOR, $directory);
                for ($i = 0; $i < $pad; $i++) {
                    $arKey[] = array_pop($arPath);
                }
                $arKey = array_reverse($arKey);
                $arKey[] = $code;
                $key = implode('/', $arKey);

                $list[$key] = [
                    'type' => 'file',
                    'pad' => $pad,
                    'name' => $name,
                    'filename' => $entry,
                    'filepath' => $filename,
                    'url' => $key,
                ];

                if (is_dir($filename)) {
                    $list[$key]['type'] = 'directory';
                    $list[$key]['filepath'] .= DIRECTORY_SEPARATOR . 'README.md';
                    $list = array_merge($list, self::scanDoc($filename, $saltKey, $pad + 1));
                }
            }
            closedir($handle);
        }
        return $list;
    }

    /**
     * @param string $filename
     *
     * @return false|string
     */
    protected static function getEntryName($filename)
    {
        $result = false;

        if (is_dir($filename)) {
            $filename .= DIRECTORY_SEPARATOR . 'README.md';
        }

        if (file_exists($filename) && $resource = fopen($filename, 'r')) {
            $heading = fgets($resource);
            if (preg_match("/^#([^#]+)$/", $heading, $matches)) {
                $result = trim($matches[1]);
            }

            fclose($resource);
        }

        return $result;
    }

    /**
     * @param $entry
     *
     * @return bool|string
     */
    public static function getEntryCode($entry)
    {
        $code = $entry;
        if (($pos = strrpos($code, '.')) !== false) {
            $code = substr($code, 0, $pos);
        }
        return $code;
    }
}
