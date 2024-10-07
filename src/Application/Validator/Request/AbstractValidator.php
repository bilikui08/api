<?php

namespace Src\Application\Validator\Request;

use Src\Infrastructure\Http\Request;

abstract class AbstractValidator
{
    public const NOT_BLANK = 'NotBlank';
    public const MAX_LENGTH = 'MaxLength';
    public const TARJETA = 'numero_tarjeta';
    protected array $validators = [];
    private array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    abstract public function isValid(Request $request): bool;

    protected function validate(string $fieldName, ?string $value): void
    {
        if (isset($this->validators[$fieldName])) {
            $validatorDefinition = $this->validators[$fieldName];
            foreach ($validatorDefinition as $validator) {

                if (self::NOT_BLANK === $validator['key']) {
                    if (null === $value || empty($value)) {
                        $this->errors[] = [
                            'field' => $fieldName,
                            'message' => $validator['message'],
                        ];
                        break;
                    }
                }

                if (self::MAX_LENGTH === $validator['key']) {
                    if (strlen($value) > $validator['value']) {
                        $this->errors[] = [
                            'field' => $fieldName,
                            'message' => str_replace('{value}', $validator['value'], $validator['message']),
                        ];
                    }
                }

                if (self::TARJETA === $validator['key']) {
                    if (!$this->validarTarjetaCredito($value)) {
                        $this->errors[] = [
                            'field' => $fieldName,
                            'message' => $validator['message'],
                        ];
                    }
                }
            }
        }
    }
    private function validarTarjetaCredito($numeroTarjeta): bool 
    {
        // Eliminamos espacios en blanco o guiones del número
        $tarjeta = preg_replace('/\D/', '', $numeroTarjeta);
    
        // Verificamos que el número esté compuesto solo por dígitos
        if (!ctype_digit($tarjeta)) {
            return false;
        }
    
        $suma = 0;
        $alternar = false;
    
        // Iteramos sobre los dígitos de derecha a izquierda
        for ($i = strlen($tarjeta) - 1; $i >= 0; $i--) {
            $digito = (int) $tarjeta[$i];
    
            if ($alternar) {
                $digito *= 2;
                if ($digito > 9) {
                    $digito -= 9;
                }
            }
    
            $suma += $digito;
            $alternar = !$alternar;
        }
    
        // Si la suma es múltiplo de 10, el número es válido
        return $suma % 10 === 0;
    }    
}
