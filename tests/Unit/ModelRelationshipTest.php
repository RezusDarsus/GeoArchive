<?php

namespace Tests\Unit;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    public function test_all_course_relationship_types_are_declared(): void
    {
        $this->assertInstanceOf(HasOne::class, (new User)->profile());
        $this->assertInstanceOf(HasMany::class, (new User)->artifacts());
        $this->assertInstanceOf(BelongsTo::class, (new Profile)->user());
        $this->assertInstanceOf(BelongsTo::class, (new Artifact)->user());
        $this->assertInstanceOf(BelongsTo::class, (new Artifact)->category());
        $this->assertInstanceOf(HasMany::class, (new Category)->artifacts());
        $this->assertInstanceOf(BelongsToMany::class, (new Artifact)->tags());
        $this->assertInstanceOf(BelongsToMany::class, (new Tag)->artifacts());
        $this->assertInstanceOf(BelongsToMany::class, (new Artifact)->historicalEvents());
        $this->assertInstanceOf(BelongsToMany::class, (new HistoricalEvent)->artifacts());
    }
}
