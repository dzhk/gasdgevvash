<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\HtmlPurifier;
use yii\web\ForbiddenHttpException;

/**
 * @property int $id
 * @property string $author
 * @property string $email
 * @property string $message
 * @property string $ip
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property string $edit_token
 * @property string $delete_token
 */
class Post extends ActiveRecord
{
    public const EDIT_WINDOW = 43200; // 12 часов в секундах
    public const DELETE_WINDOW = 1209600; // 14 дней в секундах
    public const POST_COOLDOWN = 0; // 3 минуты в секундах

    public static function tableName()
    {
        return 'post';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['author', 'email', 'message', 'ip'], 'required'],
            ['author', 'string', 'min' => 2, 'max' => 15],
            ['email', 'email'],
            ['message', 'string', 'min' => 5, 'max' => 1000],
            ['message', 'filter', 'filter' => function ($value) {
                return $this->sanitizeMessage($value);
            }],
            ['ip', 'string', 'max' => 45],
            [['edit_token', 'delete_token'], 'string', 'max' => 32],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->edit_token = Yii::$app->security->generateRandomString();
            $this->delete_token = Yii::$app->security->generateRandomString();
        }
        return parent::beforeSave($insert);
    }

    protected function sanitizeMessage($message)
    {
        $message = trim($message);
        if (empty($message)) {
            $this->addError('message', 'Сообщение не может состоять только из пробелов');
            return null;
        }

        return HtmlPurifier::process($message, [
            'HTML.Allowed' => 'b,i,s',
            'AutoFormat.RemoveEmpty' => true,
        ]);
    }

    public static function canPostAgain($ip)
    {
        $lastPost = self::find()
            ->where(['ip' => $ip])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$lastPost) {
            return true;
        }

        $cooldownEnd = $lastPost->created_at + self::POST_COOLDOWN;
        return time() >= $cooldownEnd;
    }

    public static function getCooldownTimeLeft($ip)
    {
        $lastPost = self::find()
            ->where(['ip' => $ip])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$lastPost) {
            return 0;
        }

        $cooldownEnd = $lastPost->created_at + self::POST_COOLDOWN;
        return max(0, $cooldownEnd - time());
    }

    public function canEdit()
    {
        return time() <= ($this->created_at + self::EDIT_WINDOW);
    }

    public function canDelete()
    {
        return time() <= ($this->created_at + self::DELETE_WINDOW);
    }

    public static function maskIp($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) {
                return "{$parts[0]}.{$parts[1]}.**.**";
            }
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            $count = count($parts);
            if ($count >= 4) {
                $masked = array_slice($parts, 0, $count - 4);
                $masked = array_merge($masked, array_fill(0, 4, '****'));
                return implode(':', $masked);
            }
        }
        return $ip;
    }

    public static function getPostsCountByIp($ip)
    {
        return self::find()
            ->where(['ip' => $ip])
            ->count();
    }
}