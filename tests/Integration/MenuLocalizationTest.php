<?php

namespace Speso\Ussd\Tests\Integration;

use Illuminate\Support\Facades\Lang;
use Speso\Ussd\Menu;
use Speso\Ussd\Tests\TestCase;

final class MenuLocalizationTest extends TestCase
{
    public function test_menu_can_translate_text()
    {
        Lang::addLines(['greeting.hello' => 'Bonjour'], 'fr');

        $this->assertEquals('Bonjour', Menu::build()->trans('greeting.hello', locale: 'fr'));
    }

    public function test_menu_can_translate_text_with_replacements()
    {
        Lang::addLines(['greeting.hello_name' => 'Hello :name'], 'en');

        $this->assertEquals('Hello Isaac', Menu::build()->trans('greeting.hello_name', ['name' => 'Isaac']));
    }

    public function test_menu_can_translate_a_line()
    {
        Lang::addLines(['greeting.bye' => 'Bye'], 'en');

        $this->assertEquals("Bye\n", Menu::build()->transLine('greeting.bye'));
    }

    public function test_menu_can_translate_a_pluralized_choice()
    {
        Lang::addLines(['item.count' => '{0} No items|[1,*] :count items'], 'en');

        $this->assertEquals('No items', Menu::build()->transChoice('item.count', 0, ['count' => 0]));
        $this->assertEquals('5 items', Menu::build()->transChoice('item.count', 5, ['count' => 5]));
    }
}
