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
$shopUser  = $widget->shopUser;
$clientOptions = \yii\helpers\Json::encode($widget->clientOptions);
?>
<?= \yii\helpers\Html::beginTag('div', $widget->options); ?>

    <? if ($shopUser->discountCoupons) : ?>
        <ul>
        <? foreach ($shopUser->discountCoupons as $discountCoupon) : ?>
            <li>
                <a href='#' title='<?= $discountCoupon->description; ?> <?= $discountCoupon->shopDiscount->name; ?>'>
                    <?= $discountCoupon->coupon; ?>
                </a>
                <a href='#' title='<?= \Yii::t('skeeks/shop-dicount-coupon', 'Remove coupon'); ?>' class="pull-right" onclick="sx.Shop.removeDiscountCoupon(<?= $discountCoupon->id; ?>); return false;">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-times-circle fa-w-16" onclick="sx.Shop.removeBasket('868'); return false;" style="cursor: pointer; width: 12px;height: 12px;transform-origin: 0.625em 0.5625em;overflow: visible;color: gray; margin-top: -13px;">
                                                    <g transform="translate(256 256)" class=""><g transform="translate(64, 32)  scale(1, 1)  rotate(0 0 0)" class=""><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z" transform="translate(-256 -256)" class=""></path></g></g></svg>
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

        <? $widget = \yii\bootstrap\Alert::begin([
            'options' =>
            [
                'class' => 'alert-danger',
                'style' => 'margin-top: 20px; display: none;'
            ]
        ]); ?>
        <? $widget::end(); ?>
        <? $widget = \yii\bootstrap\Alert::begin([
            'options' =>
            [
                'class' => 'alert-success',
                'style' => 'margin-top: 20px; display: none;'
            ]
        ]); ?>
        <? $widget::end(); ?>

<?= \yii\helpers\Html::endTag('div'); ?>