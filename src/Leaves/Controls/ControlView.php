<?php

namespace Rhubarb\Leaf\Leaves\Controls;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Leaf\Views\View;

abstract class ControlView extends View
{
    /**
     * @var ControlModel
     */
    protected $model;

    protected function parseRequest(WebRequest $request)
    {
        $path = $this->model->leafPath;

        // By default if a control can be represented by a single HTML element then the name of that element
        // should equal the leaf path of the control. If that is true then we can automatically discover and
        // update our model.

        $value = $request->post($path);
        if ($value !== null){
            $this->model->setValue($value);
        }

        // Now we search for indexed data. We can't unfortunately guess what the possible indexes are so we
        // have to use a regular expression to find and extract any indexes. Note that it's not possible to
        // have both un-indexed and indexed versions of the same leaf on the parent. In that case the indexed
        // version will create an array of model data in place of the single un-indexed value.
        $postData = $request->postData;

        foreach($postData as $key => $value){
            if (preg_match("/".$this->model->leafPath."\(([^)]+)\)$/", $key, $match)){
                $this->setControlViewWithIndex($match[1], $value);
            }
        }
    }

    private function setControlViewWithIndex($index, $value)
    {
        $this->model->value = $value;
        $this->model->valueChangedEvent->raise($index);
    }
}