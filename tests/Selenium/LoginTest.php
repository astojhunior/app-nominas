<?php

namespace Tests\Selenium;

use Tests\TestCase;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    protected $driver;

    protected function setUp(): void
    {
        parent::setUp();

        // Conectar con Selenium Server
        $this->driver = RemoteWebDriver::create(
            'http://localhost:4444/wd/hub',
            DesiredCapabilities::chrome()
        );
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }

        parent::tearDown();
    }

    /** @test */
    public function puede_ver_la_pagina_de_login()
    {
        $this->driver->get('http://127.0.0.1:8000/admin/login');

        // Buscar el título ¡Bienvenido Nuevamente!
        $titulo = $this->driver->findElement(WebDriverBy::tagName('h2'))->getText();
        $this->assertStringContainsString('Bienvenido', $titulo);
    }

    /** @test */
    public function muestra_error_con_credenciales_invalidas()
    {
        $this->driver->get('http://127.0.0.1:8000/admin/login');

        $this->driver->findElement(WebDriverBy::name('email'))->sendKeys('fake@email.com');
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys('123456');

        // Click al botón Entrar
        $this->driver->findElement(WebDriverBy::tagName('button'))->click();

        // Capturar mensaje error (div bg-red-500)
        $error = $this->driver->findElement(WebDriverBy::className('bg-red-500'))->getText();

        $this->assertStringContainsString('Credenciales incorrectas', $error);
    }

    /** @test */
    public function puede_iniciar_sesion_correctamente()
    {
        // Crear administrador en la BD
        $admin = Admin::create([
            'nombre' => 'Juan Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('123456'),
        ]);

        $this->driver->get('http://127.0.0.1:8000/admin/login');

        $this->driver->findElement(WebDriverBy::name('email'))->sendKeys('admin@test.com');
        $this->driver->findElement(WebDriverBy::name('password'))->sendKeys('123456');

        $this->driver->findElement(WebDriverBy::tagName('button'))->click();

        // Verificar redirección al dashboard
        $urlFinal = $this->driver->getCurrentURL();
        $this->assertStringContainsString('/admin/dashboard', $urlFinal);
    }
}
