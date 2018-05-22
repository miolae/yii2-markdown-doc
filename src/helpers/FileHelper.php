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
                $isDotDir = $entry === '.' || $entry === '..';
                $isReadme = $entry === 'README.md';
                $isntMenu = !self::isMenuItem($entry);
                if ($isDotDir || $isReadme || $isntMenu) {
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
        if (($pos = strrpos($code, 'README.md')) === false) {
            $pos = strrpos($code, '.md');
        }

        if ($pos !== false) {
            $code = substr($code, 0, $pos);
        }
        $code = rtrim($code, '/');

        return $code;
    }

    public static function getEntryUrl($entry)
    {
        $url = self::getEntryCode($entry);

        $dots = [];
        $parts = explode('/', $url);
        foreach ($parts as $key => $item) {
            if ($item === '..') {
                if (isset($dots[$key - 1])) {
                    $key--;
                }

                $dots[$key]++;
            }
        }

        $dotsNew = [];
        $sum = 0;

        foreach ($dots as $key => $count) {
            $key = $key - $sum;
            if ($key > 0) {
                $dotsNew[$key] = $count;
            }
            $sum += $count;
        }
        $dots = $dotsNew;

        $parts = array_values(array_filter($parts, function($item) {return $item !== '..';}));
        while (!empty($dots)) {
            $partsNew = [];
            $key = key($dots);
            $shift = current($dots);
            unset($dots[$key]);

            foreach ($parts as $index => $part) {
                if ($index < $key - $shift) {
                    $partsNew[$index] = $part;
                } elseif ($index >= $key) {
                    $partsNew[$index - $shift] = $part;
                }
            }

            $parts = $partsNew;

            $dotsNew = [];
            foreach ($dots as $key => $count) {
                $key = $key - $shift;
                $dotsNew[$key] = $count;
            }
            $dots = $dotsNew;
        }

        return implode('/', $parts);
    }

    public static function isMenuItem($entry)
    {
        return strpos($entry, '.') === false || stripos($entry, '.md') === strlen($entry) - 3;
    }
}
