<?php 

namespace Src\Infrastructure\Controller\Tarjeta;

use DateTimeImmutable;
use PDOException;
use Src\Domain\Model\Tarjeta;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Repository\RepositoryInterface;
use Src\Infrastructure\Controller\AbstractController;
use Src\Infrastructure\Http\Request;
use Src\Application\Validator\Request\TarjetaValidator;

class CreateController extends AbstractController
{
    public function __invoke(Request $request, RepositoryInterface $tarjetaRepository): string
    {
        $validator = new TarjetaValidator();

        if (!$validator->isValid($request)) {
            $jsonResponse['errors'] = $validator->getErrors();
            return $this->error($jsonResponse, 400);
        }

        $tarjeta = $this->handleRequest($request);
        try {
            $result = $tarjetaRepository->insertOrUpdate($tarjeta);
            if (!$result) {
                return $this->error('No se ha podido insertado la tarjeta.', 400);
            }

            return $this->json('Se ha insertado correctamente la tarjeta');

        } catch (PDOException $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    protected function handleRequest(Request $request): ModelInterface
    {
        $data = $request->getBody();

        $id = $data['id'] ?? null;
        $dni = $data['dni'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $nombreEntidadBancaria = $data['nombre_entidad_bancaria'] ?? null;
        $numero = $data['numero'] ?? null;
        $limite = $data['limite'] ?? null;

        return new Tarjeta($id, $dni, $nombre, $apellido, $nombreEntidadBancaria, $numero, $limite, new DateTimeImmutable());
    }
}