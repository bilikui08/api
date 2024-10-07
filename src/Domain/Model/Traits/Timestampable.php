<?php

namespace Src\Domain\Model\Traits;

use DateTimeImmutable;
use DateTimeInterface;

trait Timestampable
{
    private DateTimeInterface $createdAt;

    private ?DateTimeInterface $updatedAt = null;

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): DateTimeInterface|null
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
