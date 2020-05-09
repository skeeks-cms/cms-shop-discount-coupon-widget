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
use skeeks\cms\shop\models\shopUser;
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
    public $btnSubmitName               = '';
    public $shopUser                   = '';

    public $btnSubmitOptions            = [
        'class' => 'btn btn-gray btn-block',
        'type' => 'submit',
    ];


    public function init()
    {
        parent::init();
        static::registerTranslations();

        $this->options['id'] = $this->id;

        if (!$this->shopUser)
        {
            $this->shopUser = \Yii::$app->shop->shopUser;
            $this->shopUser->loadDefaultValues();
        }

        $this->clientOptions = ArrayHelper::merge($this->clientOptions, [
            'id'        => $this->id,
            'formid'    => $this->formId,
        ]);

        if (!$this->btnSubmitName)
        {
            $this->btnSubmitName = \Yii::t('skeeks/shop-dicount-coupon', 'Get a discount');
        }
    }

    public function run()
    {
        return $this->render($this->viewFile);
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
