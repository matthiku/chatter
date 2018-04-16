<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    /**
     * Test a user login
     *
     * @return void
     */
    public function testLogin()
    {
        $user1 = 'testuser1@gmail.com';

        // simple login test
        $this->browse(
            function (Browser $browser) use ($user1) {
                // startup page
                $browser->visit('/')
                    ->assertSee('Chatroom');

                // login page
                $browser->visit('/login')
                    ->type('email', 'ramona12@example.com')
                    ->type('password', 'secret')
                    ->press('Login')
                    ->assertPathIs('/home');
            }
        );

        // basic chat test
        $this->browse(
            function ($first, $second) {
                $first->loginAs(\App\User::find(1))
                    ->visit('/chat')
                    ->waitFor('.chat-composer');

                $second->loginAs(\App\User::find(5))
                    ->visit('/chat')
                    ->waitFor('.chat-composer')
                    ->type('#message', 'Hey Taylor')
                    ->press('Send');

                $first->waitForText('Hey Taylor')
                    ->assertSee('Mittie Spencer');
            }
        );
    }
}
