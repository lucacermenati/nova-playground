<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Nova\Fields\Text;
use LucaCermenati\NovaSettings\Models\SettingGroup;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettingsAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_view_and_save_setting_values(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->getJson('/nova-vendor/nova-settings/fields')
            ->assertOk();

        $this->actingAs($admin)
            ->postJson('/nova-vendor/nova-settings/fields', [
                'site_name' => 'Changed by admin',
                'items_per_page' => 10,
                'mail_from' => 'hello@example.com',
            ])
            ->assertNoContent();

        $this->assertSame('Changed by admin', setting('site_name'));
    }

    #[Test]
    public function super_admin_can_view_and_save_setting_values(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->getJson('/nova-vendor/nova-settings/fields')
            ->assertOk();
    }

    #[Test]
    public function admin_cannot_manage_setting_groups_or_settings(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->getJson('/nova-api/setting-groups')->assertForbidden();
        $this->actingAs($admin)->getJson('/nova-api/settings')->assertForbidden();

        $this->actingAs($admin)
            ->postJson('/nova-api/setting-groups', ['key' => 'shop', 'label' => 'Shop'])
            ->assertForbidden();

        $this->assertDatabaseMissing('setting_groups', ['key' => 'shop']);
    }

    #[Test]
    public function super_admin_can_manage_setting_groups_and_settings(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->getJson('/nova-api/setting-groups')->assertOk();
        $this->actingAs($superAdmin)->getJson('/nova-api/settings')->assertOk();

        $this->actingAs($superAdmin)
            ->postJson('/nova-api/setting-groups', ['key' => 'shop', 'label' => 'Shop'])
            ->assertCreated();

        $group = SettingGroup::query()->where('key', 'shop')->firstOrFail();

        $this->actingAs($superAdmin)
            ->postJson('/nova-api/settings', [
                'key' => 'shop_currency',
                'label' => 'Currency',
                'type' => Text::class,
                'group' => $group->id,
            ])
            ->assertCreated();
    }

    #[Test]
    public function admin_does_not_see_definition_menu_items(): void
    {
        $admin = User::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertStringNotContainsString('setting-groups', $this->mainMenuFor($admin));
        $this->assertStringNotContainsString('resources/settings', $this->mainMenuFor($admin));
        $this->assertStringContainsString('nova-settings', $this->mainMenuFor($admin));

        $this->assertStringContainsString('setting-groups', $this->mainMenuFor($superAdmin));
        $this->assertStringContainsString('resources/settings', $this->mainMenuFor($superAdmin));
    }

    /**
     * The sidebar menu Nova embeds in the page for the given user.
     */
    private function mainMenuFor(User $user): string
    {
        $page = $this->actingAs($user)->followingRedirects()->get('/nova')->assertOk()->getContent();

        preg_match('/"mainMenu":(\[.*?\]),"userMenu"/s', $page, $matches);

        return stripslashes($matches[1] ?? '');
    }
}
