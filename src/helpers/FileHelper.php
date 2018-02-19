<?php

namespace macfly\yii2\doc\helpers;

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

                $file = $dir . DIRECTORY_SEPARATOR . $entry;
                $key = self::getHash($file, $saltKey);
                $name = $entry;

                if (($pos = strrpos($entry, '.')) !== false) {
                    $name = substr($entry, 0, $pos);
                }

                $list[$key] = [
                    'type' => 'file',
                    'pad' => $pad,
                    'name' => $name,
                    'filename' => $entry,
                    'filepath' => $file,
                ];

                if (is_dir($file)) {
                    $list[$key]['type'] = 'directory';
                    $list = array_merge($list, self::scanDoc($file, $saltKey, $pad + 1));
                }
            }
            closedir($handle);
        }
        return $list;
    }
}
