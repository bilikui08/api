<?php

namespace Src\Infrastructure\Controller;

use Src\Infrastructure\Http\Request;

class StatusController extends AbstractController
{
    public function __invoke(Request $request): string
    {
        return $this->json(['status' => 'ok']);
    }
}
