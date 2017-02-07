<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 14.10.2016
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\shopDiscountCoupon\ShopDiscountCouponWidget */
/* @var $shopDiscountCoupon \skeeks\cms\shop\models\ShopDiscountCoupon */
/* @var $successMessage string */
/* @var $errorMessage string */
$widget     = $this->context;
$shopFuser  = $widget->shopFuser;
$clientOptions = \yii\helpers\Json::encode($widget->clientOptions);
?>
<?= \yii\helpers\Html::beginTag('div', $widget->options); ?>

    <? if (\Yii::$app->shop->shopFuser->discountCoupons) : ?>
        <? foreach (\Yii::$app->shop->shopFuser->discountCoupons as $discountCoupon) : ?>
            <p><?= $discountCoupon->coupon; ?></p>
        <? endforeach; ?>
    <? endif; ?>
    <p>Укажите код вашего купона.</p>

    <?php $form = \yii\widgets\ActiveForm::begin([
        'id'                                            => $widget->formId,
        'enableAjaxValidation'                          => false,
        'enableClientValidation'                        => false,
        'options'                        =>
        [
            'data-pjax' => 'true',
            'class' => 'nomargin',
        ],

        /*'afterValidateCallback'     => new \yii\web\JsExpression(<<<JS
            function(jForm, AjaxQuery)
            {
                var Handler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery);
                /*var Blocker = new sx.classes.AjaxHandlerBlocker(AjaxQuery, {
                    'wrapper' : jForm.closest('.modal-content')
                });

                Handler.bind('success', function()
                {
                    _.delay(function()
                    {
                        /*window.location.reload();
                    }, 1000);
                });
            }
JS
        )*/
    ]); ?>

    <? $this->registerJs(<<<JS
    (function(sx, $, _)
    {
        /*new sx.classes.CheckoutWidget({$clientOptions});*/
    })(sx, sx.$, sx._);
JS
    ); ?>

        <?= $form->field($shopDiscountCoupon, 'coupon')->label(false)->textInput([
            'class' => 'form-control text-center margin-bottom-10',
            'placeholder' => 'Код купона',
            'required' => 'required',
        ]); ?>

        <div style="display: none;">
            <?= \yii\helpers\Html::hiddenInput($widget->id, $widget->id); ?>
        </div>

        <?= \yii\helpers\Html::beginTag('div', $widget->btnSubmitWrapperOptions); ?>
            <?=
                \yii\helpers\Html::button($widget->btnSubmitName, $widget->btnSubmitOptions)
            ?>
        <?= \yii\helpers\Html::endTag('div'); ?>
    <? $form::end(); ?>
    <? if ($errorMessage) : ?>
        <? \yii\bootstrap\Alert::begin([
            'options' =>
            [
                'class' => 'alert-danger',
                'style' => 'margin-top: 20px;'
            ]
        ]); ?>
            <?= $errorMessage; ?>
        <? \yii\bootstrap\Alert::end(); ?>
    <? endif; ?>
    <? if ($successMessage) : ?>
        <? \yii\bootstrap\Alert::begin([
            'options' =>
            [
                'class' => 'alert-success',
                'style' => 'margin-top: 20px;'
            ]
        ]); ?>
            <?= $successMessage; ?>
        <? \yii\bootstrap\Alert::end(); ?>
    <? endif; ?>

<?= \yii\helpers\Html::endTag('div'); ?>