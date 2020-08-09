<?php

namespace Validation;

use Symfony\Component\Validator\Validation;

class BaseValidation {
    public $input = [];
    public $rules = [];
    public $validateData = [];
    public $validateCamelData = [];
    public $requiredData = [];
    public $throwOnValidateFail;
    public $throwOnMissingValidate;

    /**
     *  載入資料
     *
     * @param $data
     * @param $throwOnValidateFail
     * @param $throwOnMissingValidate
     */
    public function __construct($data, $throwOnValidateFail = true, $throwOnMissingValidate = true)
    {
        $this->throwOnValidateFail = $throwOnValidateFail;
        $this->throwOnMissingValidate = $throwOnMissingValidate;
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
        $this->checkRequired();

        // 填充預設值
        $this->fillingData();

        // 驗證欄位值
        $this->recursiveValidate($this->input);

        return $this->validateData;
    }

    /**
     * 驗證必填欄位
     * @throws \ErrorException
     */
    private function checkRequired()
    {
        if ($this->throwOnValidateFail) {
            foreach ($this->requiredData as $path) {
                if (!$this->recursiveValidateRequired($this->input, explode('.', $path))) {
                    throw new \ErrorException("$path is required", $this->rules[$path][2]);
                }
            }
        }
    }

    /**
     * 填充預設值
     */
    private function fillingData()
    {
        foreach ($this->rules as $path => $value) {
            if (!$this->recursiveValidateRequired($this->input, explode('.', $path)) && isset($value[3])) {
                $recursivePath = explode('.', $path);
                $this->input = $this->setValue($this->input, $recursivePath, $value[3]);
            }
        }
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

            if ($this->throwOnMissingValidate) {
                throw new \ErrorException("$path missing validate");
            }

            return;
        }

        $rule = $this->rules[$path];

        $validator = Validation::createValidator();
        $errors = $validator->validate($value, $rule[0]);

        if (count($errors) > 0 && $this->throwOnValidateFail) {
            throw new \ErrorException("$path - {$rule[1]} - {$errors[0]->getMessage()}", $rule[2]);
        }

        $recursivePath = explode('.', $path);
        $this->validateData = $this->setValue($this->validateData, $recursivePath, $value);

        $recursivePath = array_map(function ($v) {return $this->camelize($v);}, $recursivePath);
        $this->validateCamelData = $this->setValue($this->validateData, $recursivePath, $value);
    }

    /**
     * 遞迴填入資料
     *
     * @param $arr
     * @param $recursivePath
     * @param $value
     * @return mixed
     */
    private function setValue($arr, $recursivePath, $value)
    {
        $pathName = $recursivePath[0];
        if (count($recursivePath) == 1) {
            $arr[$pathName] = $value;

            return $arr;
        }

        if (!array_key_exists($pathName, $arr)) {
            $arr[$pathName] = [];
        }

        array_shift($recursivePath);
        $arr[$pathName] = $this->setValue($arr[$pathName], $recursivePath, $value);

        return $arr;
    }

    /**
     * 遞迴驗證必填欄位
     *
     * @param $arr
     * @param $recursivePath
     * @return boolean
     */
    private function recursiveValidateRequired($arr, $recursivePath)
    {
        $pathName = $recursivePath[0];

        if (count($recursivePath) == 1) {
            return array_key_exists($pathName, $arr);
        }

        if (!array_key_exists($pathName, $arr)) {
            return false;
        }

        array_shift($recursivePath);
        return $this->recursiveValidateRequired($arr[$pathName], $recursivePath);
    }

    /**
     * 字串轉駝峰
     *
     * @param $uncamelizedWords
     * @return string
     */
    private function camelize($uncamelizedWords)
    {
        return lcfirst(implode(array_map('ucfirst', explode('_', $uncamelizedWords))));
    }

    /**
     * 將參數設定到Entity
     * @param $entity
     * @param $data
     * @return mixed
     */
    public function setEntityValue($entity)
    {
        foreach ($this->validateData as $k => $v) {
            $entity->{$this->camelize('set_' . $k)}($v);
        }

        return $entity;
    }
}
