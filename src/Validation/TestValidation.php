<?php

namespace Validation;

use Symfony\Component\Validator\Constraints as Assert;

class TestValidation extends BaseValidation {
    /**
     *  載入資料
     *
     * @param $data
     * @param $throwOnValidateFail
     */
    public function __construct($data, $throwOnValidateFail = true)
    {
        parent::__construct($data, $throwOnValidateFail);

        $this->rules = [
            '[test]' => [new Assert\Count(1), 'testAAAmessage', 10001],
            '[name][first_name]' => [new Assert\Length(['min' => 1, 'max' => 30]), 'testAAAmessage', 10002],
            '[name][last_name]' => [new Assert\Length(['min' => 1]), 'testAAAmessage', 10003],
        ];

        $this->requiredData = [
            '[name][first_name]',
            '[name][last_name]',
//            '[test]',
        ];
    }
}