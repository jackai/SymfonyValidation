<?php

namespace Validation;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductValidation extends BaseValidation {
    /**
     *  載入資料
     *
     * @param $data
     * @param $throwOnValidateFail
     */
    public function __construct($data, $throwOnValidateFail = true)
    {
        parent::__construct($data, $throwOnValidateFail);

        $checkPicture = function ($object, ExecutionContextInterface $context, $payload)
        {
            if (parse_url($object, PHP_URL_SCHEME) !== 'https') {
                $context->buildViolation('url scheme should be https')
                    ->addViolation();
            }
        };

//        驗證欄位
        $this->rules = [
//            '參數名稱' => [驗證規則, 錯誤訊息, 錯誤代碼, 預設值]
            'name' => [new Assert\Length(['min' => 1, 'max' => 30]), 'Invalid name', 10001],
            'picture' => [new Assert\Callback($checkPicture), 'Invalid picture', 10003],
            'price' => [new Assert\GreaterThan(0), 'Invalid price', 10004, 99999],
        ];

//        必填欄位
        $this->requiredData = [
            'name',
            'picture',
        ];
    }
}