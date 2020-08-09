<?php

namespace Validation;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validation;

class BaseValidation {
    public $input = [];
    public $rules = [];
    public $validateData = [];
    public $requiredData = [];
    public $throwOnValidateFail;

    /**
     *  載入資料
     *
     * @param $data
     * @param $throwOnValidateFail
     */
    public function __construct($data, $throwOnValidateFail = true)
    {
        $this->throwOnValidateFail = $throwOnValidateFail;
        $this->input = $data;
    }

    /**
     * 驗證資料
     *
     * @return mixed
     * @throws \Exception
     */
    public function validate()
    {
        // 驗證必填欄位
        if ($this->throwOnValidateFail) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            foreach ($this->requiredData as $path) {
                if ($propertyAccessor->getValue($this->input, $path) === null) {
                    throw new \Exception("$path is required", $this->rules[$path][2]);
                }
            }
        }

        // 驗證欄位值
        $this->recursiveValidate($this->input);

        return $this->validateData;
    }

    /**
     * 遞迴驗證資料
     *
     * @param $value
     * @param string $path
     * @throws \Exception
     */
    private function recursiveValidate($value, $path = '')
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->recursiveValidate($v, sprintf("%s[%s]", $path, $k));
            }
            return;
        }

        if (!array_key_exists($path, $this->rules)) {

            if ($this->throwOnValidateFail) {
                throw new \Exception("$path missing validate");
            }

            return;
        }

        $rule = $this->rules[$path];

        $validator = Validation::createValidator();
        $errors = $validator->validate($value, $rule[0]);

        if (count($errors) > 0 && $this->throwOnValidateFail) {
            throw new \Exception("$path - {$rule[1]}", $rule[2]);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($this->validateData, $path, $value);
    }
}
