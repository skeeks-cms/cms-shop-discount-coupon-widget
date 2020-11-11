<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 14.10.2016
 *
 * @var $this yii\web\View
 * @var $widget \skeeks\cms\shopDiscountCoupon\ShopDiscountCouponWidget
 * @var $discountCoupon \skeeks\cms\shop\models\ShopDiscountCoupon
 */

$widget = $this->context;
$shopUser = $widget->shopUser;
$clientOptions = \yii\helpers\Json::encode($widget->clientOptions);

$isShowForm = true;
?>
<?= \yii\helpers\Html::beginTag('div', $widget->options); ?>

<? if ($shopUser->discountCoupons) : ?>
    <ul class="list-unstyled sx-applyed-coupons">
        <? foreach ($shopUser->discountCoupons as $discountCoupon) : ?>
            <?php if ($discountCoupon->shopDiscount->is_last) {
                $isShowForm = false;
            } ?>
            <li>
                <div class="d-flex flex-row">
                    <div class="" style="width: 100%;">
                        <a href='#' data-toggle="tooltip" title='<?= $discountCoupon->description; ?> <?= $discountCoupon->shopDiscount->name; ?>'
                           class="sx-main-text-color g-color-primary--hover g-text-underline--none--hover">
                            <?= $discountCoupon->coupon; ?>
                        </a>
                    </div>
                    <a href='#' style="font-size: 11px;" data-toggle="tooltip" title='<?= \Yii::t('skeeks/shop-dicount-coupon', 'Remove coupon'); ?>'
                       class="sx-btn-remove-coupon pull-right g-color-primary--hover g-text-underline--none--hover my-auto sx-color-silver"
                       onclick="sx.Shop.removeDiscountCoupon(<?= $discountCoupon->id; ?>); return false;">
                        <i class="hs-icon hs-icon-close"></i>
                    </a>
                </div>
            </li>
        <? endforeach; ?>
    </ul>
<? endif; ?>

    <form id="<?= $widget->formId; ?>" class="sx-discount-coupon-form" method="post" data-pjax="false" <?php echo $isShowForm ?: "style='display: none;'"; ?>>

        <? $this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.createNamespace('classes.shop', sx);

    sx.classes.shop.ShopDiscountCouponWidget = sx.classes.Component.extend({

        /**
        *
        * @returns {*|HTMLElement}
        */
        getJWrapper: function() {
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
                    'allowResponseSuccessMessage' : false,
                    'allowResponseErrorMessage' : false,
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
        }
    });

    new sx.classes.shop.ShopDiscountCouponWidget({$clientOptions});
})(sx, sx.$, sx._);
JS
        ); ?>

        <div class="input-group">
            <?= \yii\helpers\Html::textInput('coupon_code', null, [
                'class'        => 'form-control',
                'placeholder'  => \Yii::t('skeeks/shop-dicount-coupon', 'Coupon code'),
                'autocomplete' => "off",
                'required'     => 'required',
            ]); ?>
            <div class="input-group-append">
                <?=
                \yii\helpers\Html::button($widget->btnSubmitName, $widget->btnSubmitOptions)
                ?>
            </div>
        </div>

    </form>

<? $widget = \yii\bootstrap\Alert::begin([
    'options' =>
        [
            'class' => 'alert-danger',
            'style' => 'margin-top: 20px; display: none;',
        ],
]); ?>
<? $widget::end(); ?>
<? $widget = \yii\bootstrap\Alert::begin([
    'options' =>
        [
            'class' => 'alert-success',
            'style' => 'margin-top: 20px; display: none;',
        ],
]); ?>
<? $widget::end(); ?>

<?= \yii\helpers\Html::endTag('div'); ?>