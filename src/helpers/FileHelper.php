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
    public static function scanDoc($dir, $saltKey, $pad = 0)
    {
        $list = [];
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == "..") {
                    continue;
                }

                $filename = $dir . DIRECTORY_SEPARATOR . $entry;
                $key = self::getHash($filename, $saltKey);
                $name = self::getEntryName($filename);

                if (($pos = strrpos($entry, '.')) !== false) {
                    $name = substr($entry, 0, $pos);
                }

                $list[$key] = [
                    'type' => 'file',
                    'pad' => $pad,
                    'name' => $name,
                    'filename' => $entry,
                    'filepath' => $filename,
                ];

                if (is_dir($filename)) {
                    $list[$key]['type'] = 'directory';
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
     * @return string
     */
    protected static function getEntryName($filename)
    {
        $result = $filename;

        $resource = fopen($filename, 'r');
        if ($resource) {
            $heading = fgets($resource);
            if (preg_match("/^#([^#]+)$/", $heading, $matches)) {
                $result = trim($matches[1]);
            }

            fclose($resource);
        }

        return $result;
    }
}
