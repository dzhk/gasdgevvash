<?php
/** @var $widget CaptchaWidget */

use app\widgets\CaptchaWidget;

$widget->registerAssets();
?>
<div class="form-group">
    <label>Код с картинки: <strong>sudke</strong></label>
    <input type="text" name="captcha" class="form-control" required>
    <small class="form-text text-muted">Введите код с картинки выше</small>
</div>