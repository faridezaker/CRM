<?php

namespace App\Repositories\Interfaces;

interface LeadRepositoryInterface {
    public function index(int $userId);

    public function findContact(int $contact_id);

    public function store(array $data);

    public function updateStatus(int $contact_id, string $status);

    public function allActive();
}
