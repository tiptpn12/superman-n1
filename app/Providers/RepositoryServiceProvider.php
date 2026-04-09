<?php

namespace App\Providers;

use App\Repositories\BagianRepository;
use App\Repositories\FlowDetailRepository;
use App\Repositories\GlRepository;
use App\Repositories\Interfaces\BagianRepositoryInterface;
use App\Repositories\Interfaces\FlowDetailRepositoryInterface;
use App\Repositories\Interfaces\GlRepositoryInterface;
use App\Repositories\Interfaces\NamaKaryawanRepositoryInterface;
use App\Repositories\Interfaces\ProsesRepositoryInterface;
use App\Repositories\Interfaces\SppbBayarRepositoryInterface;
use App\Repositories\Interfaces\SppbBuktiKasRepositoryInterface;
use App\Repositories\Interfaces\SppdRepositoryInterface;
use App\Repositories\Interfaces\SppnBuktiKasRepositoryInterface;
use App\Repositories\Interfaces\SppnTerimaRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\VendorRepositoryInterface;
use App\Repositories\NamaKaryawanRepository;
use App\Repositories\ProsesRepository;
use App\Repositories\SppbBayarRepository;
use App\Repositories\SppbBuktiKasRepository;
use App\Repositories\SppdRepository;
use App\Repositories\SppnBuktiKasRepository;
use App\Repositories\SppnTerimaRepository;
use App\Repositories\UserRepository;
use App\Repositories\VendorRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            FlowDetailRepositoryInterface::class,
            FlowDetailRepository::class
        );

        $this->app->bind(
            SppdRepositoryInterface::class,
            SppdRepository::class
        );

        $this->app->bind(
            VendorRepositoryInterface::class,
            VendorRepository::class
        );

        $this->app->bind(
            BagianRepositoryInterface::class,
            BagianRepository::class
        );

        $this->app->bind(
            GlRepositoryInterface::class,
            GlRepository::class
        );

        $this->app->bind(
            SppnBuktiKasRepositoryInterface::class,
            SppnBuktiKasRepository::class
        );

        $this->app->bind(
            SppbBuktiKasRepositoryInterface::class,
            SppbBuktiKasRepository::class
        );

        $this->app->bind(
            SppbBayarRepositoryInterface::class,
            SppbBayarRepository::class
        );

        $this->app->bind(
            SppnTerimaRepositoryInterface::class,
            SppnTerimaRepository::class
        );

        $this->app->bind(
            NamaKaryawanRepositoryInterface::class,
            NamaKaryawanRepository::class
        );

        $this->app->bind(
            ProsesRepositoryInterface::class,
            ProsesRepository::class
        );
    }
}
