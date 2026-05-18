<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    public static function create(
        int $userId,
        int|null $orgId,
        string $title,
        string $message,
        string $type = 'info',
        array $data = [],
    ): AppNotification {
        return AppNotification::create([
            'user_id' => $userId,
            'organization_id' => $orgId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public static function notifyUser(int $userId, string $title, string $message, string $type = 'info', array $data = []): void
    {
        $user = User::find($userId);
        if (!$user) return;

        static::create($userId, $user->organization_id, $title, $message, $type, $data);
    }

    public static function notifyOrgAdmins(int $orgId, string $title, string $message, string $type = 'info', array $data = []): void
    {
        User::where('organization_id', $orgId)
            ->whereIn('role', ['company_admin', 'super_admin'])
            ->get()
            ->each(fn (User $user) => static::create($user->id, $orgId, $title, $message, $type, $data));
    }

    public static function notifyOrg(int $orgId, string $title, string $message, string $type = 'info', array $data = []): void
    {
        User::where('organization_id', $orgId)
            ->get()
            ->each(fn (User $user) => static::create($user->id, $orgId, $title, $message, $type, $data));
    }

    public static function workflowActivated(int $userId, int $orgId, string $workflowName): void
    {
        static::create($userId, $orgId, 'Workflow Activated', "Workflow \"{$workflowName}\" is now running.", 'success', ['type' => 'workflow']);
    }

    public static function workflowPaused(int $userId, int $orgId, string $workflowName): void
    {
        static::create($userId, $orgId, 'Workflow Paused', "Workflow \"{$workflowName}\" has been paused.", 'info', ['type' => 'workflow']);
    }

    public static function automationToggled(int $userId, int $orgId, string $name, bool $isActive): void
    {
        $action = $isActive ? 'enabled' : 'disabled';
        $type = $isActive ? 'success' : 'info';
        static::create($userId, $orgId, 'Automation ' . ucfirst($action), "Automation \"{$name}\" has been {$action}.", $type, ['type' => 'automation']);
    }

    public static function candidateAnalyzed(int $userId, int $orgId, string $candidateName, int $score): void
    {
        $level = $score >= 80 ? 'Strong' : ($score >= 60 ? 'Good' : 'Below threshold');
        static::create($userId, $orgId, 'CV Analysis Complete', "{$candidateName}: {$level} match (score: {$score}/100)", 'info', ['type' => 'recruitment']);
    }

    public static function articlePublished(int $userId, int $orgId, string $title): void
    {
        static::notifyOrg($orgId, 'New Article Published', "\"{$title}\" has been added to the Knowledge Base.", 'info', ['type' => 'knowledge']);
    }
}
