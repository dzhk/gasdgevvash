<?php

namespace app\services;

use Yii;
use yii\web\ForbiddenHttpException;
use app\models\Post;

class PostService
{
    public function createPost(array $data, string $ip): Post
    {
        $model = new Post();
        $model->load($data, '');
        $model->ip = $ip;

        if (!$model->save()) {
            throw new \RuntimeException('Failed to create post: ' . json_encode($model->errors));
        }

        return $model;
    }

    public function editPost(int $id, string $token, string $message): Post
    {
        $post = $this->getPost($id, $token);

        if (!$post->canEdit()) {
            throw new ForbiddenHttpException('Срок редактирования истёк (12 часов с момента публикации).');
        }

        $post->message = $message;
        if (!$post->save()) {
            throw new \RuntimeException('Failed to update post: ' . json_encode($post->errors));
        }

        return $post;
    }

    public function deletePost(int $id, string $token): void
    {
        $post = $this->getPost($id, $token);

        if (!$post->canDelete()) {
            throw new ForbiddenHttpException('Срок удаления истёк (14 дней с момента публикации).');
        }

        $post->deleted_at = time();
        if (!$post->save(false)) {
            throw new \RuntimeException('Failed to delete post');
        }
    }

    public function getPost(int $id, string $token): Post
    {
        $post = Post::findOne($id);

        if (!$post) {
            throw new \yii\web\NotFoundHttpException('Пост не найден.');
        }

        if ($post->edit_token !== $token && $post->delete_token !== $token) {
            throw new \yii\web\NotFoundHttpException('Неверный токен.');
        }

        return $post;
    }

    public function getPosts(): array
    {
        return Post::find()
            ->where(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    public function canPostAgain(string $ip): bool
    {
        $lastPost = Post::find()
            ->where(['ip' => $ip])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$lastPost) {
            return true;
        }

        $cooldownEnd = $lastPost->created_at + Post::POST_COOLDOWN;
        return time() >= $cooldownEnd;
    }

    public function getCooldownTimeLeft(string $ip): int
    {
        $lastPost = Post::find()
            ->where(['ip' => $ip])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$lastPost) {
            return 0;
        }

        $cooldownEnd = $lastPost->created_at + Post::POST_COOLDOWN;
        return max(0, $cooldownEnd - time());
    }
}