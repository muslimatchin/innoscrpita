<?php
namespace App\Services\Interfaces;

interface PreferenceServiceInterface
{
    public function getUserPreferences($userId);
    public function setUserPreferences($userId, array $preferences);
}
