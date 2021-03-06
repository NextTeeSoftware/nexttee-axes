<?php

namespace App\Providers;

use App\Modules\Tag\Tag;
use App\Modules\Post\Post;
use App\Modules\Product\Product;
use App\Modules\Tag\TagValidator;
use App\Modules\Carousel\Carousel;
use App\Modules\Category\Category;
use App\Modules\Tag\TagRepository;
use App\Modules\Post\PostValidator;
use App\Modules\Post\PostRepository;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use App\Modules\Product\ProductValidator;
use App\Modules\Product\ProductRepository;
use App\Modules\Carousel\CarouselValidator;
use App\Modules\Category\CategoryValidator;
use App\Modules\Carousel\CarouselRepository;
use App\Modules\Category\CategoryRepository;
use Sharenjoy\Cmsharenjoy\Service\Categorize\Categorize;
use Sharenjoy\Cmsharenjoy\Service\Categorize\Categories\Provider as CategoryProvider;
use Sharenjoy\Cmsharenjoy\Service\Categorize\CategoryRelates\Provider as CategoryRelateProvider;
use Sharenjoy\Cmsharenjoy\Service\Categorize\CategoryHierarchy\Provider as CategoryHierarchyProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // The Post Binding
        $this->app->bind('App\Modules\Post\PostInterface', function()
        {
            return new PostRepository(new Post, new PostValidator);
        });

        // The Tag Binding
        $this->app->bind('App\Modules\Tag\TagInterface', function()
        {
            return new TagRepository(new Tag, new TagValidator);
        });

        // The Category Binding
        $this->app->bind('App\Modules\Category\CategoryInterface', function()
        {
            return new CategoryRepository(new Category, new CategoryValidator);
        });

        // The Carousel Binding
        $this->app->bind('App\Modules\Carousel\CarouselInterface', function()
        {
            return new CarouselRepository(new Carousel, new CarouselValidator);
        });

        // The Product Binding
        $this->app->bind('App\Modules\Product\ProductInterface', function()
        {
            return new ProductRepository(new Product, new ProductValidator);
        });

        $this->registerCategoryProvider();
        $this->registerCategoryRelateProvider();
        $this->registerCategoryHierarchyProvider();

        $this->app['categorize'] = $this->app->share(function($app)
        {
            return new Categorize(
                $app['config'],
                $app['categorize.category'],
                $app['categorize.categoryRelate'],
                $app['categorize.categoryHierarchy']
            );
        });
    }

    /**
     * Register category provider.
     *
     * @return \CategoryProvider
     */
    protected function registerCategoryProvider()
    {
        $this->app['categorize.category'] = $this->app->share(function($app)
        {
            $model = $app['config']->get('categorize.categories.model');

            return new CategoryProvider($model);
        });
    }

    /**
     * Register category hierarchy provider.
     *
     * @return \CategoryHierarchyProvider
     */
    protected function registerCategoryHierarchyProvider()
    {
        $this->app['categorize.categoryHierarchy'] = $this->app->share(function($app)
        {
            $model = $app['config']->get('categorize.categoryHierarchy.model');

            return new CategoryHierarchyProvider($model);
        });
    }

    /**
     * Register category relate provider.
     *
     * @return \CategoryHierarchyProvider
     */
    protected function registerCategoryRelateProvider()
    {
        $this->app['categorize.categoryRelate'] = $this->app->share(function($app)
        {
            $model = $app['config']->get('categorize.categoryRelates.model');

            return new CategoryRelateProvider($model);
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Auto create app alias with boot method.
        AliasLoader::getInstance()->alias('Categorize', 'Sharenjoy\Cmsharenjoy\Service\Categorize\Facades\Categorize');
    }

}