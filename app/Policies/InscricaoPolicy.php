<?php

namespace App\Policies;

use App\Models\Inscricao;
use App\Models\User;
use App\Models\InscricaoPessoa; // Add this import if you need to check InscricaoPessoa
use Illuminate\Auth\Access\Response;

class InscricaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        // If the user is an admin (web guard)
        if ($user instanceof User) {
            return true;
        }

        // If the user is an applicant (candidato guard)
        if ($user instanceof InscricaoPessoa) {
            return true; // Adjust based on the applicant's permissions
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Inscricao $inscricao): bool
    {
        if ($user instanceof User) {
            return true; // Admin can view any inscricao
        }

        if ($user instanceof InscricaoPessoa) {
            // Only allow viewing if the applicant is related to the Inscricao
            return $user->idpessoa === $inscricao->idinscricao_pessoa;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        if ($user instanceof User) {
            return false; // Admin cannot create any Inscricao
        }

        if ($user instanceof InscricaoPessoa) {
            return true; // Applicants can create their own inscrição (or adjust accordingly)
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Inscricao $inscricao): bool
    {
        if ($user instanceof User) {
            return true; // Admin can update Inscricao
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Inscricao $inscricao): bool
    {
        return false;
      
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Inscricao $inscricao): bool
    {
        if ($user instanceof User) {
            return true; // Admin can restore any Inscricao
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Inscricao $inscricao): bool
    {
        if ($user instanceof User) {
            return true; // Admin can permanently delete any Inscricao
        }

        return false;
    }
}
