<?php

namespace app\services;

use Yii;
use app\models\Post;

class MailService
{
    public function sendPostNotification(Post $post): void
    {
        $editUrl = Yii::$app->urlManager->createAbsoluteUrl([
            'post/edit',
            'id' => $post->id,
            'token' => $post->edit_token,
        ]);

        $deleteUrl = Yii::$app->urlManager->createAbsoluteUrl([
            'post/confirm-delete',
            'id' => $post->id,
            'token' => $post->delete_token,
        ]);

        $message = Yii::$app->mailer->compose()
            ->setFrom('StoryValut@StoryValut.com')
            ->setTo($post->email)
            ->setSubject('Ваше сообщение на StoryValut')
            ->setTextBody("
                Спасибо за ваше сообщение!
                
                Вы можете редактировать сообщение в течение 12 часов:
                $editUrl
                
                Или удалить его в течение 14 дней:
                $deleteUrl
            ");

        $message->send();
    }
}