<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 14.10.2016
 */
namespace skeeks\cms\shopDiscountCoupon;

use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\shop\models\ShopBuyer;
use skeeks\cms\shop\models\ShopDiscountCoupon;
use skeeks\cms\shop\models\ShopFuser;
use skeeks\cms\shop\models\ShopOrder;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class ShopDiscountCouponWidget
 *
 * @package skeeks\cms\shopDiscountCoupon
 */
class ShopDiscountCouponWidget extends Widget
{
    public static $autoIdPrefix = 'ShopDiscountCouponWidget';

    public $viewFile = 'default';

    public $options = [];
    public $clientOptions = [];

    public $btnSubmitWrapperOptions     = [];
    public $btnSubmitName               = 'Получить скидку';
    public $btnSubmitOptions            = [
        'class' => 'btn btn-gray btn-block',
        'type' => 'submit',
    ];

    /**
     * @var ShopFuser
     */
    public $shopFuser = null;

    /**
     * @var ShopBuyer
     */
    public $shopBuyer = null;

    public $shopErrors = [];

    public $notSubmitParam = 'sx-not-submit';

    public function init()
    {
        parent::init();
        static::registerTranslations();

        $this->options['id'] = $this->id;

        if (!$this->shopFuser)
        {
            $this->shopFuser = \Yii::$app->shop->shopFuser;
            $this->shopFuser->loadDefaultValues();
        }
        //Покупателя никогда нет
        $this->shopFuser->buyer_id = null;

        $this->clientOptions = ArrayHelper::merge($this->clientOptions, [
            'formid'    => $this->formId,
            'notsubmit' => $this->notSubmitParam,
        ]);

        if (!$this->btnSubmitName)
        {
            $this->btnSubmitName = \Yii::t('skeeks/shop-checkout', 'Submit');
        }
    }

    public function run()
    {
        $rr = new RequestResponse();

        $shopDiscountCoupon = new \skeeks\cms\shop\models\ShopDiscountCoupon();
        $errorMessage = "";
        $successMessage = "";


        if ($rr->isRequestPjaxPost() && \Yii::$app->request->post($this->id))
        {
            try
            {
                $shopDiscountCoupon->load(\Yii::$app->request->post());

                if (!$shopDiscountCoupon->coupon)
                {
                    throw new Exception("Некорректный купон");
                }

                $applyShopDiscountCoupon = ShopDiscountCoupon::find()
                    ->where(['coupon' => $shopDiscountCoupon->coupon])
                    //->andWhere(['is_active' => 1])
                    ->one();

                if (!$applyShopDiscountCoupon) {
                    throw new Exception("Купон не существует или неактивен");
                }

                $discount_coupons = $this->shopFuser->discount_coupons;
                $discount_coupons[] = $applyShopDiscountCoupon->id;
                array_unique($discount_coupons);
                $this->shopFuser->discount_coupons = $discount_coupons;
                $this->shopFuser->save();
                $this->shopFuser->recalculate()->save();
                $successMessage = "Купон успешно применен";

                $shopDiscountCoupon->coupon = '';

            } catch (\Exception $e)
            {
                $errorMessage = $e->getMessage();
            }

        }

        return $this->render($this->viewFile, [
            'shopDiscountCoupon' => $shopDiscountCoupon,
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage
        ]);
    }


    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->id . "-form";
    }


    static public $isRegisteredTranslations = false;

    static public function registerTranslations()
    {
        if (self::$isRegisteredTranslations === false)
        {
            \Yii::$app->i18n->translations['skeeks/shop-dicount-coupon'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@skeeks/cms/shopDiscountCoupon/messages',
                'fileMap' => [
                    'skeeks/shop-dicount-coupon' => 'main.php',
                ],
            ];
            self::$isRegisteredTranslations = true;
        }
    }
}
