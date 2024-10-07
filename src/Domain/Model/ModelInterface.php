<?php

namespace Src\Domain\Model;

interface ModelInterface
{
    public const KEY_RETURN_TO_ARRAY = 'results';
    public function getId(): ?int;
    public function setId(int $id): self;
    public function toReturnArray(): array;
    public function toArray(): array;
}
