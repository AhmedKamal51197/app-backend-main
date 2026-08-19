<?php

namespace App\ValueObjects\Invoices;

use App\Models\User;
use InvalidArgumentException;

/**
 * A value-object class to define the invoice user parties
 */
final class InvoiceUsers
{
    /**
     * Class constructor
     */
    public function __construct(private readonly User $user, private readonly User $client)
    {
        if ($user === $client) {
            throw new InvalidArgumentException(__('User and client cannot be the same.'));
        }
    }

    /**
     * Get the user owner party
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the client user party
     */
    public function getClient(): User
    {
        return $this->client;
    }
}
