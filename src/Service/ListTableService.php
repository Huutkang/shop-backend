<?php
// src/Service/ListTableService.php

namespace App\Service;

use App\Entity\ListTable;
use App\Repository\ListTableRepository;

class ListTableService
{
    private ListTableRepository $repository;

    public function __construct(ListTableRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createOrUpdate(string $tableName, ?string $description): ListTable
    {
        $listTable = $this->repository->findByTableName($tableName) ?? new ListTable();
        $listTable->setTableName($tableName);
        $listTable->setDescription($description);

        $this->repository->save($listTable);

        return $listTable;
    }

    public function delete(string $tableName): void
    {
        $listTable = $this->repository->findByTableName($tableName);

        if ($listTable) {
            $this->repository->delete($listTable);
        }
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }
}
