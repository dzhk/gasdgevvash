<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Подтверждение удаления';
?>

<div class="container mt-4">
    <h1 class="mb-4">Подтверждение удаления</h1>

    <div class="card card-default mb-4">
        <div class="card-body">
            <p>Вы уверены, что хотите удалить это сообщение?</p>
            <p><strong><?= nl2br(Html::encode($model->message)) ?></strong></p>

            <?php $form = ActiveForm::begin(); ?>

            <div class="form-group">
                <?= Html::submitButton('Удалить', ['class' => 'btn btn-danger']) ?>
                <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-link']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>