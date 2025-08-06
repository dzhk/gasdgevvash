
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Post;

$this->title = 'StoryValut';
?>

<div class="container mt-4">
    <h1 class="mb-4">StoryValut</h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $messages): ?>
        <?php foreach ((array)$messages as $message): ?>
            <div class="alert alert-<?= $type ?>"><?= $message ?></div>
        <?php endforeach ?>
    <?php endforeach ?>

    <?php $form = ActiveForm::begin([
        'action' => ['post/create'],
        'options' => ['class' => 'mb-4'],
    ]); ?>

    <div class="card card-default mb-4">
        <div class="card-body">
            <h5 class="card-title">Новое сообщение</h5>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'author')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ваше имя',
                    ])->label(false) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput([
                        'type' => 'email',
                        'placeholder' => 'Ваш email',
                    ])->label(false) ?>
                </div>
            </div>

            <?= $form->field($model, 'message')->textarea([
                'rows' => 4,
                'placeholder' => 'Ваше сообщение...',
            ])->label(false) ?>

            <?= \app\widgets\CaptchaWidget::widget() ?>

            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="mb-3">
        <small class="text-muted">Показаны записи 1-<?= count($posts) ?> из <?= count($posts) ?>.</small>
    </div>

    <?php foreach ($posts as $post): ?>
        <div class="card card-default mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= Html::encode($post->author) ?></h5>
                <p><?= nl2br(Html::encode($post->message)) ?></p>
                <p>
                    <small class="text-muted">
                        <?= Yii::$app->formatter->asRelativeTime($post->created_at) ?> |
                        <?= Post::maskIp($post->ip) ?> |
                        <?= Yii::t('app', '{n, plural, =0{нет постов} one{# пост} few{# поста} many{# постов} other{# поста}}', [
                            'n' => Post::getPostsCountByIp($post->ip),
                        ]) ?>
                    </small>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>