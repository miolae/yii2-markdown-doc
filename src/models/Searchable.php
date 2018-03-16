<?php

namespace miolae\yii2\doc\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property string path
 * @property string content
 * @property int    changed_at
 * @property int    created_at
 * @property int    updated_at
 */
class Searchable extends ActiveRecord
{
    public static function tableName()
    {
        return 'md_doc_search';
    }

    public function rules()
    {
        return [
            [['path', 'changed_at', 'content', 'title'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }
}
