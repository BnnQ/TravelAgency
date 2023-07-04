<?php

use Exceptions\DatabaseConnectionException;
use FiveTwo\DependencyInjection\Container;
use Pages\AdminPanel;
use Pages\Comments;
use Pages\Login;
use Pages\Logout;
use Pages\Register;
use Pages\Tours;
use Services\ICityRepository;
use Services\ICountryRepository;
use Services\IHotelRepository;
use Services\IImageRepository;
use Services\IRoleManager;
use Services\ITokenGenerator;
use Services\MySqlCityRepository;
use Services\MySqlCountryRepository;
use Services\MySqlHotelRepository;
use Services\MySqlImageRepository;
use Services\MySqlRoleManager;
use Services\MySqlUserManager;
use Services\PseudoRandomTokenGenerator;
use Services\UserManagerBase;

class DependencyContainer
{
    private static ?Container $container = null;

    public static function getContainer(): Container {
        if (self::$container == null) {
            self::$container = new Container();

            self::$container
                ->addSingletonClass(Startup::class)
                ->addTransientClass(Outlet::class)
                ->addTransientClass(Tours::class)
                ->addTransientClass(Comments::class)
                ->addTransientClass(Login::class)
                ->addTransientClass(Register::class)
                ->addTransientClass(Logout::class)
                ->addTransientClass(AdminPanel::class)
                ->addTransientClass(GetCities::class)
                ->addSingletonImplementation(UserManagerBase::class, MySqlUserManager::class)
                ->addSingletonClass(MySqlUserManager::class)
                ->addTransientImplementation(ITokenGenerator::class, PseudoRandomTokenGenerator::class)
                ->addTransientClass(PseudoRandomTokenGenerator::class)
                ->addSingletonImplementation(IRoleManager::class, MySqlRoleManager::class)
                ->addSingletonClass(MySqlRoleManager::class)
                ->addSingletonImplementation(ICountryRepository::class, MySqlCountryRepository::class)
                ->addSingletonClass(MySqlCountryRepository::class)
                ->addSingletonImplementation(ICityRepository::class, MySqlCityRepository::class)
                ->addSingletonClass(MySqlCityRepository::class)
                ->addSingletonImplementation(IHotelRepository::class, MySqlHotelRepository::class)
                ->addSingletonClass(MySqlHotelRepository::class)
                ->addSingletonImplementation(IImageRepository::class, MySqlImageRepository::class)
                ->addSingletonClass(MySqlImageRepository::class)
                ->addSingletonFactory(mysqli::class, function () {
                    $config = parse_ini_file('appsettings.ini', true)['mysql_database'];
                    $databaseName = $config['name'];

                    $context = new mysqli(hostname: $config['host'], username: $config['username'], password: $config['password'], database: $databaseName);
                    if ($context->connect_errno)
                        throw new DatabaseConnectionException($databaseName, $context->connect_errno);

                    return $context;
                });

        }

        return self::$container;
    }

    public function __destruct()
    {
        $context = self::$container->get(mysqli::class);
        $context?->close();
    }

}