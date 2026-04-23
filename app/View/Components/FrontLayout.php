<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Component;

class FrontLayout extends Component
{
    public $title;
    public $classC;
    public $dirNav;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = null,$classC = null,$dirNav = null)
    {
        if($dirNav == null){
            $dirNav = Cookie::get('dirNav') ?? 'vertical';
        }
        $this->title = $title ?? config('app.name');
        $this->classC = $classC ;
        $this->dirNav = $dirNav;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // if($this->dirNav == 'vertical'){
        //     return view('layouts.front-layout',['template' => 'vertical-menu-template-starter']);
        // }
        return view('layouts.front-layout-horizantal',['template' => 'horizontal-menu-template']);
    }
}
