<?php

namespace miolae\yii2\doc\helpers;

use yii\base\InvalidConfigException;

class FileHelper
{
    /**
     * @param string $dir
     * @param int    $pad
     *
     * @return array
     * @throws InvalidConfigException
     */
    public static function scanDoc($directory, $pad = 0)
    {
        $list = [];

        if ($pad === 0 && file_exists($directory . DIRECTORY_SEPARATOR . 'README.md')) {
            $list[''] = [
                'type' => 'file',
                'pad' => $pad,
                'name' => '',
                'filename' => '',
                'filepath' => $directory . DIRECTORY_SEPARATOR . 'README.md',
                'url' => '',
            ];
        }

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
                    $list = array_merge($list, self::scanDoc($filename, $pad + 1));
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
