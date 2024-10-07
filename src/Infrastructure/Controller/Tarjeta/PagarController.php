<?php 

namespace Src\Infrastructure\Controller\Tarjeta;

use DateTimeImmutable;
use PDOException;
use Src\Domain\Model\Pago;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Model\Tarjeta;
use Src\Domain\Repository\RepositoryInterface;
use Src\Infrastructure\Controller\AbstractController;
use Src\Infrastructure\Http\Request;
use Src\Application\Validator\Request\TarjetaValidator;
use Src\Infrastructure\Persistence\Repository\AbstractRepository;
use Src\Infrastructure\Persistence\Repository\TarjetaRepository;

class PagarController extends AbstractController
{
    public function __invoke(Request $request, RepositoryInterface $pagoRepository, RepositoryInterface $tarjetaRepository): string
    {
        $tarjeta = $tarjetaRepository->getByNumero($request->tarjeta);

        if (!$tarjeta) {
            return $this->error('No se ha encontrado la tarjeta.', 400);
        }

        $pago = $this->handleRequest($request, $tarjeta);
        try {
            $result = $pagoRepository->insertOrUpdate($pago);
            if (!$result) {
                return $this->error('No se ha podido insertado el pago.', 400);
            }

            return $this->json('Se ha insertado correctamente el pago');

        } catch (PDOException $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    protected function handleRequest(Request $request, Tarjeta $tarjeta): ModelInterface
    {
        $data = $request->getBody();

        $id = $data['id'] ?? null;
        $monto = $data['monto'] ?? null;

       //var_dump($tarjeta);

        return new Pago($id, $tarjeta->getId(), $monto, new DateTimeImmutable());
    }
}