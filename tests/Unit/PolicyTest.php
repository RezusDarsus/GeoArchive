<?php

namespace Tests\Unit;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use App\Models\User;
use App\Policies\ArtifactPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\HistoricalEventPolicy;
use App\Policies\TagPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PolicyTest extends TestCase
{
    public static function policies(): array
    {
        return [
            [new ArtifactPolicy, new Artifact],
            [new CategoryPolicy, new Category],
            [new HistoricalEventPolicy, new HistoricalEvent],
            [new TagPolicy, new Tag],
        ];
    }

    #[DataProvider('policies')]
    public function test_archive_policies_distinguish_administrators_from_users(object $policy, object $model): void
    {
        $admin = new User(['role' => 'admin']);
        $user = new User(['role' => 'user']);

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->view($admin, $model));
        $this->assertTrue($policy->update($admin, $model));
        $this->assertTrue($policy->delete($admin, $model));
        $this->assertFalse($policy->viewAny($user));
        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->view($user, $model));
        $this->assertFalse($policy->update($user, $model));
        $this->assertFalse($policy->delete($user, $model));
    }
}
