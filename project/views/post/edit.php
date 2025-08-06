<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование сообщения';
?>

<div class="container mt-4">
    <h1 class="mb-4">Редактирование сообщения</h1>

    <?php $form = ActiveForm::begin(); ?>

    <div class="card card-default mb-4">
        <div class="card-body">
            <?= $form->field($model, 'message')->textarea([
                'rows' => 6,
            ])->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-link']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>