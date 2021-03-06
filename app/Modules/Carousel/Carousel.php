<?php

namespace App\Modules\Carousel;

use Sharenjoy\Cmsharenjoy\Core\EloquentBaseModel;

class Carousel extends EloquentBaseModel
{
    protected $table = 'carousels';

    protected $fillable = [
        'user_id',
        'status_id',
        'title',
        'img',
        'video',
        'link',
        'type',
        'content',
        'sort'
    ];

    protected $eventItem = [
        'creating'    => ['user_id', 'sort'],
        'created'     => [],
        'updating'    => ['user_id'],
        'saving'      => ['selected_type'],
        'saved'       => [],
        'deleted'     => [],
    ];

    public $filterFormConfig = [
        'status'      => ['order'=>'10', 'option'=>'status', 'pleaseSelect'=>true],
        'keyword'     => ['order'=>'20', 'filter' => 'carousels.title,carousels.description'],
    ];

    public $formConfig = [
        'title'        => ['order' => '10'],
        'content'      => ['order' => '15', 'type' => 'textarea', 'args'=>['rows'=>'4']],
        'img'          => ['order' => '20', 'size' =>'1580x860'],
        'type'         => ['order' => '25', 'type' => 'radio', 'option' => 'carousel_type', 'args' => ['@click'=>'changeCarouselType']],
        'link'         => ['order' => '30', 'outer-div' => ['class'=>'form-group', ':class'=>'{"animated fadeIn": animationStyle}', 'v-show'=>'imageShow']],
        'video'        => ['order' => '40', 'outer-div' => ['class'=>'form-group', ':class'=>'{"animated fadeIn": animationStyle}', 'v-show'=>'videoShow'], 'type' => 'youtube'],
        'status_id'    => ['order' => '60', 'type' =>'radio', 'option'=>'status', 'value'=>'1'],
    ];

    public function eventSelectedType($key, $model)
    {
        if ($model) {
            if ($model->type == 'image') {
                $model->video = null;
            } elseif ($model->type == 'video') {
                $model->img = null;
                $model->link = null;
            }
        }
    }

    public function grabCarouseltypeLists()
    {
        return trans_options('options.carousel_type');
    }

}