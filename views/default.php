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

    <? if ($shopFuser->discountCoupons) : ?>
        <ul>
        <? foreach ($shopFuser->discountCoupons as $discountCoupon) : ?>
            <li>
                <a href='#' title='<?= $discountCoupon->description; ?> <?= $discountCoupon->shopDiscount->name; ?>'>
                    <?= $discountCoupon->coupon; ?>
                </a>
                <a href='#' title='<?= \Yii::t('skeeks/shop-dicount-coupon', 'Remove coupon'); ?>' class="pull-right" onclick="sx.Shop.removeDiscountCoupon(<?= $discountCoupon->id; ?>); return false;">
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
            </li>
        <? endforeach; ?>
        </ul>
    <? endif; ?>
    <p><?= \Yii::t('skeeks/shop-dicount-coupon', 'Please enter your coupon'); ?></p>

    <form id="<?= $widget->formId; ?>" class="nomargin" method="post" data-pjax="false">

    <? $this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.createNamespace('classes.shop', sx);

    sx.classes.shop.ShopDiscountCouponWidget = sx.classes.Component.extend({

        _init: function()
        {},

        /**
        *
        * @returns {*|HTMLElement}
        */
        getJWrapper: function()
        {
            return $('#' + this.get('id'))
        },

        _onDomReady: function()
        {
            var self = this;
            this.JForm = $('form', this.getJWrapper());
            this.JFormName = $('[name=coupon_code]', this.JForm);
            this.JMessageError = $('.alert-danger', this.getJWrapper());
            this.JMessageSuccess = $('.alert-success', this.getJWrapper());

            this.JForm.on('submit', function()
            {
                var ajaxQuery = sx.Shop.createAjaxAddDiscountCoupon(self.JFormName.val());
                ajaxQuery.unbind('success');

                var AjaxHandler = new sx.classes.AjaxHandlerStandartRespose(ajaxQuery, {
                    'enableBlocker' : true,
                    'blockerSelector' : self.getJWrapper()
                });

                AjaxHandler.bind('success', function(e, response)
                {
                    self.JMessageError.hide();
                    self.JMessageSuccess.empty().show().append(response.message);
                    _.delay(function()
                    {
                        sx.Shop.trigger('change');
                    }, 800);

                });

                AjaxHandler.bind('error', function(e, response)
                {
                    self.JMessageSuccess.hide();
                    self.JMessageError.empty().show().append(response.message);
                });

                ajaxQuery.execute();

                return false;
            });
        },

        _onWindowReady: function()
        {

        }
    });

    new sx.classes.shop.ShopDiscountCouponWidget({$clientOptions});
})(sx, sx.$, sx._);
JS
    ); ?>

        <?= \yii\helpers\Html::textInput('coupon_code', null, [
            'class' => 'form-control text-center margin-bottom-10',
            'placeholder' => \Yii::t('skeeks/shop-dicount-coupon', 'Coupon code'),
            'required' => 'required',
        ]); ?>

        <?= \yii\helpers\Html::beginTag('div', $widget->btnSubmitWrapperOptions); ?>
            <?=
                \yii\helpers\Html::button($widget->btnSubmitName, $widget->btnSubmitOptions)
            ?>
        <?= \yii\helpers\Html::endTag('div'); ?>
    </form>

        <? \yii\bootstrap\Alert::begin([
            'options' =>
            [
                'class' => 'alert-danger',
                'style' => 'margin-top: 20px; display: none;'
            ]
        ]); ?>
        <? \yii\bootstrap\Alert::end(); ?>
        <? \yii\bootstrap\Alert::begin([
            'options' =>
            [
                'class' => 'alert-success',
                'style' => 'margin-top: 20px; display: none;'
            ]
        ]); ?>
        <? \yii\bootstrap\Alert::end(); ?>

<?= \yii\helpers\Html::endTag('div'); ?>