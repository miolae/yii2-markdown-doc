<?php


namespace miolae\yii2\doc\helpers;


use miolae\yii2\doc\models\Searchable;

class SearchHelper
{
    /**
     * @param $list
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function index($list)
    {
        /** @var Searchable[] $dbList */
        $dbList = Searchable::find()->indexBy('path')->all();

        foreach ($list as $path => $item) {
            if (isset($dbList[$path])) {
                $searchable =  $dbList[$path];
                unset($dbList[$path]);
            } else {
                $searchable = new Searchable();;
            }

            if ($searchable->changed_at !== $item['timestamp']) {
                $content = file_get_contents($item['filepath']);
                $searchable->setAttributes([
                    'path' => $path,
                    'changed_at' => $item['timestamp'],
                    'title' => self::getTitle($item['filepath']),
                    'content' => self::filterContent($content),
                ]);

                $searchable->save();
            }
        }

        foreach ($dbList as $searchable) {
            $searchable->delete();
        }
    }

    /**
     * @param $filename
     *
     * @return string
     */
    private static function getTitle($filename)
    {
        $title = FileHelper::getEntryName($filename);

        return self::filterContent($title);
    }

    /**
     * @param string $filepath
     *
     * @return string
     */
    private static function filterContent($content)
    {
        $content = preg_replace("/[^A-Za-zА-Яа-я\d\s]/u", ' ', $content);
        $content = preg_replace("/\s+/u", ' ', $content);

        $content = mb_strtolower($content);

        $content = explode(' ', $content);
        $content = array_filter($content, function($word) {
            return $word === 'git' || mb_strlen($word) > 3;
        });
        $content = implode(' ', $content);

        return $content;
    }
}
