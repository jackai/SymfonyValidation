<?php

namespace Validation;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validation;

class BaseValidationBackup {
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
     * @throws \ErrorException
     */
    public function validate()
    {
        // 驗證必填欄位
        if ($this->throwOnValidateFail) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            foreach ($this->requiredData as $path) {
                $p2 = str_replace(".", '][', $path);
                if ($propertyAccessor->getValue($this->input, "[$p2]") === null) {
                    throw new \ErrorException("$p2 is required", $this->rules[$path][2]);
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
     * @throws \ErrorException
     */
    private function recursiveValidate($value, $path = '')
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $recursivePath = $path == '' ? $k : "$path.$k";
                $this->recursiveValidate($v, $recursivePath);
            }
            return;
        }

        if (!array_key_exists($path, $this->rules)) {

            if ($this->throwOnValidateFail) {
                throw new \ErrorException("$path missing validate");
            }

            return;
        }

        $rule = $this->rules[$path];

        $validator = Validation::createValidator();
        $errors = $validator->validate($value, $rule[0]);

        if (count($errors) > 0 && $this->throwOnValidateFail) {
            throw new \ErrorException("$path - {$rule[1]}", $rule[2]);
        }

//        $recursivePath = explode('.', $path);
//        $this->validateData = $this->setValue($this->validateData, $recursivePath, $value);

        $p2 = str_replace(".", '][', $path);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($this->validateData, "[$p2]", $value);
    }

    /**
     * 遞迴填入資料
     *
     * @param $arr
     * @param $recursivePath
     * @param $value
     * @return mixed
     */
//    private function setValue($arr, $recursivePath, $value)
//    {
//        $pathName = $recursivePath[0];
//        if (count($recursivePath) == 1) {
//            $arr[$pathName] = $value;
//
//            return $arr;
//        }
//
//        if (!array_key_exists($pathName, $arr)) {
//            $arr[$pathName] = [];
//        }
//
//        array_shift($recursivePath);
//        $arr[$pathName] = $this->setValue($arr[$pathName], $recursivePath, $value);
//
//        return $arr;
//    }

    /**
     * 遞迴驗證必填欄位
     *
     * @param $arr
     * @param $recursivePath
     * @return boolean
     */
//    private function recursiveValidateRequired($arr, $recursivePath)
//    {
//        $pathName = $recursivePath[0];
//
//        if (count($recursivePath) == 1) {
//            return array_key_exists($pathName, $arr);
//        }
//
//        if (!array_key_exists($pathName, $arr)) {
//            return false;
//        }
//
//        array_shift($recursivePath);
//        return $this->recursiveValidateRequired($arr[$pathName], $recursivePath);
//    }
}
