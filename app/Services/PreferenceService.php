<?php
namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\PreferenceServiceInterface as InterfacesPreferenceServiceInterface;

class PreferenceService implements InterfacesPreferenceServiceInterface
{


    public function __construct(
    ) {
       
    }

    public function getUserPreferences($userId)
    {
        $user = User::findOrFail($userId);
        return [
            'sources' => $user->preferredSources,
            'categories' => $user->preferredCategories,
            'authors' => $user->preferredAuthors,
        ];
    }

    public function setUserPreferences($userId, array $preferences)
    {
        $user = User::findOrFail($userId);

        if (isset($preferences['sources'])) {
            $user->preferredSources()->sync($preferences['sources']);
        }

        if (isset($preferences['categories'])) {
            $user->preferredCategories()->sync($preferences['categories']);
        }

        if (isset($preferences['authors'])) {
            $user->preferredAuthors()->sync($preferences['authors']);
        }

        return $this->getUserPreferences($userId);
    }
}
