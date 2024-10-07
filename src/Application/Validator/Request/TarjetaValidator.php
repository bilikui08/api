<?php

namespace Src\Application\Validator\Request;

use Src\Infrastructure\Http\Request;

class TarjetaValidator extends AbstractValidator
{
    protected array $validators = [
        'dni' =>
        [
            [
                'key' => self::NOT_BLANK,
                'message' => 'El dni es obligatorio.',
            ],
            [
                'key' => self::MAX_LENGTH,
                'value' => '255',
                'message' => 'El dni no puede superar los {value} caracteres.',
            ],
        ],
        'nombre' =>
        [
            [
                'key' => self::NOT_BLANK,
                'message' => 'El nombre es obligatorio.',
            ],
            [
                'key' => self::MAX_LENGTH,
                'value' => '255',
                'message' => 'El nombre no puede superar los {value} caracteres.',
            ],
        ],
        'apellido' =>
        [
            [
                'key' => self::NOT_BLANK,
                'message' => 'El apellido es obligatoria.',
            ],
            [
                'key' => self::MAX_LENGTH,
                'value' => '255',
                'message' => 'El apellido no puede superar los {value} caracteres.',
            ],
        ],
        'numero' => 
        [
            [
                'key' => self::NOT_BLANK,
                'message' => 'El numero de tarjeta es obligatorio.',
            ],
            [
                'key' => self::MAX_LENGTH,
                'value' => '16',
                'message' => 'El numero no puede superar los {value} caracteres.',
            ],
            [
                'key' => self::TARJETA,
                'message' => 'El numero de tarjeta no es valido.',
            ],
        ]
    ];

    public function isValid(Request $request): bool
    {
        $data = $request->getBody();

        $dni = $data['dni'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $nombre_entidad_bancaria = $data['nombre_entidad_bancaria'] ?? null;
        $numero = $data['numero'] ?? null;
        $limite = $data['limite'] ?? null;

        $this->validate('dni', $dni);
        $this->validate('nombre', $nombre);
        $this->validate('apellido', $apellido);
        $this->validate('nombre_entidad_bancaria', $nombre_entidad_bancaria);
        $this->validate('numero', $numero);
        $this->validate('limite', $limite);

        return empty($this->getErrors());
    }
}
