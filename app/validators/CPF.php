<?php
namespace Myappbook\Validators;

use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation;

class CPF extends Validator implements ValidatorInterface
{
    public function validate(Validation $validator, $attribute)
    {
        if($this->getOption("allowEmpty") && empty($validator->getValue($attribute)))
            return true;
            
        if (! $this->validateCPF($validator->getValue($attribute))) {
            $message = $this->getOption('message');
            if (!$message) {
                $message = 'CPF Inválido';
            }
            $validator->appendMessage(new Message($message, $attribute, 'InvalidCpf'));
            return false;
        }
        return true;
    }
    
    public function validateCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
        $blackList = ['00000000000', '11111111111', '22222222222', '33333333333', '44444444444', '55555555555', '66666666666', '77777777777', '88888888888', '99999999999'];
        if (in_array($cpf, $blackList))
            return false;
        // Validates the size
        if (strlen($cpf) != 11)
            return false;
        // Validates the first verifying digit
        if (! $this->validateDigit(substr($cpf, 0, 10), $cpf{9}))
            return false;
        // Validates the second verifying digit
        return $this->validateDigit($cpf, $cpf{10});
    }

    public function validateDigit($body, $digit)
    {
        for ($i = 0, $j = strlen($body), $sum = 0; $i < (strlen($body)-1); $i++, $j--)
            $sum += $body{$i} * $j;
        $remainder = $sum % 11;
        return $digit == ($remainder < 2 ? 0 : 11 - $remainder);
    }
}